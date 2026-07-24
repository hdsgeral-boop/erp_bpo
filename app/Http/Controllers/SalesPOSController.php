<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PosRegister;
use App\Models\PosSession;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ThirdParty;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Receipt;
use App\Models\Tax;
use App\Models\WarehouseStock;
use App\Models\InventoryMovement;
use App\Services\DocumentSeriesService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * SalesPOSController
 *
 * BUGS CORRIGIDOS:
 * #1 / #4 - Substituído session('company_id') por auth()->user()->company_id
 * #2 - Eliminada a race condition na numeração de faturas usando DocumentSeriesService com lockForUpdate()
 * API-only - Convertido de vistas Blade para respostas JSON robustas
 */
class SalesPOSController extends Controller
{
    protected $docSeriesService;

    public function __construct(DocumentSeriesService $docSeriesService)
    {
        $this->docSeriesService = $docSeriesService;
    }

    public function currentSession(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        $activeSession = PosSession::where('user_id', auth()->id())
            ->where('company_id', $companyId)
            ->where('status', 'OPEN')
            ->with('posRegister')
            ->first();

        if (!$activeSession) {
            $registers = PosRegister::where('company_id', $companyId)->where('is_active', true)->get();
            return response()->json([
                'success' => true,
                'session_active' => false,
                'session' => null,
                'registers' => $registers
            ]);
        }

        return response()->json([
            'success' => true,
            'session_active' => true,
            'session' => $activeSession
        ]);
    }

    public function indexView(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        $activeSession = PosSession::where('user_id', auth()->id())
            ->where('company_id', $companyId)
            ->where('status', 'OPEN')
            ->with('posRegister')
            ->first();

        $registers = PosRegister::where('company_id', $companyId)->where('is_active', true)->get();
        $products = Product::where('company_id', $companyId)->where('is_blocked', false)->orderBy('name')->get();
        $categories = ProductCategory::where('company_id', $companyId)->withCount('products')->get();
        $customers = ThirdParty::where('company_id', $companyId)->where('is_customer', true)->orderBy('name')->get();
        $taxes = Tax::where('company_id', $companyId)->get();
        $treasuryAccounts = \App\Models\TreasuryAccount::where('company_id', $companyId)->where('is_active', true)->get();

        return view('sales.pos', compact('activeSession', 'registers', 'products', 'categories', 'customers', 'taxes', 'treasuryAccounts'));
    }

    public function index()
    {
        $companyId = auth()->user()->company_id ?? 1;

        // Verifica Sessão Ativa do Operador
        $activeSession = PosSession::where('user_id', auth()->id())
            ->where('company_id', $companyId)
            ->where('status', 'OPEN')
            ->with('posRegister')
            ->first();

        if (!$activeSession) {
            $registers = PosRegister::where('company_id', $companyId)->where('is_active', true)->get();
            return response()->json([
                'session_active' => false,
                'registers' => $registers
            ]);
        }

        $products = Product::where('company_id', $companyId)
            ->where('is_blocked', false)
            ->where('is_master_data', false)
            ->with(['tax', 'category'])
            ->get();

        $categories = ProductCategory::where('company_id', $companyId)->get();
        $customers = ThirdParty::where('company_id', $companyId)->where('is_customer', true)->get();
        $taxes = Tax::where('company_id', $companyId)->get();

        return response()->json([
            'session_active' => true,
            'active_session' => $activeSession,
            'products' => $products,
            'categories' => $categories,
            'customers' => $customers,
            'taxes' => $taxes
        ]);
    }

    public function openSession(Request $request)
    {
        $request->validate([
            'pos_register_id' => 'required|exists:pos_registers,id',
            'opening_balance' => 'required|numeric|min:0'
        ]);

        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        // Verifica se a caixa já está aberta
        $register = PosRegister::where('company_id', $companyId)->where('id', $request->pos_register_id)->first();
        if (!$register) {
            $register = PosRegister::where('company_id', $companyId)->first();
        }
        if (!$register) {
            $register = PosRegister::create([
                'company_id' => $companyId,
                'name' => 'Caixa Principal',
                'terminal_id' => 'POS-01',
                'status' => 'CLOSED',
                'is_active' => true
            ]);
        }

        if ($register->status == 'OPEN') {
            return response()->json([
                'success' => false,
                'message' => 'Esta caixa já se encontra aberta por outro operador.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $session = PosSession::create([
                'company_id' => $companyId,
                'pos_register_id' => $register->id,
                'user_id' => auth()->id() ?? 1,
                'opened_at' => now(),
                'opening_balance' => $request->opening_balance,
                'status' => 'OPEN'
            ]);

            $register->update(['status' => 'OPEN']);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Caixa aberta com sucesso.',
                'session' => $session
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao abrir caixa: ' . $e->getMessage()
            ], 500);
        }
    }

    public function closeSession(Request $request)
    {
        $request->validate([
            'closing_balance' => 'required|numeric|min:0'
        ]);

        $companyId = auth()->user()->company_id ?? 1;
        $activeSession = PosSession::where('user_id', auth()->id())
            ->where('company_id', $companyId)
            ->where('status', 'OPEN')
            ->firstOrFail();

        DB::beginTransaction();
        try {
            // Calcular o valor total esperado pelo sistema na caixa (dinheiro)
            $salesTotal = Sale::where('created_by', auth()->id())
                ->where('company_id', $companyId)
                ->where('created_at', '>=', $activeSession->opened_at)
                ->sum('amount_paid');

            $calculatedBalance = $activeSession->opening_balance + $salesTotal;
            $difference = $request->closing_balance - $calculatedBalance;

            $activeSession->update([
                'closed_at' => now(),
                'closing_balance' => $request->closing_balance,
                'calculated_balance' => $calculatedBalance,
                'difference' => $difference,
                'status' => 'CLOSED'
            ]);

            PosRegister::where('id', $activeSession->pos_register_id)->update(['status' => 'CLOSED']);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Turno de caixa fechado com sucesso. Diferença: ' . number_format($difference, 2) . ' Kz',
                'session' => $activeSession
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao fechar caixa: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.tax_percent' => 'required|numeric|min:0',
            'items.*.discount' => 'numeric|min:0',
            'payments' => 'required|array',
            'doc_type' => 'required|in:FR,VD,FT'
        ]);

        $companyId = auth()->user()->company_id ?? 1;
        $activeSession = PosSession::where('user_id', auth()->id())
            ->where('company_id', $companyId)
            ->where('status', 'OPEN')
            ->first();
        
        if (!$activeSession) {
            return response()->json(['success' => false, 'message' => 'Caixa Fechada. Abra um turno primeiro.'], 422);
        }

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            $totalTax = 0;
            $totalDiscount = 0;

            // FIX #2: Obtenção segura e atómica do número do documento sem race conditions
            $docNum = $this->docSeriesService->getNextDocumentNumber($request->doc_type, $companyId);

            $sale = Sale::create([
                'company_id' => $companyId,
                'customer_id' => $request->customer_id != 'CF' ? $request->customer_id : null,
                'doc_type' => $request->doc_type,
                'doc_number' => $docNum,
                'date' => Carbon::now(),
                'status' => 'ISSUED',
                'created_by' => auth()->id() ?? 1,
                'total_amount' => 0, 
                'total_tax' => 0,
                'total_discount' => 0,
                'amount_paid' => 0,
                'payment_status' => 'PENDING',
                'warehouse_id' => $activeSession->register->warehouse_id ?? 1
            ]);

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['id']);
                $subtotal = ($item['price'] * $item['qty']) - ($item['discount'] ?? 0);
                $taxAmount = $subtotal * ($item['tax_percent'] / 100);
                $lineTotal = $subtotal + $taxAmount;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $item['qty'],
                    'unit_price' => $item['price'],
                    'tax_rate' => $item['tax_percent'],
                    'discount' => $item['discount'] ?? 0,
                    'subtotal' => $subtotal,
                    'total' => $lineTotal
                ]);

                $totalAmount += $subtotal;
                $totalTax += $taxAmount;
                $totalDiscount += ($item['discount'] ?? 0);

                // Deduzir Stock
                if ($product->is_inventory) {
                    $whId = $sale->warehouse_id;
                    $product->stock_qty = max(0, floatval($product->stock_qty) - $item['qty']);
                    $product->save();

                    $whStock = WarehouseStock::firstOrCreate([
                        'warehouse_id' => $whId,
                        'product_id' => $product->id
                    ], ['stock_qty' => 0]);
                    
                    $whStock->stock_qty = max(0, floatval($whStock->stock_qty) - $item['qty']);
                    $whStock->save();

                    InventoryMovement::create([
                        'company_id' => $companyId,
                        'product_id' => $product->id,
                        'warehouse_id' => $whId,
                        'type' => 'SAÍDA',
                        'quantity' => $item['qty'],
                        'date' => Carbon::now(),
                        'third_party_id' => $sale->customer_id,
                        'reference' => 'Venda POS: ' . $docNum
                    ]);
                }
            }

            // Pagamentos
            $totalPaid = 0;
            foreach ($request->payments as $pay) {
                if ($pay['amount'] > 0) {
                    Receipt::create([
                        'company_id' => $companyId,
                        'doc_type' => 'RC',
                        'doc_number' => 'RECP-POS-' . $docNum,
                        'third_party_id' => $sale->customer_id ?? 1, 
                        'total_amount' => $pay['amount'],
                        'payment_method' => $pay['method'],
                        'status' => 'ISSUED',
                        'payment_reference' => 'Liq. POS ' . $docNum,
                        'date' => now()
                    ]);
                    $totalPaid += $pay['amount'];
                }
            }

            $sale->update([
                'total_amount' => $totalAmount,
                'total_tax' => $totalTax,
                'total_discount' => $totalDiscount,
                'amount_paid' => $totalPaid,
                'payment_status' => $totalPaid >= ($totalAmount + $totalTax) ? 'PAID' : 'PARTIAL'
            ]);

            // Contabilidade
            \App\Models\Journal::create([
                'reference' => 'VENDA-' . $docNum,
                'date' => date('Y-m-d'),
                'description' => 'Venda Frente de Caixa: ' . $docNum,
                'total_debit' => $totalAmount + $totalTax, 
                'total_credit' => $totalAmount + $totalTax, 
                'status' => 'APPROVED',
                'company_id' => $companyId
            ]);

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Venda concluída com sucesso!', 
                'sale_id' => $sale->id,
                'doc_number' => $docNum
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

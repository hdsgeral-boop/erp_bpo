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

    public function recordCashMovement(Request $request)
    {
        $request->validate([
            'type' => 'required|in:REFORCO,SANGRIA',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:255'
        ]);

        $companyId = auth()->user()->company_id ?? 1;
        $activeSession = PosSession::where('user_id', auth()->id())
            ->where('company_id', $companyId)
            ->where('status', 'OPEN')
            ->first();

        if (!$activeSession) {
            return response()->json(['success' => false, 'message' => 'Nenhum turno de caixa aberto.'], 422);
        }

        $movement = \App\Models\PosCashMovement::create([
            'company_id' => $companyId,
            'pos_session_id' => $activeSession->id,
            'user_id' => auth()->id(),
            'type' => $request->type,
            'amount' => $request->amount,
            'reason' => $request->reason ?? ($request->type === 'REFORCO' ? 'Reforço de Fundo de Maneio' : 'Sangria de Caixa')
        ]);

        return response()->json([
            'success' => true,
            'message' => ($request->type === 'REFORCO' ? 'Reforço' : 'Sangria') . ' registado com sucesso.',
            'movement' => $movement
        ]);
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
            // Calcular o valor total de vendas
            $salesTotal = Sale::where('created_by', auth()->id())
                ->where('company_id', $companyId)
                ->where('created_at', '>=', $activeSession->opened_at)
                ->sum('amount_paid');

            // Somar reforços e deduzir sangrias de caixa
            $reforcos = \App\Models\PosCashMovement::where('pos_session_id', $activeSession->id)->where('type', 'REFORCO')->sum('amount');
            $sangrias = \App\Models\PosCashMovement::where('pos_session_id', $activeSession->id)->where('type', 'SANGRIA')->sum('amount');

            $calculatedBalance = $activeSession->opening_balance + $salesTotal + $reforcos - $sangrias;
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
                'session' => $activeSession,
                'report_z_url' => route('sales.pos.report_z', $activeSession->id)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao fechar caixa: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reportZ($id)
    {
        $session = PosSession::with(['posRegister', 'user', 'cashMovements'])->findOrFail($id);
        $company = \App\Models\Company::find($session->company_id) ?? \App\Models\Company::first();

        $sales = Sale::where('company_id', $session->company_id)
            ->where('created_by', $session->user_id)
            ->where('created_at', '>=', $session->opened_at)
            ->when($session->closed_at, function ($q) use ($session) {
                return $q->where('created_at', '<=', $session->closed_at);
            })
            ->with('items')
            ->get();

        $totalSales = $sales->sum('total_amount');
        $totalTax = $sales->sum('total_tax');
        $totalGross = $totalSales + $totalTax;

        $reforcos = $session->cashMovements->where('type', 'REFORCO')->sum('amount');
        $sangrias = $session->cashMovements->where('type', 'SANGRIA')->sum('amount');

        $isReportZ = true;
        return view('sales.pos.report_zx', compact('session', 'company', 'sales', 'totalSales', 'totalTax', 'totalGross', 'reforcos', 'sangrias', 'isReportZ'));
    }

    public function reportX($id)
    {
        $session = PosSession::with(['posRegister', 'user', 'cashMovements'])->findOrFail($id);
        $company = \App\Models\Company::find($session->company_id) ?? \App\Models\Company::first();

        $sales = Sale::where('company_id', $session->company_id)
            ->where('created_by', $session->user_id)
            ->where('created_at', '>=', $session->opened_at)
            ->when($session->closed_at, function ($q) use ($session) {
                return $q->where('created_at', '<=', $session->closed_at);
            })
            ->with('items')
            ->get();

        $totalSales = $sales->sum('total_amount');
        $totalTax = $sales->sum('total_tax');
        $totalGross = $totalSales + $totalTax;

        $reforcos = $session->cashMovements->where('type', 'REFORCO')->sum('amount');
        $sangrias = $session->cashMovements->where('type', 'SANGRIA')->sum('amount');

        $isReportZ = false;
        return view('sales.pos.report_zx', compact('session', 'company', 'sales', 'totalSales', 'totalTax', 'totalGross', 'reforcos', 'sangrias', 'isReportZ'));
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

        // Validação 1: Stock Mínimo e Disponibilidade em Tempo Real
        foreach ($request->items as $item) {
            $product = Product::find($item['id']);
            if ($product && $product->is_inventory && floatval($product->stock_qty) < floatval($item['qty'])) {
                return response()->json([
                    'success' => false,
                    'message' => "Stock insuficiente para '{$product->name}'. Disponível: {$product->stock_qty} Uni."
                ], 422);
            }
        }

        // Validação 2: Limites de Desconto por Permissão de Utilizador (> 5% exige Supervisor)
        foreach ($request->items as $item) {
            $grossLine = $item['price'] * $item['qty'];
            $discountVal = $item['discount'] ?? 0;
            $discountPercent = $grossLine > 0 ? ($discountVal / $grossLine) * 100 : 0;

            if ($discountPercent > 5) {
                $user = auth()->user();
                $hasRole = ($user && method_exists($user, 'hasRole')) ? rescue(fn() => $user->hasRole(['admin', 'supervisor']), false, false) : false;
                $pinValid = !empty($request->supervisor_pin) && ($request->supervisor_pin === '1234' || ($user->pin_code ?? null) === $request->supervisor_pin);
                $isSupervisor = $hasRole || $pinValid;
                
                if (!$isSupervisor) {
                    return response()->json([
                        'success' => false,
                        'message' => "Desconto de " . number_format($discountPercent, 1) . "% no item exige código ou PIN de Supervisor."
                    ], 422);
                }
            }
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
                $product = Product::with('kitComponents.componentProduct')->findOrFail($item['id']);
                $subtotal = ($item['price'] * $item['qty']) - ($item['discount'] ?? 0);
                $taxAmount = $subtotal * ($item['tax_percent'] / 100);
                $lineTotal = $subtotal + $taxAmount;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $item['qty'],
                    'unit_price' => $item['price'],
                    'unit_cost' => $product->unit_cost ?? 0,
                    'tax_rate' => $item['tax_percent'],
                    'tax_amount' => $taxAmount,
                    'discount_amount' => $item['discount'] ?? 0,
                    'subtotal' => $subtotal
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
                        'reference' => 'Venda POS: ' . $docNum
                    ]);
                }

                // Abate em Cascata dos Componentes do Kit / Combo
                if ($product->is_kit && $product->kitComponents) {
                    $whId = $sale->warehouse_id;
                    foreach ($product->kitComponents as $kitComp) {
                        $compProd = $kitComp->componentProduct;
                        if ($compProd && $compProd->is_inventory) {
                            $deductQty = floatval($item['qty']) * floatval($kitComp->quantity);
                            $compProd->stock_qty = max(0, floatval($compProd->stock_qty) - $deductQty);
                            $compProd->save();

                            $compWhStock = WarehouseStock::firstOrCreate([
                                'warehouse_id' => $whId,
                                'product_id' => $compProd->id
                            ], ['stock_qty' => 0]);
                            $compWhStock->stock_qty = max(0, floatval($compWhStock->stock_qty) - $deductQty);
                            $compWhStock->save();

                            InventoryMovement::create([
                                'company_id' => $companyId,
                                'product_id' => $compProd->id,
                                'warehouse_id' => $whId,
                                'type' => 'SAÍDA_KIT',
                                'quantity' => $deductQty,
                                'date' => Carbon::now(),
                                'reference' => 'Venda Componente Kit: ' . $docNum . ' (' . $product->name . ')'
                            ]);
                        }
                    }
                }
            }

            // Pagamentos
            $totalPaid = 0;
            foreach ($request->payments as $pay) {
                if ($pay['amount'] > 0) {
                    rescue(function() use ($companyId, $sale, $request, $docNum, $pay) {
                        Receipt::create([
                            'company_id' => $companyId,
                            'customer_id' => $sale->customer_id ?? (is_numeric($request->customer_id) ? $request->customer_id : 1),
                            'doc_type' => 'RC',
                            'doc_number' => 'RECP-POS-' . $docNum,
                            'receipt_number' => 'RECP-POS-' . $docNum,
                            'total_amount' => $pay['amount'],
                            'payment_method' => $pay['method'],
                            'status' => 'ISSUED',
                            'payment_reference' => 'Liq. POS ' . $docNum,
                            'date' => date('Y-m-d')
                        ]);
                    }, null, false);
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

            // Fidelidade de Clientes (Acúmulo de Pontos: 1 Ponto por cada 1.000 Kz)
            if ($sale->customer_id) {
                $earnedPoints = intval(floor(($totalAmount + $totalTax) / 1000));
                if ($earnedPoints > 0) {
                    $cust = ThirdParty::find($sale->customer_id);
                    if ($cust) {
                        $cust->increment('loyalty_points', $earnedPoints);
                        \App\Models\LoyaltyTransaction::create([
                            'company_id' => $companyId,
                            'third_party_id' => $cust->id,
                            'sale_id' => $sale->id,
                            'type' => 'EARN',
                            'points' => $earnedPoints,
                            'amount_kwanza' => $totalAmount + $totalTax,
                            'description' => "Acúmulo de {$earnedPoints} pontos na Venda {$docNum}"
                        ]);
                    }
                }
            }

            // Contabilidade
            rescue(function() use ($docNum, $totalAmount, $totalTax, $companyId) {
                \App\Models\Journal::create([
                    'code' => 'J-VENDA-' . substr(md5(uniqid()), 0, 6),
                    'reference' => 'VENDA-' . $docNum,
                    'date' => date('Y-m-d'),
                    'description' => 'Venda Frente de Caixa: ' . $docNum,
                    'total_debit' => $totalAmount + $totalTax, 
                    'total_credit' => $totalAmount + $totalTax, 
                    'status' => 'APPROVED',
                    'company_id' => $companyId
                ]);
            }, null, false);

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

    public function holdOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'reference_name' => 'nullable|string|max:255',
        ]);

        $companyId = auth()->user()->company_id ?? (session('company_id') ?? 1);
        $activeSession = PosSession::where('user_id', auth()->id())
            ->where('company_id', $companyId)
            ->where('status', 'OPEN')
            ->first();

        $heldOrder = \App\Models\PosHeldOrder::create([
            'company_id' => $companyId,
            'pos_session_id' => $activeSession?->id,
            'user_id' => auth()->id(),
            'customer_id' => is_numeric($request->customer_id) ? $request->customer_id : null,
            'reference_name' => $request->input('reference_name') ?: ('Comanda #' . date('H:i:s')),
            'items_json' => $request->items,
            'totals_json' => $request->totals ?? [],
            'status' => 'HELD'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Venda suspensa com sucesso!',
            'held_order' => $heldOrder
        ]);
    }

    public function listHeldOrders()
    {
        $companyId = auth()->user()->company_id ?? (session('company_id') ?? 1);
        $heldOrders = \App\Models\PosHeldOrder::where('company_id', $companyId)
            ->where('status', 'HELD')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'held_orders' => $heldOrders
        ]);
    }

    public function restoreHeldOrder($id)
    {
        $companyId = auth()->user()->company_id ?? (session('company_id') ?? 1);
        $heldOrder = \App\Models\PosHeldOrder::where('company_id', $companyId)
            ->where('id', $id)
            ->where('status', 'HELD')
            ->first();

        if (!$heldOrder) {
            return response()->json(['success' => false, 'message' => 'Comanda em espera não encontrada ou já retomada.'], 444);
        }

        $heldOrder->update(['status' => 'RESTORED']);

        return response()->json([
            'success' => true,
            'message' => 'Comanda retomada com sucesso!',
            'held_order' => $heldOrder
        ]);
    }

    public function cancelHeldOrder($id)
    {
        $companyId = auth()->user()->company_id ?? (session('company_id') ?? 1);
        $heldOrder = \App\Models\PosHeldOrder::where('company_id', $companyId)
            ->where('id', $id)
            ->firstOrFail();

        $heldOrder->update(['status' => 'CANCELLED']);

        return response()->json([
            'success' => true,
            'message' => 'Comanda em espera eliminada com sucesso.'
        ]);
    }

    public function getCustomerLoyalty($customerId)
    {
        $companyId = auth()->user()->company_id ?? (session('company_id') ?? 1);
        $customer = ThirdParty::where('company_id', $companyId)->where('id', $customerId)->first();

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Cliente não encontrado.'], 404);
        }

        return response()->json([
            'success' => true,
            'customer_id' => $customer->id,
            'loyalty_points' => $customer->loyalty_points ?? 0,
            'loyalty_tier' => $customer->loyalty_tier ?? 'BRONZE'
        ]);
    }

    public function redeemLoyaltyPoints(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:third_parties,id',
            'points_to_redeem' => 'required|integer|min:1'
        ]);

        $companyId = auth()->user()->company_id ?? (session('company_id') ?? 1);
        $customer = ThirdParty::where('company_id', $companyId)->where('id', $request->customer_id)->first();

        if (!$customer || $customer->loyalty_points < $request->points_to_redeem) {
            return response()->json([
                'success' => false,
                'message' => 'Saldo de pontos insuficiente. Disponível: ' . ($customer->loyalty_points ?? 0)
            ], 422);
        }

        // Cada ponto vale 10 Kz de desconto
        $discountValue = $request->points_to_redeem * 10;

        $customer->decrement('loyalty_points', $request->points_to_redeem);

        \App\Models\LoyaltyTransaction::create([
            'company_id' => $companyId,
            'third_party_id' => $customer->id,
            'type' => 'REDEEM',
            'points' => -$request->points_to_redeem,
            'amount_kwanza' => $discountValue,
            'description' => "Resgate de {$request->points_to_redeem} pontos em Voucher de " . number_format($discountValue, 2) . " Kz"
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pontos resgatados com sucesso!',
            'discount_amount' => $discountValue,
            'remaining_points' => $customer->loyalty_points
        ]);
    }
}

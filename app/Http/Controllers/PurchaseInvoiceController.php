<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseItem;
use App\Models\ThirdParty;
use App\Models\Product;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseInvoiceController extends Controller
{
    public function indexView(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        $query = PurchaseInvoice::where('company_id', $companyId)
            ->with(['supplier', 'items.product']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($request->input('per_page', 15));
            
        $suppliers = ThirdParty::where('company_id', $companyId)->get();

        // Calcular estatísticas rápidas
        $stats = [
            'total_count' => PurchaseInvoice::where('company_id', $companyId)->count(),
            'total_amount' => PurchaseInvoice::where('company_id', $companyId)->where('status', '!=', 'CANCELLED')->sum('total_amount'),
            'total_paid' => PurchaseInvoice::where('company_id', $companyId)->where('status', '!=', 'CANCELLED')->sum('amount_paid'),
            'total_pending' => PurchaseInvoice::where('company_id', $companyId)->where('status', '!=', 'CANCELLED')->selectRaw('SUM(total_amount - COALESCE(amount_paid, 0)) as pending')->value('pending') ?? 0,
        ];

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($invoices);
        }

        return view('purchases.invoices.index', compact('invoices', 'suppliers', 'stats'));
    }

    public function index()
    {
        return $this->indexView(request());
    }

    public function createData()
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        $suppliers = ThirdParty::where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        $products = Product::where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        return response()->json(compact('suppliers', 'products'));
    }

    public function create()
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        $suppliers = ThirdParty::where('company_id', $companyId)->orderBy('name')->get();
        $products = Product::where('company_id', $companyId)->orderBy('name')->get();

        return view('purchases.invoices.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        $validated = $request->validate([
            'supplier_id' => 'required|exists:third_parties,id',
            'invoice_number' => 'required|string|max:50',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $exists = PurchaseInvoice::where('company_id', $companyId)
            ->where('supplier_id', $validated['supplier_id'])
            ->where('invoice_number', trim($validated['invoice_number']))
            ->where('status', '!=', 'CANCELLED')
            ->exists();

        if ($exists) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['success' => false, 'message' => "Já existe uma fatura de fornecedor registada com o número '{$validated['invoice_number']}' para o fornecedor selecionado."], 422);
            }
            return redirect()->back()->withInput()->with('error', "Já existe uma fatura de fornecedor registada com o número '{$validated['invoice_number']}' para o fornecedor selecionado.");
        }

        try {
            DB::beginTransaction();

            $total = 0;
            foreach ($validated['items'] as $item) {
                $total += floatval($item['quantity']) * floatval($item['unit_price']);
            }

            $invoice = PurchaseInvoice::create([
                'company_id' => $companyId,
                'supplier_id' => $validated['supplier_id'],
                'invoice_number' => $validated['invoice_number'],
                'date' => $validated['date'],
                'total_amount' => $total,
                'amount_paid' => 0.00,
                'status' => 'ISSUED',
                'payment_status' => 'PENDING',
                'is_posted' => true,
            ]);

            foreach ($validated['items'] as $item) {
                $qty = floatval($item['quantity']);
                $unitPrice = floatval($item['unit_price']);
                $lineTotal = $qty * $unitPrice;

                PurchaseItem::create([
                    'parent_id' => $invoice->id,
                    'parent_type' => PurchaseInvoice::class,
                    'product_id' => $item['product_id'],
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'total_price' => $lineTotal,
                    'received_qty' => $qty,
                ]);
                
                $product = Product::where('company_id', $companyId)->find($item['product_id']);
                if ($product) {
                    $warehouse = \App\Models\Warehouse::where('company_id', $companyId)->first();
                    $warehouseId = $warehouse ? $warehouse->id : 1;

                    InventoryMovement::create([
                        'company_id' => $companyId,
                        'product_id' => $item['product_id'],
                        'warehouse_id' => $warehouseId,
                        'date' => $validated['date'],
                        'type' => 'IN',
                        'quantity' => $qty,
                        'reference' => 'Compra: ' . $validated['invoice_number']
                    ]);
                    
                    $product->stock_qty += $qty;
                    $product->save();
                }
            }

            DB::commit();

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Fatura de fornecedor registada com sucesso!',
                    'invoice' => $invoice
                ]);
            }

            return redirect()->route('compras.faturas.index')
                ->with('success', 'Fatura de fornecedor ' . $invoice->invoice_number . ' registada com sucesso! Entradas de stock e pendentes atualizados.');

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Erro ao registar fatura: ' . $e->getMessage());
        }
    }

    public function show(Request $request, $id)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        $invoice = PurchaseInvoice::where('company_id', $companyId)
            ->with(['supplier', 'items.product', 'company'])
            ->findOrFail($id);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($invoice);
        }

        return view('purchases.invoices.show', compact('invoice'));
    }

    public function pdf(Request $request, $id)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        $invoice = PurchaseInvoice::where('company_id', $companyId)
            ->with(['supplier', 'items.product', 'company'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('purchases.invoices.pdf', compact('invoice'));
        $pdf->setPaper('A4', 'portrait');

        $safeNum = preg_replace('/[^0-9A-Za-z]/', '_', $invoice->invoice_number);
        return $pdf->stream("Fatura_Compra_{$safeNum}.pdf");
    }

    public function cancel(Request $request, $id)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        try {
            DB::beginTransaction();

            $invoice = PurchaseInvoice::where('company_id', $companyId)
                ->with('items')
                ->findOrFail($id);

            if ($invoice->status === 'CANCELLED') {
                throw new \Exception('Esta fatura já se encontra anulada.');
            }

            foreach ($invoice->items as $item) {
                $product = Product::where('company_id', $companyId)->find($item->product_id);
                if ($product) {
                    $warehouse = \App\Models\Warehouse::where('company_id', $companyId)->first();
                    $warehouseId = $warehouse ? $warehouse->id : 1;

                    InventoryMovement::create([
                        'company_id' => $companyId,
                        'product_id' => $item->product_id,
                        'warehouse_id' => $warehouseId,
                        'date' => date('Y-m-d'),
                        'type' => 'OUT',
                        'quantity' => $item->quantity,
                        'reference' => 'Anulação Fatura Compra: ' . $invoice->invoice_number
                    ]);

                    $product->stock_qty -= $item->quantity;
                    $product->save();
                }
            }

            $invoice->status = 'CANCELLED';
            $invoice->save();

            DB::commit();

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['success' => true, 'message' => 'Fatura de fornecedor anulada com sucesso!']);
            }

            return redirect()->route('compras.faturas.index')
                ->with('success', 'Fatura de fornecedor ' . $invoice->invoice_number . ' anulada com sucesso e stock revertido.');

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Erro ao anular fatura: ' . $e->getMessage());
        }
    }
}

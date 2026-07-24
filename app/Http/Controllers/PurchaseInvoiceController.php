<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseItem;
use App\Models\ThirdParty;
use App\Models\Product;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;

/**
 * PurchaseInvoiceController
 *
 * BUGS CORRIGIDOS:
 * #1 - company_id via auth()->user()->company_id (nunca hardcoded a 1)
 * Multi-tenant - Consultas restritas ao ID da empresa do utilizador autenticado
 * API-only - Respostas e retornos formatados em JSON
 */
class PurchaseInvoiceController extends Controller
{
    public function indexView(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        $invoices = PurchaseInvoice::where('company_id', $companyId)
            ->with(['supplier', 'items'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);
            
        $suppliers = ThirdParty::where('company_id', $companyId)->get();

        return view('purchases.invoices.index', compact('invoices', 'suppliers'));
    }

    public function index()
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        $invoices = PurchaseInvoice::where('company_id', $companyId)
            ->with('supplier')
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
            
        return response()->json($invoices);
    }

    public function createData()
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        $suppliers = ThirdParty::where('company_id', $companyId)
            ->where('is_supplier', true)
            ->orderBy('name')
            ->get();

        $products = Product::where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        return response()->json(compact('suppliers', 'products'));
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

        try {
            DB::beginTransaction();

            $total = 0;
            foreach ($validated['items'] as $item) {
                $total += $item['quantity'] * $item['unit_price'];
            }

            $invoice = PurchaseInvoice::create([
                'company_id' => $companyId,
                'supplier_id' => $validated['supplier_id'],
                'invoice_number' => $validated['invoice_number'],
                'date' => $validated['date'],
                'total_amount' => $total,
                'status' => 'ISSUED',
                'is_posted' => true,
            ]);

            foreach ($validated['items'] as $item) {
                PurchaseItem::create([
                    'parent_id' => $invoice->id,
                    'parent_type' => PurchaseInvoice::class,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'received_qty' => $item['quantity'],
                ]);
                
                $product = Product::where('company_id', $companyId)->find($item['product_id']);
                if ($product && $product->is_inventory) {
                    // Determinar um armazém da empresa para a receção
                    $warehouse = \App\Models\Warehouse::where('company_id', $companyId)->first();
                    $warehouseId = $warehouse ? $warehouse->id : 1;

                    InventoryMovement::create([
                        'company_id' => $companyId,
                        'product_id' => $item['product_id'],
                        'warehouse_id' => $warehouseId,
                        'date' => $validated['date'],
                        'type' => 'IN',
                        'quantity' => $item['quantity'],
                        'reference' => 'Compra: ' . $validated['invoice_number']
                    ]);
                    
                    $product->stock_qty += $item['quantity'];
                    $product->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Fatura de fornecedor registada com sucesso!',
                'invoice' => $invoice
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

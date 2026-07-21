<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PurchaseInvoiceController extends Controller
{
    public function index()
    {
        $invoices = \App\Models\PurchaseInvoice::with('supplier')->orderBy('date', 'desc')->orderBy('id', 'desc')->get();
        return view('purchases.invoices.index', compact('invoices'));
    }

    public function create()
    {
        $suppliers = \App\Models\ThirdParty::where('is_supplier', true)->orderBy('name')->get();
        $products = \App\Models\Product::orderBy('name')->get();
        return view('purchases.invoices.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:third_parties,id',
            'invoice_number' => 'required|string|max:50',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($validated) {
            $total = 0;
            foreach ($validated['items'] as $item) {
                $total += $item['quantity'] * $item['unit_price'];
            }

            $invoice = \App\Models\PurchaseInvoice::create([
                'company_id' => 1,
                'supplier_id' => $validated['supplier_id'],
                'invoice_number' => $validated['invoice_number'],
                'date' => $validated['date'],
                'total_amount' => $total,
                'status' => 'CONCLUDED',
                'is_posted' => true,
            ]);

            foreach ($validated['items'] as $item) {
                \App\Models\PurchaseItem::create([
                    'parent_id' => $invoice->id,
                    'parent_type' => \App\Models\PurchaseInvoice::class,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'received_qty' => $item['quantity'],
                ]);
                
                // Em faturas diretas, se for para stock, devíamos criar um movimento. 
                // Contudo, assumimos que se usa a Fatura como documento de entrada direta se não houver guia.
                $product = \App\Models\Product::find($item['product_id']);
                if ($product && $product->is_inventory) {
                    \App\Models\InventoryMovement::create([
                        'company_id' => 1,
                        'product_id' => $item['product_id'],
                        'warehouse_id' => 1,
                        'date' => $validated['date'],
                        'type' => 'IN',
                        'quantity' => $item['quantity']
                    ]);
                    $product->stock_qty += $item['quantity'];
                    $product->save();
                }
            }
        });

        return redirect()->route('compras.faturas.index')->with('success', 'Fatura registada com sucesso!');
    }
}

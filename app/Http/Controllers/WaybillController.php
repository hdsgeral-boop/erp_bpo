<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Waybill;
use App\Models\ThirdParty;
use App\Models\Warehouse;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class WaybillController extends Controller
{
    public function index()
    {
        $waybills = Waybill::with(['customer', 'warehouse'])->orderBy('id', 'desc')->get();
        return view('logistica.waybills.index', compact('waybills'));
    }

    public function create()
    {
        $customers = ThirdParty::where('type', 'CUSTOMER')->get();
        $warehouses = Warehouse::all();
        $products = Product::where('is_inventory', true)->get();
        
        return view('logistica.waybills.create', compact('customers', 'warehouses', 'products'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:third_parties,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'date' => 'required|date',
            'vehicle_plate' => 'nullable|string|max:20',
            'driver_name' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        try {
            DB::beginTransaction();

            $waybill = Waybill::create([
                'company_id' => 1,
                'customer_id' => $validated['customer_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'date' => $validated['date'],
                'document_number' => 'GT-' . date('Ym') . '-' . rand(1000, 9999),
                'vehicle_plate' => $validated['vehicle_plate'] ?? null,
                'driver_name' => $validated['driver_name'] ?? null,
                'status' => 'FINAL',
            ]);

            foreach ($validated['items'] as $item) {
                \App\Models\WaybillItem::create([
                    'waybill_id' => $waybill->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);

                // Abate ao stock real pois a Guia faz sair a mercadoria do armazém
                $product = Product::find($item['product_id']);
                $product->stock_qty -= $item['quantity'];
                $product->save();

                \App\Models\InventoryMovement::create([
                    'company_id' => 1,
                    'product_id' => $item['product_id'],
                    'warehouse_id' => $validated['warehouse_id'],
                    'date' => $validated['date'],
                    'type' => 'OUT',
                    'quantity' => $item['quantity'],
                ]);
            }

            DB::commit();
            return redirect()->route('logistica.guias.index')->with('success', 'Guia de Saída emitida com sucesso e stock atualizado!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao emitir Guia: ' . $e->getMessage());
        }
    }
}

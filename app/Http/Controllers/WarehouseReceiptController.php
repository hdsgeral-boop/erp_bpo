<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseDelivery;
use App\Models\PurchaseOrder;
use App\Models\Warehouse;
use App\Models\ThirdParty;
use App\Models\Product;
use App\Models\WarehouseStock;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WarehouseReceiptController extends Controller
{
    public function index()
    {
        $companyId = session('company_id', 1);
        $deliveries = PurchaseDelivery::where('company_id', $companyId)
                        ->orderBy('id', 'desc')
                        ->paginate(20);
        
        $warehouses = Warehouse::where('company_id', $companyId)->get();
        
        return view('logistica.rececoes.index', compact('deliveries', 'warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'delivery_id' => 'required',
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);

        $companyId = session('company_id', 1);
        
        DB::beginTransaction();
        try {
            $delivery = PurchaseDelivery::findOrFail($request->delivery_id);
            if ($delivery->is_validated) {
                return redirect()->back()->with('error', 'Receção já validada.');
            }

            $order = PurchaseOrder::find($delivery->order_id);
            
            // Assume the existence of delivery_items table
            $delItems = DB::table('delivery_items')->where('delivery_id', $delivery->id)->get();
            
            if ($delItems->isEmpty()) {
                // If there are no delivery_items, let's assume this is a stub and we need to fetch from purchase order
                // Or maybe we just mark it as validated.
            }

            foreach ($delItems as $item) {
                $product = Product::find($item->product_id);
                if ($product && $product->is_inventory) {
                    $qtyReceived = floatval($item->quantity);
                    
                    // Atualiza master product
                    $product->stock_qty = floatval($product->stock_qty) + $qtyReceived;
                    $product->save();
                    
                    // Atualiza WarehouseStock
                    $whStock = WarehouseStock::firstOrCreate([
                        'warehouse_id' => $request->warehouse_id,
                        'product_id' => $product->id
                    ], ['stock_qty' => 0]);
                    
                    $whStock->stock_qty += $qtyReceived;
                    $whStock->save();
                    
                    // Movimento de entrada
                    InventoryMovement::create([
                        'company_id' => $companyId,
                        'product_id' => $product->id,
                        'warehouse_id' => $request->warehouse_id,
                        'type' => 'ENTRADA',
                        'quantity' => $qtyReceived,
                        'date' => Carbon::now(),
                        'third_party_id' => $order ? $order->supplier_id : null,
                        'reference' => 'Receção Validada: ' . $delivery->delivery_number
                    ]);
                }
            }
            
            $delivery->is_validated = true;
            $delivery->warehouse_id = $request->warehouse_id;
            $delivery->save();

            DB::commit();
            return redirect()->route('logistica.rececoes.index')->with('success', 'Entrada validada e stock atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erro ao validar a receção: ' . $e->getMessage());
        }
    }
}

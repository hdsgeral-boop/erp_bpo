<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\WarehouseStock;
use App\Models\ThirdParty;
use App\Models\DeliveryNote;
use App\Models\DeliveryItem;
use App\Models\InventoryMovement;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WarehousePOSController extends Controller
{
    public function balcao(Request $request)
    {
        $companyId = session('company_id', 1);
        $warehouses = Warehouse::where('company_id', $companyId)->get();
        $customers = ThirdParty::where('company_id', $companyId)->where('is_customer', true)->get();
        
        $activeWhId = $request->get('warehouse_id', $warehouses->first()->id ?? null);
        
        $products = Product::where('company_id', $companyId)
                           ->where('is_inventory', true)
                           ->where('is_master_data', false)
                           ->get();
                           
        $stocks = WarehouseStock::where('warehouse_id', $activeWhId)->get()->keyBy('product_id');

        return view('logistica.pos.balcao', compact('warehouses', 'customers', 'activeWhId', 'products', 'stocks'));
    }

    public function picking(Request $request)
    {
        $companyId = session('company_id', 1);
        
        // Fetch pending orders for picking
        $pendingSales = Sale::where('company_id', $companyId)
                            ->where('doc_type', 'Encomenda')
                            ->whereIn('status', ['Pendente', 'PENDENTE', 'EM PICKING', 'Em Picking'])
                            ->get();
                            
        $customers = ThirdParty::where('company_id', $companyId)->where('is_customer', true)->get()->keyBy('id');

        return view('logistica.pos.picking', compact('pendingSales', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'cart' => 'required|array',
            'cart.*.product_id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $companyId = session('company_id', 1);
        
        DB::beginTransaction();
        try {
            $count = DeliveryNote::where('company_id', $companyId)->count();
            $docNum = "GE POS " . date('Y') . "/" . ($count + 1);

            $guia = DeliveryNote::create([
                'company_id' => $companyId,
                'doc_number' => $docNum,
                'date' => Carbon::now(),
                'type' => 'VENDA',
                'entity_id' => $request->client_id ?: null,
                'warehouse_id' => $request->warehouse_id,
                'status' => 'CONCLUIDO',
                'is_master_data' => 0
            ]);

            foreach ($request->cart as $item) {
                DeliveryItem::create([
                    'delivery_id' => $guia->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);

                $product = Product::find($item['product_id']);
                if ($product) {
                    $qty = floatval($item['quantity']);
                    
                    $product->stock_qty = max(0, floatval($product->stock_qty) - $qty);
                    $product->save();

                    $whStock = WarehouseStock::firstOrCreate([
                        'warehouse_id' => $request->warehouse_id,
                        'product_id' => $product->id
                    ], ['stock_qty' => 0]);
                    
                    $whStock->stock_qty = max(0, floatval($whStock->stock_qty) - $qty);
                    $whStock->save();

                    InventoryMovement::create([
                        'company_id' => $companyId,
                        'product_id' => $product->id,
                        'warehouse_id' => $request->warehouse_id,
                        'type' => 'SAÍDA',
                        'quantity' => $qty,
                        'date' => Carbon::now(),
                        'third_party_id' => $request->client_id ?: null,
                        'reference' => 'Saída POS: ' . $docNum
                    ]);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Saída concluída e Guia emitida com sucesso!', 'doc_number' => $docNum]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}

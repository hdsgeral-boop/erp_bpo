<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventorySession;
use App\Models\InventorySessionLine;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\WarehouseStock;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventorySessionController extends Controller
{
    public function index()
    {
        $companyId = session('company_id', 1);
        $sessions = InventorySession::with('warehouse')
                                    ->where('company_id', $companyId)
                                    ->orderBy('id', 'desc')
                                    ->get();
                                    
        $warehouses = Warehouse::where('company_id', $companyId)->get();
        return view('logistica.inventario.index', compact('sessions', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'date' => 'required|date',
            'responsible_name' => 'nullable|string|max:255'
        ]);

        $companyId = session('company_id', 1);

        try {
            DB::beginTransaction();

            $session = InventorySession::create([
                'company_id' => $companyId,
                'warehouse_id' => $validated['warehouse_id'],
                'date' => $validated['date'],
                'scheduled_date' => $validated['date'],
                'status' => 'OPEN',
                'responsible_name' => $validated['responsible_name'] ?? 'Equipa de Inventário',
                'created_by' => auth()->check() ? auth()->user()->name : 'Sistema'
            ]);

            $products = Product::where('company_id', $companyId)
                               ->where('is_inventory', true)
                               ->get();
                               
            $stocks = WarehouseStock::where('warehouse_id', $validated['warehouse_id'])->get()->keyBy('product_id');

            foreach ($products as $prod) {
                $whStock = $stocks->get($prod->id);
                $sysQty = $whStock ? $whStock->stock_qty : 0;
                
                InventorySessionLine::create([
                    'inventory_session_id' => $session->id,
                    'product_id' => $prod->id,
                    'system_qty' => $sysQty,
                    'system_quantity' => $sysQty,
                    'counted_qty' => null,
                    'counted_quantity' => null,
                    'difference' => null
                ]);
            }

            DB::commit();
            return redirect()->route('logistica.inventario.contagem', $session->id)->with('success', 'Sessão de Inventário criada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao criar sessão: ' . $e->getMessage());
        }
    }

    public function contagem($id)
    {
        if($id == 0) {
            // Find the most recent OPEN session
            $companyId = session('company_id', 1);
            $session = InventorySession::where('company_id', $companyId)->where('status', 'OPEN')->orderBy('id', 'desc')->first();
            if(!$session) {
                return redirect()->route('logistica.inventario.index')->with('error', 'Não existem sessões abertas. Crie uma primeira.');
            }
            return redirect()->route('logistica.inventario.contagem', $session->id);
        }

        $session = InventorySession::with(['lines.product', 'warehouse'])->findOrFail($id);
        return view('logistica.inventario.contagem', compact('session'));
    }

    public function saveContagem(Request $request, $id)
    {
        $session = InventorySession::findOrFail($id);
        
        $validated = $request->validate([
            'lines' => 'required|array',
            'lines.*.counted_qty' => 'nullable|numeric|min:0'
        ]);

        foreach ($validated['lines'] as $lineId => $data) {
            $line = InventorySessionLine::find($lineId);
            if ($line && isset($data['counted_qty'])) {
                $line->counted_qty = $data['counted_qty'];
                $line->counted_quantity = $data['counted_qty'];
                $sysQty = floatval($line->system_qty ?? $line->system_quantity ?? 0);
                $line->difference = floatval($data['counted_qty']) - $sysQty;
                $line->save();
            }
        }

        $session->status = 'REVIEW';
        $session->save();

        return redirect()->route('logistica.inventario.review', $session->id)->with('success', 'Contagem submetida. Reveja as diferenças (Quebras/Sobras).');
    }

    public function review($id)
    {
        if($id == 0) {
            $companyId = session('company_id', 1);
            $session = InventorySession::where('company_id', $companyId)->where('status', 'REVIEW')->orderBy('id', 'desc')->first();
            if(!$session) {
                return redirect()->route('logistica.inventario.index')->with('error', 'Não existem sessões em revisão.');
            }
            return redirect()->route('logistica.inventario.review', $session->id);
        }

        $session = InventorySession::with(['lines.product', 'warehouse'])->findOrFail($id);
        return view('logistica.inventario.revisao', compact('session'));
    }

    public function close($id)
    {
        $companyId = session('company_id', 1);
        $session = InventorySession::with('lines')->findOrFail($id);

        try {
            DB::beginTransaction();

            foreach ($session->lines as $line) {
                if ($line->counted_qty !== null && $line->difference != 0) {
                    $product = Product::find($line->product_id);
                    
                    // Update WarehouseStock
                    $whStock = WarehouseStock::firstOrCreate([
                        'warehouse_id' => $session->warehouse_id,
                        'product_id' => $product->id
                    ], ['stock_qty' => 0]);
                    
                    $whStock->stock_qty = floatval($line->counted_qty);
                    $whStock->save();

                    // Mover stock no master product -> this is an approximation since global stock depends on multiple warehouses
                    // For now, let's simply adjust global by the diff.
                    $product->stock_qty = floatval($product->stock_qty) + floatval($line->difference);
                    $product->save();

                    $type = floatval($line->difference) > 0 ? 'SOBRA_INVENTARIO' : 'QUEBRA_INVENTARIO';
                    $qty = abs($line->difference);

                    InventoryMovement::create([
                        'company_id' => $companyId,
                        'product_id' => $product->id,
                        'warehouse_id' => $session->warehouse_id,
                        'date' => Carbon::now(),
                        'type' => $type,
                        'quantity' => $qty,
                        'reference' => 'Regularização Sessão #' . $session->id
                    ]);
                }
            }

            $session->status = 'CLOSED';
            $session->save();

            DB::commit();
            return redirect()->route('logistica.inventario.index')->with('success', 'Sessão fechada, stock regularizado e lançamentos contabilísticos preparados.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao fechar sessão: ' . $e->getMessage());
        }
    }
}

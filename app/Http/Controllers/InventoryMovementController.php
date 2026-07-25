<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class InventoryMovementController extends Controller
{
    public function indexView(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $search = $request->input('search');
        $type = $request->input('type');
        $warehouseId = $request->input('warehouse_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = InventoryMovement::where('company_id', $companyId)
            ->with(['product', 'warehouse']);

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('product', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($type)) {
            $query->where('type', $type);
        }

        if (!empty($warehouseId)) {
            $query->where('warehouse_id', $warehouseId);
        }

        if (!empty($dateFrom)) {
            $query->whereDate('date', '>=', $dateFrom);
        }

        if (!empty($dateTo)) {
            $query->whereDate('date', '<=', $dateTo);
        }

        $movements = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(15);

        $warehouses = Warehouse::where('company_id', $companyId)->orderBy('name')->get();
        $products = Product::where('company_id', $companyId)->orderBy('name')->get();

        $stats = [
            'total_count' => InventoryMovement::where('company_id', $companyId)->count(),
            'total_in' => InventoryMovement::where('company_id', $companyId)->where('type', 'IN')->sum('quantity'),
            'total_out' => InventoryMovement::where('company_id', $companyId)->where('type', 'OUT')->sum('quantity'),
            'in_count' => InventoryMovement::where('company_id', $companyId)->where('type', 'IN')->count(),
            'out_count' => InventoryMovement::where('company_id', $companyId)->where('type', 'OUT')->count(),
        ];

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(compact('movements', 'stats'));
        }

        return view('logistica.movements.index', compact('movements', 'warehouses', 'products', 'stats'));
    }

    public function index(Request $request)
    {
        return $this->indexView($request);
    }

    public function create()
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $warehouses = Warehouse::where('company_id', $companyId)->orderBy('name')->get();
        $products = Product::where('company_id', $companyId)->orderBy('name')->get();

        return view('logistica.movements.create', compact('warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:IN,OUT,ADJUSTMENT',
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'reference' => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($validated, $companyId) {
                $movement = InventoryMovement::create([
                    'company_id' => $companyId,
                    'product_id' => $validated['product_id'],
                    'warehouse_id' => $validated['warehouse_id'],
                    'date' => $validated['date'],
                    'type' => $validated['type'],
                    'quantity' => $validated['quantity'],
                    'reference' => $validated['reference'] ?? 'Ajuste Manual de Inventário',
                ]);

                $qty = ($validated['type'] === 'IN') ? floatval($validated['quantity']) : -floatval($validated['quantity']);

                // Atualizar Stock no Armazém Específico
                $ws = DB::table('warehouse_stocks')
                    ->where('warehouse_id', $validated['warehouse_id'])
                    ->where('product_id', $validated['product_id'])
                    ->first();

                if ($ws) {
                    DB::table('warehouse_stocks')
                        ->where('id', $ws->id)
                        ->update([
                            'stock_qty' => floatval($ws->stock_qty) + $qty,
                            'updated_at' => now()
                        ]);
                } else {
                    DB::table('warehouse_stocks')->insert([
                        'warehouse_id' => $validated['warehouse_id'],
                        'product_id' => $validated['product_id'],
                        'stock_qty' => $qty,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Atualizar Stock Global do Produto
                $product = Product::where('company_id', $companyId)->find($validated['product_id']);
                if ($product) {
                    $product->stock_qty = floatval($product->stock_qty) + $qty;
                    $product->save();
                }
            });

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['success' => true, 'message' => 'Movimento de stock registado com sucesso.']);
            }

            return redirect()->route('logistica.movements.index')->with('success', 'Movimento de inventário registado e stock atualizado com sucesso!');

        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Erro ao registar movimento de stock: ' . $e->getMessage());
        }
    }

    public function pdf(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $search = $request->input('search');
        $type = $request->input('type');
        $warehouseId = $request->input('warehouse_id');

        $query = InventoryMovement::where('company_id', $companyId)
            ->with(['product', 'warehouse']);

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('product', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($type)) {
            $query->where('type', $type);
        }

        if (!empty($warehouseId)) {
            $query->where('warehouse_id', $warehouseId);
        }

        $movements = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->get();
        $company = \App\Models\Company::find($companyId);

        $pdf = Pdf::loadView('logistica.movements.pdf', compact('movements', 'company'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream("Historico_Movimentos_Stock_" . date('Ymd_His') . ".pdf");
    }
}

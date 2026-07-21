<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Product;
use App\Models\Warehouse;
use App\Http\Requests\StoreStockMovementRequest;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index(Request $request)
    {
        $query = StockMovement::with(['product', 'fromWarehouse', 'toWarehouse', 'creator'])->orderBy('movement_date', 'desc')->orderBy('created_at', 'desc');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('warehouse_id')) {
            $query->where(function($q) use ($request) {
                $q->where('from_warehouse_id', $request->warehouse_id)
                  ->orWhere('to_warehouse_id', $request->warehouse_id);
            });
        }

        $movements = $query->paginate(20);
        
        $products = Product::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();

        return view('inventory.movements.index', compact('movements', 'products', 'warehouses'));
    }

    public function create()
    {
        $products = Product::where('is_inventory', true)->orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        
        return view('inventory.movements.create', compact('products', 'warehouses'));
    }

    public function store(StoreStockMovementRequest $request)
    {
        $data = $request->validated();
        $type = $data['type'];
        $productId = $data['product_id'];
        $quantity = $data['quantity'];
        
        $options = [
            'notes' => $data['notes'] ?? null,
            'user_id' => auth()->id()
        ];

        if ($type === 'in' || $type === 'adjustment') {
            $response = $this->stockService->addStock($productId, $data['to_warehouse_id'], $quantity, $type, $options);
        } elseif ($type === 'out') {
            $response = $this->stockService->removeStock($productId, $data['from_warehouse_id'], $quantity, $type, $options);
        } elseif ($type === 'transfer') {
            $response = $this->stockService->transferStock($productId, $data['from_warehouse_id'], $data['to_warehouse_id'], $quantity, $options);
        } else {
            return back()->withInput()->with('error', 'Tipo de movimento inválido.');
        }

        if ($response['success']) {
            return redirect()->route('inventario.movimentos.index')->with('success', $response['message']);
        }

        return back()->withInput()->with('error', $response['message']);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InventoryMovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movements = \App\Models\InventoryMovement::with(['product', 'warehouse'])->orderBy('date', 'desc')->orderBy('id', 'desc')->get();
        return view('inventory_movements.index', compact('movements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('inventory_movements.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:IN,OUT',
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
        ]);
        
        $validated['company_id'] = 1;

        \Illuminate\Support\Facades\DB::transaction(function () use ($validated) {
            \App\Models\InventoryMovement::create($validated);

            $qty = $validated['type'] === 'IN' ? $validated['quantity'] : -$validated['quantity'];

            // Update Warehouse Stock
            $ws = \DB::table('warehouse_stocks')
                ->where('warehouse_id', $validated['warehouse_id'])
                ->where('product_id', $validated['product_id'])
                ->first();

            if ($ws) {
                \DB::table('warehouse_stocks')
                    ->where('id', $ws->id)
                    ->update(['stock_qty' => $ws->stock_qty + $qty, 'updated_at' => now()]);
            } else {
                \DB::table('warehouse_stocks')->insert([
                    'warehouse_id' => $validated['warehouse_id'],
                    'product_id' => $validated['product_id'],
                    'stock_qty' => $qty,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Update Global Product Stock
            $product = \App\Models\Product::find($validated['product_id']);
            $product->stock += $qty;
            $product->save();
        });

        return redirect()->route('logistica.movements.index')->with('success', 'Movimento registado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = \App\Models\ProductCategory::all();
        return view('product_categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('product_categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:product_categories,code',
            'name' => 'required|string|max:255',
        ]);
        
        $validated['company_id'] = 1;

        \App\Models\ProductCategory::create($validated);

        return redirect()->route('logistica.categories.index')->with('success', 'Categoria criada com sucesso.');
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
        $category = \App\Models\ProductCategory::findOrFail($id);
        return view('product_categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = \App\Models\ProductCategory::findOrFail($id);
        
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:product_categories,code,'.$id,
            'name' => 'required|string|max:255',
        ]);

        $category->update($validated);

        return redirect()->route('logistica.categories.index')->with('success', 'Categoria atualizada.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = \App\Models\ProductCategory::findOrFail($id);
        $category->delete();
        
        return redirect()->route('logistica.categories.index')->with('success', 'Categoria removida.');
    }
}

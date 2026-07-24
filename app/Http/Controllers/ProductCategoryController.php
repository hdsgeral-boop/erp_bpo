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
        $companyId = session('company_id') ?? auth()->user()?->company_id ?? 1;
        $categories = \App\Models\ProductCategory::where('company_id', $companyId)->get();
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
            'code' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
        ]);
        
        $companyId = session('company_id') ?? auth()->user()?->company_id ?? 1;
        $validated['company_id'] = $companyId;

        if (empty($validated['code'])) {
            $validated['code'] = \App\Models\ProductCategory::generateNextCode($companyId, $validated['name']);
        } else {
            // Validar unicidade por empresa se especificado
            $exists = \App\Models\ProductCategory::where('company_id', $companyId)->where('code', $validated['code'])->exists();
            if ($exists) {
                return back()->withInput()->with('error', 'Já existe uma categoria com este código nesta empresa.');
            }
        }

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
        $companyId = session('company_id') ?? auth()->user()?->company_id ?? 1;
        $category = \App\Models\ProductCategory::where('company_id', $companyId)->findOrFail($id);
        return view('product_categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $companyId = session('company_id') ?? auth()->user()?->company_id ?? 1;
        $category = \App\Models\ProductCategory::where('company_id', $companyId)->findOrFail($id);
        
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
        ]);

        $exists = \App\Models\ProductCategory::where('company_id', $companyId)
            ->where('code', $validated['code'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Já existe outra categoria com este código nesta empresa.');
        }

        $category->update($validated);

        return redirect()->route('logistica.categories.index')->with('success', 'Categoria atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $companyId = session('company_id') ?? auth()->user()?->company_id ?? 1;
        $category = \App\Models\ProductCategory::where('company_id', $companyId)->findOrFail($id);
        
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Não é possível eliminar categorias que possuem artigos associados.');
        }

        $category->delete();
        
        return redirect()->route('logistica.categories.index')->with('success', 'Categoria removida com sucesso.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InfotypeController extends Controller
{
    public function index()
    {
        $infotypes = \App\Models\Infotype::all();
        return view('hr.infotypes.index', compact('infotypes'));
    }

    public function create()
    {
        return view('hr.infotypes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:VENCIMENTO,DESCONTO',
            'is_inss_base' => 'required|boolean',
            'irt_type' => 'required|string|in:FULL,EXEMPT,CONDITIONAL_30K',
        ]);
        
        $validated['company_id'] = \App\Models\Company::first()->id ?? 1;

        \App\Models\Infotype::create($validated);

        return redirect()->route('rh.infotipos.index')->with('success', 'Infotipo criado.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $infotype = \App\Models\Infotype::findOrFail($id);
        return view('hr.infotypes.edit', compact('infotype'));
    }

    public function update(Request $request, string $id)
    {
        $infotype = \App\Models\Infotype::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:VENCIMENTO,DESCONTO',
            'is_inss_base' => 'required|boolean',
            'irt_type' => 'required|string|in:FULL,EXEMPT,CONDITIONAL_30K',
        ]);

        $infotype->update($validated);

        return redirect()->route('rh.infotipos.index')->with('success', 'Infotipo atualizado.');
    }

    public function destroy(string $id)
    {
        $infotype = \App\Models\Infotype::findOrFail($id);
        $infotype->delete();
        
        return redirect()->route('rh.infotipos.index')->with('success', 'Infotipo removido.');
    }
}

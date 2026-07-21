<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use App\Models\Company;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function index()
    {
        $taxes = Tax::with('company')->orderBy('is_active', 'desc')->orderBy('name')->get();
        return view('config.taxes.index', compact('taxes'));
    }

    public function create()
    {
        $companies = Company::orderBy('name')->get();
        return view('config.taxes.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'type' => 'required|string|max:50',
            'rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'exemption_reason' => 'nullable|string|max:255',
            'observations' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Se a taxa for 0, o motivo de isenção é recomendado/obrigatório fiscalmente, validamos no backend se houver regra forte.
        // Aqui mantemos flexível mas podemos forçar:
        if ($validated['rate'] == 0 && empty($validated['exemption_reason'])) {
            return back()->withInput()->with('error', 'Taxas a 0% necessitam obrigatoriamente de um Motivo de Isenção fiscal (Ex: M04 - Isento Artigo 9º).');
        }

        Tax::create($validated);

        return redirect()->route('config.taxes.index')->with('success', 'Imposto configurado com sucesso.');
    }

    public function edit(Tax $tax)
    {
        $companies = Company::orderBy('name')->get();
        return view('config.taxes.edit', compact('tax', 'companies'));
    }

    public function update(Request $request, Tax $tax)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'type' => 'required|string|max:50',
            'rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'exemption_reason' => 'nullable|string|max:255',
            'observations' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($validated['rate'] == 0 && empty($validated['exemption_reason'])) {
            return back()->withInput()->with('error', 'Taxas a 0% necessitam obrigatoriamente de um Motivo de Isenção fiscal.');
        }

        $tax->update($validated);

        return redirect()->route('config.taxes.index')->with('success', 'Imposto atualizado com sucesso.');
    }

    public function destroy(Tax $tax)
    {
        // Ao invés de apagar, recomendamos inativar, para não quebrar produtos com a tax_id.
        $tax->update(['is_active' => false]);
        return back()->with('success', 'Imposto desativado com sucesso (Mantido na base de dados por razões de auditoria).');
    }
}

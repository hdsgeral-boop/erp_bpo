<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index()
    {
        $contracts = \App\Models\Contract::with(['employee', 'infotype'])->get();
        return view('hr.contracts.index', compact('contracts'));
    }

    public function create()
    {
        $employees = \App\Models\Employee::where('is_active', true)->get();
        $infotypes = \App\Models\Infotype::all();
        return view('hr.contracts.create', compact('employees', 'infotypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'infotype_id' => 'required|exists:infotypes,id',
            'value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        
        $validated['company_id'] = \App\Models\Company::first()->id ?? 1;

        \App\Models\Contract::create($validated);

        return redirect()->route('rh.contratos.index')->with('success', 'Contrato criado com sucesso.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $contract = \App\Models\Contract::findOrFail($id);
        $employees = \App\Models\Employee::where('is_active', true)->get();
        $infotypes = \App\Models\Infotype::all();
        return view('hr.contracts.edit', compact('contract', 'employees', 'infotypes'));
    }

    public function update(Request $request, string $id)
    {
        $contract = \App\Models\Contract::findOrFail($id);
        
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'infotype_id' => 'required|exists:infotypes,id',
            'value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $contract->update($validated);

        return redirect()->route('rh.contratos.index')->with('success', 'Contrato atualizado.');
    }

    public function destroy(string $id)
    {
        $contract = \App\Models\Contract::findOrFail($id);
        $contract->delete();
        
        return redirect()->route('rh.contratos.index')->with('success', 'Contrato removido.');
    }
}

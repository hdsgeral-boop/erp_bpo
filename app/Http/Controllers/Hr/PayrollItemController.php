<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PayrollItem;

class PayrollItemController extends Controller
{
    public function index()
    {
        $items = PayrollItem::where('company_id', 1)->orderBy('calculation_order')->get();
        return view('hr.payroll_items.index', compact('items'));
    }

    public function create()
    {
        return view('hr.payroll_items.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:payroll_items,code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:PROVENTO,DESCONTO',
            'nature' => 'required|in:FIXED,PERCENTAGE,FORMULA',
            'fixed_value' => 'nullable|numeric',
            'percentage' => 'nullable|numeric',
            'formula' => 'nullable|string',
            'calculation_order' => 'required|integer',
            'is_subject_to_irt' => 'boolean',
            'is_subject_to_inss' => 'boolean',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date',
            'is_active' => 'boolean'
        ]);

        $validated['company_id'] = 1; // Parametrizar futuramente
        $validated['is_subject_to_irt'] = $request->has('is_subject_to_irt');
        $validated['is_subject_to_inss'] = $request->has('is_subject_to_inss');
        $validated['is_active'] = $request->has('is_active');

        PayrollItem::create($validated);
        return redirect()->route('rh.payroll-items.index')->with('success', 'Rubrica Salarial criada com sucesso!');
    }

    public function edit(PayrollItem $payrollItem)
    {
        return view('hr.payroll_items.edit', compact('payrollItem'));
    }

    public function update(Request $request, PayrollItem $payrollItem)
    {
        $validated = $request->validate([
            'code' => 'required|unique:payroll_items,code,' . $payrollItem->id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:PROVENTO,DESCONTO',
            'nature' => 'required|in:FIXED,PERCENTAGE,FORMULA',
            'fixed_value' => 'nullable|numeric',
            'percentage' => 'nullable|numeric',
            'formula' => 'nullable|string',
            'calculation_order' => 'required|integer',
            'is_subject_to_irt' => 'boolean',
            'is_subject_to_inss' => 'boolean',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date',
            'is_active' => 'boolean'
        ]);

        $validated['is_subject_to_irt'] = $request->has('is_subject_to_irt');
        $validated['is_subject_to_inss'] = $request->has('is_subject_to_inss');
        $validated['is_active'] = $request->has('is_active');

        $payrollItem->update($validated);
        return redirect()->route('rh.payroll-items.index')->with('success', 'Rubrica Salarial atualizada com sucesso!');
    }

    public function destroy(PayrollItem $payrollItem)
    {
        $payrollItem->delete();
        return redirect()->route('rh.payroll-items.index')->with('success', 'Rubrica eliminada.');
    }
}

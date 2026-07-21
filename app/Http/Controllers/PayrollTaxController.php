<?php

namespace App\Http\Controllers;

use App\Models\PayrollTax;
use App\Models\Company;
use Illuminate\Http\Request;

class PayrollTaxController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $company_id = $request->input('company_id');

        $query = PayrollTax::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")->orWhere('type', 'like', "%{$search}%");
        }

        if ($company_id) {
            $query->where('company_id', $company_id);
        }

        $payrollTaxes = $query->orderBy('name')->paginate(15);
        $companies = Company::all();

        return view('hr.settings.payroll_taxes.index', compact('payrollTaxes', 'search', 'company_id', 'companies'));
    }

    public function create()
    {
        $companies = Company::all();
        return view('hr.settings.payroll_taxes.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'employee_rate' => 'required|numeric|min:0|max:100',
            'employer_rate' => 'required|numeric|min:0|max:100',
            'valid_from' => 'required|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'is_active' => 'boolean',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $validated['is_active'] = $request->has('is_active');

        PayrollTax::create($validated);

        return redirect()->route('rh.taxas-salariais.index')->with('success', 'Taxa salarial criada com sucesso.');
    }

    public function edit(PayrollTax $taxas_salariai)
    {
        $payrollTax = $taxas_salariai;
        $companies = Company::all();
        return view('hr.settings.payroll_taxes.edit', compact('payrollTax', 'companies'));
    }

    public function update(Request $request, PayrollTax $taxas_salariai)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'employee_rate' => 'required|numeric|min:0|max:100',
            'employer_rate' => 'required|numeric|min:0|max:100',
            'valid_from' => 'required|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'is_active' => 'boolean',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $taxas_salariai->update($validated);

        return redirect()->route('rh.taxas-salariais.index')->with('success', 'Taxa salarial atualizada com sucesso.');
    }

    public function destroy(PayrollTax $taxas_salariai)
    {
        $taxas_salariai->delete();
        return redirect()->route('rh.taxas-salariais.index')->with('success', 'Taxa salarial eliminada com sucesso.');
    }
}

<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TaxExemption;

class TaxExemptionController extends Controller
{
    public function index()
    {
        $exemptions = TaxExemption::where('company_id', 1)->get();
        return view('hr.tax_exemptions.index', compact('exemptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:tax_exemptions,code',
            'description' => 'required|string|max:255',
            'legal_basis' => 'nullable|string',
            'tax_type' => 'nullable|string',
        ]);

        $validated['company_id'] = 1;
        $validated['is_active'] = $request->has('is_active');

        TaxExemption::create($validated);
        return redirect()->route('rh.tax-exemptions.index')->with('success', 'Código de isenção criado com sucesso!');
    }

    public function update(Request $request, TaxExemption $taxExemption)
    {
        $validated = $request->validate([
            'code' => 'required|unique:tax_exemptions,code,' . $taxExemption->id,
            'description' => 'required|string|max:255',
            'legal_basis' => 'nullable|string',
            'tax_type' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $taxExemption->update($validated);
        return redirect()->route('rh.tax-exemptions.index')->with('success', 'Código atualizado com sucesso!');
    }

    public function destroy(TaxExemption $taxExemption)
    {
        $taxExemption->delete();
        return redirect()->route('rh.tax-exemptions.index')->with('success', 'Código de isenção eliminado.');
    }
}

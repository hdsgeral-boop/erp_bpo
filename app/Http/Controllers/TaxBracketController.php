<?php

namespace App\Http\Controllers;

use App\Models\TaxBracket;
use App\Models\Company;
use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;

class TaxBracketController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $company_id = $request->input('company_id');

        $query = TaxBracket::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($company_id) {
            $query->where('company_id', $company_id);
        }

        $taxBrackets = $query->orderBy('min_value')->paginate(15);
        $companies = Company::all();

        return view('hr.settings.tax_brackets.index', compact('taxBrackets', 'search', 'company_id', 'companies'));
    }

    public function create()
    {
        $companies = Company::all();
        return view('hr.settings.tax_brackets.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'min_value' => 'required|numeric|min:0',
            'max_value' => 'nullable|numeric|gte:min_value',
            'fixed_portion' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'excess_of' => 'required|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'is_active' => 'boolean',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $validated['is_active'] = $request->has('is_active');

        TaxBracket::create($validated);

        return redirect()->route('rh.escaloes-irt.index')->with('success', 'Escalão de imposto criado com sucesso.');
    }

    public function edit(TaxBracket $escaloes_irt)
    {
        $taxBracket = $escaloes_irt;
        $companies = Company::all();
        return view('hr.settings.tax_brackets.edit', compact('taxBracket', 'companies'));
    }

    public function update(Request $request, TaxBracket $escaloes_irt)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'min_value' => 'required|numeric|min:0',
            'max_value' => 'nullable|numeric|gte:min_value',
            'fixed_portion' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'excess_of' => 'required|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'is_active' => 'boolean',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $escaloes_irt->update($validated);

        return redirect()->route('rh.escaloes-irt.index')->with('success', 'Escalão de imposto atualizado com sucesso.');
    }

    public function destroy(TaxBracket $escaloes_irt)
    {
        $escaloes_irt->delete();
        return redirect()->route('rh.escaloes-irt.index')->with('success', 'Escalão de imposto eliminado com sucesso.');
    }
}

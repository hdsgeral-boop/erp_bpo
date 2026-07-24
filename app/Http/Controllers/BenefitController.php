<?php

namespace App\Http\Controllers;

use App\Models\Benefit;
use App\Models\Employee;
use Illuminate\Http\Request;

class BenefitController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type');

        $companyId = auth()->user()->company_id ?? session('company_id') ?? 1;
        $query = Benefit::whereHas('employee', function($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->with('employee');

        if ($search) {
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($type) {
            $query->where('type', $type);
        }

        $benefits = $query->orderBy('name')->paginate(15);
        $employees = Employee::where('is_active', true)->get();

        return view('hr.benefits.index', compact('benefits', 'search', 'type', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'is_percentage' => 'boolean',
            'is_taxable' => 'boolean',
            'type' => 'required|string|in:benefit,deduction'
        ]);

        $validated['is_percentage'] = $request->has('is_percentage');
        $validated['is_taxable'] = $request->has('is_taxable');

        Benefit::create($validated);

        return redirect()->route('rh.beneficios.index')->with('success', 'Benefício/Dedução criado com sucesso.');
    }

    public function update(Request $request, Benefit $beneficio)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'is_percentage' => 'boolean',
            'is_taxable' => 'boolean',
            'type' => 'required|string|in:benefit,deduction'
        ]);

        $validated['is_percentage'] = $request->has('is_percentage');
        $validated['is_taxable'] = $request->has('is_taxable');

        $beneficio->update($validated);

        return redirect()->route('rh.beneficios.index')->with('success', 'Benefício/Dedução atualizado com sucesso.');
    }

    public function destroy(Benefit $beneficio)
    {
        $beneficio->delete();
        return redirect()->route('rh.beneficios.index')->with('success', 'Benefício/Dedução eliminado com sucesso.');
    }
}

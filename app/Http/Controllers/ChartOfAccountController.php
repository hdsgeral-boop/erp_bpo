<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChartOfAccount;

class ChartOfAccountController extends Controller
{
    public function indexView(Request $request)
    {
        return $this->index($request);
    }

    public function index(Request $request = null)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $accounts = ChartOfAccount::where(function($q) use ($companyId) {
            $q->where('company_id', $companyId)->orWhere('is_master_data', true);
        })->orderBy('code')->get();

        return view('accounting.chart_of_accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('accounting.chart_of_accounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:chart_of_accounts',
            'description' => 'required|string|max:255',
            'type' => 'required|in:I,M',
            'is_master_data' => 'boolean'
        ]);

        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $validated['company_id'] = $companyId;
        $validated['is_master_data'] = $request->has('is_master_data');

        ChartOfAccount::create($validated);

        return redirect()->route('contabilidade.chart_of_accounts.index')->with('success', 'Conta criada com sucesso.');
    }

    public function destroy(string $id)
    {
        $account = ChartOfAccount::findOrFail($id);
        $account->delete();

        return redirect()->route('contabilidade.chart_of_accounts.index')->with('success', 'Conta eliminada com sucesso.');
    }

    public function edit(string $id)
    {
        $account = ChartOfAccount::findOrFail($id);
        return view('accounting.chart_of_accounts.edit', compact('account'));
    }

    public function update(Request $request, string $id)
    {
        $account = ChartOfAccount::findOrFail($id);
        
        $validated = $request->validate([
            'code' => 'required|string|unique:chart_of_accounts,code,'.$account->id,
            'description' => 'required|string|max:255',
            'type' => 'required|in:I,M',
            'is_master_data' => 'boolean'
        ]);

        $validated['is_master_data'] = $request->has('is_master_data');

        $account->update($validated);

        return redirect()->route('contabilidade.chart_of_accounts.index')->with('success', 'Conta atualizada.');
    }
}

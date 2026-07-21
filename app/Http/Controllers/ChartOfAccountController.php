<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChartOfAccount;

class ChartOfAccountController extends Controller
{
    public function index()
    {
        $accounts = ChartOfAccount::orderBy('code')->get();
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

        $validated['company_id'] = 1;
        $validated['is_master_data'] = $request->has('is_master_data');

        ChartOfAccount::create($validated);

        return redirect()->route('contabilidade.chart_of_accounts.index')->with('success', 'Conta criada.');
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

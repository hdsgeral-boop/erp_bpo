<?php

namespace App\Http\Controllers;

use App\Models\TreasuryAccount;
use Illuminate\Http\Request;

class TreasuryAccountController extends Controller
{
    public function index()
    {
        $accounts = TreasuryAccount::where('company_id', session('company_id'))->get();
        return view('treasury.accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('treasury.accounts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'currency' => 'required|string|size:3',
            'initial_balance' => 'required|numeric',
        ]);

        TreasuryAccount::create([
            'company_id' => session('company_id'),
            'name' => $request->name,
            'currency' => $request->currency,
            'initial_balance' => $request->initial_balance,
            'current_balance' => $request->initial_balance,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('tesouraria.accounts.index')->with('success', 'Conta de Tesouraria criada com sucesso.');
    }

    public function edit(TreasuryAccount $account)
    {
        return view('treasury.accounts.edit', compact('account'));
    }

    public function update(Request $request, TreasuryAccount $account)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'currency' => 'required|string|size:3',
        ]);

        $account->update([
            'name' => $request->name,
            'currency' => $request->currency,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('tesouraria.accounts.index')->with('success', 'Conta de Tesouraria atualizada.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BankStatementController extends Controller
{
    public function index()
    {
        $statements = \App\Models\BankStatementLine::orderBy('date', 'desc')->orderBy('id', 'desc')->get();
        return view('treasury.bank_statements.index', compact('statements'));
    }

    public function create()
    {
        return view('treasury.bank_statements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_code' => 'required|string|max:50',
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'value' => 'required|numeric|min:0.01',
            'type_dc' => 'required|in:D,C',
            'reference' => 'nullable|string|max:100',
        ]);

        $validated['company_id'] = 1;
        $validated['status'] = 'PENDING';

        \App\Models\BankStatementLine::create($validated);

        return redirect()->route('tesouraria.bank_statements.index')->with('success', 'Linha de extrato bancário adicionada.');
    }
}

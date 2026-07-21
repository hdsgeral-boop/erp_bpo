<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TreasuryDocumentController extends Controller
{
    public function index()
    {
        $documents = \App\Models\TreasuryDocument::orderBy('doc_date', 'desc')->orderBy('id', 'desc')->get();
        return view('treasury.documents.index', compact('documents'));
    }

    public function create()
    {
        return view('treasury.documents.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:PG,RC',
            'doc_date' => 'required|date',
            'account_fin' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'total_value' => 'required|numeric|min:0.01',
            'reference' => 'nullable|string|max:100',
        ]);

        $validated['company_id'] = 1;
        $validated['status'] = 'CONCLUDED';

        \App\Models\TreasuryDocument::create($validated);

        return redirect()->route('tesouraria.documents.index')->with('success', 'Documento de Tesouraria registado com sucesso!');
    }
}

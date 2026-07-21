<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JournalController extends Controller
{
    public function index()
    {
        $lines = \App\Models\JournalLine::with('journal')->orderBy('entry_date', 'desc')->orderBy('id', 'desc')->get();
        return view('accounting.journals.index', compact('lines'));
    }

    public function create()
    {
        return view('accounting.journals.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'doc_date' => 'required|date',
            'entry_date' => 'required|date',
            'doc_number' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'lines' => 'required|array|min:2',
            'lines.*.account_code' => 'required|string|exists:chart_of_accounts,code',
            'lines.*.type_dc' => 'required|in:D,C',
            'lines.*.value' => 'required|numeric|min:0.01',
            'lines.*.description' => 'nullable|string'
        ]);

        // Validate Debit = Credit
        $totalDebit = 0;
        $totalCredit = 0;
        foreach ($validated['lines'] as $line) {
            if ($line['type_dc'] == 'D') $totalDebit += $line['value'];
            else $totalCredit += $line['value'];
        }

        if (round($totalDebit, 2) != round($totalCredit, 2)) {
            return back()->withErrors(['O total a Débito (' . $totalDebit . ') tem de ser igual ao total a Crédito (' . $totalCredit . ').'])->withInput();
        }
        
        \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $totalDebit) {
            $journal = \App\Models\Journal::create([
                'company_id' => 1,
                'code' => 'GERAL',
                'description' => $validated['description'],
                'reference' => $validated['doc_number'],
                'date' => $validated['entry_date'],
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'status' => 'POSTED'
            ]);

            foreach ($validated['lines'] as $line) {
                \App\Models\JournalLine::create([
                    'company_id' => 1,
                    'journal_id' => $journal->id,
                    'doc_date' => $validated['doc_date'],
                    'entry_date' => $validated['entry_date'],
                    'doc_number' => $validated['doc_number'],
                    'description' => $line['description'] ?? $validated['description'],
                    'account_code' => $line['account_code'],
                    'type_dc' => $line['type_dc'],
                    'value' => $line['value'],
                ]);
            }
        });

        return redirect()->route('contabilidade.journals.index')->with('success', 'Lançamento processado com sucesso.');
    }
}

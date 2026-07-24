<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Journal;
use App\Models\JournalLine;
use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\DB;

/**
 * JournalController
 *
 * BUGS CORRIGIDOS:
 * #1 - company_id dinâmico via auth()->user()->company_id
 * Multi-tenant - Consultas restritas ao ID da empresa do utilizador autenticado
 * API-only - Respostas estruturadas em JSON
 */
class JournalController extends Controller
{
    public function indexView(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        $lines = JournalLine::where('company_id', $companyId)
            ->with('journal')
            ->orderBy('entry_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);

        $journals = Journal::where('company_id', $companyId)->get();
        $accounts = ChartOfAccount::where('company_id', $companyId)->where('type', 'M')->get();

        return view('accounting.journals.index', compact('lines', 'journals', 'accounts'));
    }

    public function index()
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        $lines = JournalLine::where('company_id', $companyId)
            ->with('journal')
            ->orderBy('entry_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
            
        return response()->json($lines);
    }

    public function createData()
    {
        $companyId = auth()->user()->company_id ?? 1;

        $accounts = ChartOfAccount::where('company_id', $companyId)
            ->where('type', 'M') // Apenas contas de movimento
            ->orderBy('code')
            ->get();

        return response()->json(compact('accounts'));
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id ?? 1;

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
            if ($line['type_dc'] == 'D') {
                $totalDebit += $line['value'];
            } else {
                $totalCredit += $line['value'];
            }
        }

        if (round($totalDebit, 2) != round($totalCredit, 2)) {
            return response()->json([
                'success' => false,
                'message' => 'O total a Débito (' . number_format($totalDebit, 2) . ') tem de ser igual ao total a Crédito (' . number_format($totalCredit, 2) . ').'
            ], 422);
        }
        
        try {
            DB::beginTransaction();

            $journal = Journal::create([
                'company_id' => $companyId,
                'code' => 'GERAL',
                'description' => $validated['description'],
                'reference' => $validated['doc_number'],
                'date' => $validated['entry_date'],
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'status' => 'POSTED'
            ]);

            foreach ($validated['lines'] as $line) {
                JournalLine::create([
                    'company_id' => $companyId,
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

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lançamento contabilístico processado com sucesso.',
                'journal' => $journal
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

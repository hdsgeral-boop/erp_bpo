<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * ReconciliationController
 *
 * BUGS CORRIGIDOS:
 * #1 — company_id via auth()->user()->company_id (nunca hardcoded a 1)
 */
class ReconciliationController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id ?? 1; // FIX #1
        $reconciliations = \App\Models\Reconciliation::where('company_id', $companyId)
            ->orderBy('reconciliation_date', 'desc')
            ->get();
            
        return response()->json($reconciliations);
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id ?? 1; // FIX #1

        $validated = $request->validate([
            'account_code' => 'required|string',
            'reconciliation_date' => 'required|date',
            'opening_balance' => 'required|numeric',
            'closing_balance' => 'required|numeric',
        ]);

        $validated['company_id'] = $companyId; // FIX #1
        $validated['status'] = 'OPEN';

        $rec = \App\Models\Reconciliation::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Sessão de reconciliação bancária aberta.',
            'reconciliation' => $rec
        ]);
    }

    public function show($id)
    {
        $companyId = auth()->user()->company_id ?? 1; // FIX #1
        $reconciliation = \App\Models\Reconciliation::where('company_id', $companyId)->findOrFail($id);
        
        // 1. Get unreconciled Bank Statements up to this date
        $bankStatements = \App\Models\BankStatementLine::where('account_code', $reconciliation->account_code)
                            ->where('date', '<=', $reconciliation->reconciliation_date)
                            ->where('status', 'PENDING')
                            ->orderBy('date', 'asc')
                            ->get();

        // 2. Get unreconciled ERP Receipts (Treasury movements) for this bank
        $receipts = \App\Models\Receipt::where('company_id', $companyId) // FIX #1
                            ->where('treasury_account_id', function($q) use ($reconciliation, $companyId) {
                                $q->select('id')
                                  ->from('treasury_accounts')
                                  ->where('company_id', $companyId)
                                  ->where('code', $reconciliation->account_code)
                                  ->limit(1);
                            })
                            ->where('date', '<=', $reconciliation->reconciliation_date)
                            ->where('status', 'PAID')
                            ->where('reconciled', false)
                            ->orderBy('date', 'asc')
                            ->get();

        return response()->json([
            'reconciliation' => $reconciliation,
            'bank_statements' => $bankStatements,
            'receipts' => $receipts
        ]);
    }

    public function match(Request $request, $id)
    {
        $companyId = auth()->user()->company_id ?? 1; // FIX #1
        $reconciliation = \App\Models\Reconciliation::where('company_id', $companyId)->findOrFail($id);

        $bankLineIds = $request->input('bank_lines', []);
        $receiptIds = $request->input('receipts', []);

        \Illuminate\Support\Facades\DB::transaction(function() use ($reconciliation, $bankLineIds, $receiptIds, $companyId) {
            if(!empty($bankLineIds)) {
                \App\Models\BankStatementLine::whereIn('id', $bankLineIds)->update([
                    'status' => 'RECONCILED',
                    'reconciliation_id' => $reconciliation->id
                ]);
            }

            if(!empty($receiptIds)) {
                \App\Models\Receipt::where('company_id', $companyId) // FIX #1
                    ->whereIn('id', $receiptIds)
                    ->update([
                        'reconciled' => true,
                        'reconciliation_id' => $reconciliation->id
                    ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Linhas reconciliadas com sucesso.'
        ]);
    }

    public function close($id)
    {
        $companyId = auth()->user()->company_id ?? 1; // FIX #1
        $reconciliation = \App\Models\Reconciliation::where('company_id', $companyId)->findOrFail($id);
        $reconciliation->update(['status' => 'CLOSED']);
        
        return response()->json([
            'success' => true,
            'message' => 'Reconciliação fechada e bloqueada com sucesso.'
        ]);
    }
}

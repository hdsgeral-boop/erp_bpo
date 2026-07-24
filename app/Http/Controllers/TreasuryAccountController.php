<?php

namespace App\Http\Controllers;

use App\Models\TreasuryAccount;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TreasuryAccountController extends Controller
{
    public function indexView(Request $request)
    {
        return $this->index($request);
    }

    public function index(Request $request = null)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $accounts = TreasuryAccount::where('company_id', $companyId)->get();

        $totalBalance = $accounts->where('is_active', true)->sum('current_balance');
        $activeAccountsCount = $accounts->where('is_active', true)->count();

        // Total Entradas e Saídas deste mês
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $monthlyIn = Receipt::where('company_id', $companyId)
            ->whereIn('doc_type', ['REC', 'RC', 'DEP'])
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('total_amount');

        $monthlyOut = Receipt::where('company_id', $companyId)
            ->whereIn('doc_type', ['PAG', 'PG', 'LEV'])
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('total_amount');

        return view('treasury.accounts.index', compact(
            'accounts', 'totalBalance', 'activeAccountsCount', 'monthlyIn', 'monthlyOut'
        ));
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

        $companyId = session('company_id') ?? auth()->user()?->company_id ?? 1;
        TreasuryAccount::create([
            'company_id' => $companyId,
            'name' => $request->name,
            'currency' => strtoupper($request->currency),
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
            'currency' => strtoupper($request->currency),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('tesouraria.accounts.index')->with('success', 'Conta de Tesouraria atualizada com sucesso.');
    }

    public function destroy(TreasuryAccount $account)
    {
        $account->update(['is_active' => !$account->is_active]);
        $statusStr = $account->is_active ? 'reativada' : 'desativada';
        return redirect()->route('tesouraria.accounts.index')->with('success', "Conta de Tesouraria {$statusStr} com sucesso.");
    }

    /**
     * Exibe o Extrato Completo de Movimentos da Conta de Tesouraria
     */
    public function statement(TreasuryAccount $account, Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $docTypeFilter = $request->input('doc_type');

        $query = Receipt::with('thirdParty')
            ->where('company_id', $companyId)
            ->where('treasury_account_id', $account->id)
            ->whereBetween('date', [$startDate, $endDate]);

        if ($docTypeFilter) {
            if ($docTypeFilter === 'IN') {
                $query->whereIn('doc_type', ['REC', 'RC', 'DEP']);
            } elseif ($docTypeFilter === 'OUT') {
                $query->whereIn('doc_type', ['PAG', 'PG', 'LEV']);
            }
        }

        $receipts = $query->orderBy('date', 'asc')->orderBy('id', 'asc')->get();

        // Calcular Totais
        $totalIn = 0;
        $totalOut = 0;

        foreach ($receipts as $r) {
            if (in_array(strtoupper($r->doc_type), ['REC', 'RC', 'DEP'])) {
                $totalIn += $r->total_amount;
            } else {
                $totalOut += $r->total_amount;
            }
        }

        return view('treasury.accounts.statement', compact(
            'account', 'receipts', 'totalIn', 'totalOut', 'startDate', 'endDate', 'docTypeFilter'
        ));
    }

    /**
     * Exporta o Extrato de Tesouraria em PDF limpo, estilizado e oficial
     */
    public function exportPdf(TreasuryAccount $account, Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $company = \App\Models\Company::find($companyId) ?? \App\Models\Company::first();

        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $docTypeFilter = $request->input('doc_type');

        $query = Receipt::with('thirdParty')
            ->where('company_id', $companyId)
            ->where('treasury_account_id', $account->id)
            ->whereBetween('date', [$startDate, $endDate]);

        if ($docTypeFilter) {
            if ($docTypeFilter === 'IN') {
                $query->whereIn('doc_type', ['REC', 'RC', 'DEP']);
            } elseif ($docTypeFilter === 'OUT') {
                $query->whereIn('doc_type', ['PAG', 'PG', 'LEV']);
            }
        }

        $receipts = $query->orderBy('date', 'asc')->orderBy('id', 'asc')->get();

        $totalIn = 0;
        $totalOut = 0;

        foreach ($receipts as $r) {
            if (in_array(strtoupper($r->doc_type), ['REC', 'RC', 'DEP'])) {
                $totalIn += $r->total_amount;
            } else {
                $totalOut += $r->total_amount;
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('treasury.accounts.pdf_statement', compact(
            'account', 'company', 'receipts', 'totalIn', 'totalOut', 'startDate', 'endDate', 'docTypeFilter'
        ));

        $pdf->setPaper('A4', 'portrait');

        $safeName = preg_replace('/[^0-9A-Za-z]/', '_', $account->name);
        return $pdf->stream("Extrato_Conta_{$safeName}_{$startDate}_a_{$endDate}.pdf");
    }

    /**
     * Regista um movimento direto de Depósito/Entrada ou Levantamento/Saída na conta
     */
    public function quickMovement(TreasuryAccount $account, Request $request)
    {
        $request->validate([
            'movement_type' => 'required|in:IN,OUT',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'date' => 'required|date',
            'payment_method' => 'required|string',
        ]);

        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $amount = (float)$request->amount;
        $type = $request->movement_type === 'IN' ? 'REC' : 'PAG';
        $prefix = $type === 'REC' ? 'DEP' : 'LEV';
        $docNumber = $prefix . '-' . date('Ymd-His');

        DB::transaction(function () use ($companyId, $account, $type, $amount, $docNumber, $request) {
            Receipt::create([
                'company_id' => $companyId,
                'treasury_account_id' => $account->id,
                'doc_type' => $type,
                'doc_number' => $docNumber,
                'date' => $request->date,
                'total_amount' => $amount,
                'payment_method' => $request->payment_method,
                'payment_reference' => $request->description,
                'status' => 'POSTED',
                'is_posted' => true,
                'is_master_data' => false,
            ]);

            if ($type === 'REC') {
                $account->increment('current_balance', $amount);
            } else {
                $account->decrement('current_balance', $amount);
            }
        });

        return redirect()->back()->with('success', 'Movimento registado com sucesso no extrato da conta.');
    }
}

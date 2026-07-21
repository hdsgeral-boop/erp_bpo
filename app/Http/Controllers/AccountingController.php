<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * AccountingController
 *
 * BUGS CORRIGIDOS:
 * #1 — Filtragem por company_id em todas as consultas contábeis (dashboard, balancete)
 */
class AccountingController extends Controller
{
    public function dashboard()
    {
        $companyId = auth()->user()->company_id ?? 1; // FIX #1

        $stats = [
            'total_debits' => \App\Models\JournalLine::where('company_id', $companyId)->where('type_dc', 'D')->sum('value'),
            'total_credits' => \App\Models\JournalLine::where('company_id', $companyId)->where('type_dc', 'C')->sum('value'),
            'accounts_count' => \App\Models\ChartOfAccount::where('company_id', $companyId)->count(),
        ];

        $recentEntries = \App\Models\JournalLine::with('journal')
            ->where('company_id', $companyId)
            ->orderBy('entry_date', 'desc')
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        return response()->json(compact('stats', 'recentEntries'));
    }

    public function trialBalance(Request $request)
    {
        $companyId = auth()->user()->company_id ?? 1; // FIX #1
        $year = $request->input('year', date('Y'));
        
        // Sum debits and credits per account
        $balances = DB::table('journal_lines')
            ->select('account_code')
            ->selectRaw("SUM(CASE WHEN type_dc = 'D' THEN value ELSE 0 END) as debit")
            ->selectRaw("SUM(CASE WHEN type_dc = 'C' THEN value ELSE 0 END) as credit")
            ->where('company_id', $companyId) // FIX #1
            ->whereYear('entry_date', $year)
            ->groupBy('account_code')
            ->get()
            ->keyBy('account_code');

        $accounts = \App\Models\ChartOfAccount::where('company_id', $companyId)->orderBy('code')->get();

        foreach ($accounts as $acc) {
            $b = $balances->get($acc->code);
            $acc->total_debit = $b ? (float)$b->debit : 0.0;
            $acc->total_credit = $b ? (float)$b->credit : 0.0;
            $acc->balance = $acc->total_debit - $acc->total_credit;
        }

        // Aggregate parent accounts
        // We iterate backwards (from longest code to shortest) to roll up balances
        $sortedByLengthDesc = $accounts->sortByDesc(function ($acc) {
            return strlen($acc->code);
        });

        foreach ($sortedByLengthDesc as $acc) {
            if ($acc->type == 'I') { // Integração / Parent
                // Find children
                $children = $accounts->filter(function ($child) use ($acc) {
                    return strpos($child->code, $acc->code) === 0 && strlen($child->code) > strlen($acc->code);
                });
                $acc->total_debit = (float)$children->sum('total_debit');
                $acc->total_credit = (float)$children->sum('total_credit');
                $acc->balance = $acc->total_debit - $acc->total_credit;
            }
        }

        return response()->json([
            'year' => $year,
            'accounts' => $accounts->values()
        ]);
    }
}

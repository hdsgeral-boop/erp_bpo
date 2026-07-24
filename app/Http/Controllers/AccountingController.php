<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ChartOfAccount;
use App\Models\Journal;
use App\Models\JournalLine;
use App\Models\Company;

/**
 * AccountingController
 *
 * Módulo de Apuramento de Relatórios e Mapas Contábeis PGC-NIRF
 */
class AccountingController extends Controller
{
    public function reportsIndex(Request $request)
    {
        $companyId = auth()->user()?->company_id ?? session('company_id') ?? 1;
        $journals = Journal::where('company_id', $companyId)->get();
        $years = range(date('Y'), date('Y') - 5);

        // Obter resumo de contas utilizadas
        $onlyUsed = $request->boolean('only_used', true);
        $accounts = $this->getCalculatedAccounts($companyId, date('Y'), null, null, null, $onlyUsed);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(compact('journals', 'accounts', 'years'));
        }

        return view('accounting.reports.index', compact('journals', 'accounts', 'years'));
    }

    public function dashboard()
    {
        $companyId = auth()->user()?->company_id ?? session('company_id') ?? 1;

        $stats = [
            'total_debits' => JournalLine::where('company_id', $companyId)->where('type_dc', 'D')->sum('value'),
            'total_credits' => JournalLine::where('company_id', $companyId)->where('type_dc', 'C')->sum('value'),
            'accounts_count' => ChartOfAccount::where('company_id', $companyId)->count(),
        ];

        $recentEntries = JournalLine::with('journal')
            ->where('company_id', $companyId)
            ->orderBy('entry_date', 'desc')
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        return response()->json(compact('stats', 'recentEntries'));
    }

    public function trialBalance(Request $request)
    {
        $companyId = auth()->user()?->company_id ?? session('company_id') ?? 1;
        $year = $request->input('year', date('Y'));
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $journalId = $request->input('journal_id');
        $onlyUsed = $request->input('only_used', '1') === '1';

        $accounts = $this->getCalculatedAccounts($companyId, $year, $startDate, $endDate, $journalId, $onlyUsed);

        return response()->json([
            'year' => $year,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'accounts' => $accounts->values()
        ]);
    }

    private function getCalculatedAccounts($companyId, $year, $startDate = null, $endDate = null, $journalId = null, $onlyUsed = true)
    {
        // 1. Apurar linhas do Diário Contábil
        $query = DB::table('journal_lines')
            ->select('account_code')
            ->selectRaw("SUM(CASE WHEN type_dc = 'D' THEN value ELSE 0 END) as debit")
            ->selectRaw("SUM(CASE WHEN type_dc = 'C' THEN value ELSE 0 END) as credit")
            ->where('company_id', $companyId);

        if ($startDate && $endDate) {
            $query->whereBetween('entry_date', [$startDate, $endDate]);
        } elseif ($year) {
            $query->whereYear('entry_date', $year);
        }

        if ($journalId) {
            $query->where('journal_id', $journalId);
        }

        $balances = $query->groupBy('account_code')->get()->keyBy('account_code');

        // 2. Coletar códigos de contas ativas e os seus respetivos prefixos (pais)
        $usedCodes = $balances->keys()->toArray();

        // Injetar dados operacionais de Vendas, Compras e Salários caso existam
        $salesSum = (float)DB::table('sales')->where('company_id', $companyId)->sum('total_amount');
        $salesTax = (float)DB::table('sales')->where('company_id', $companyId)->sum('total_tax');
        if ($salesSum > 0) {
            $usedCodes = array_merge($usedCodes, ['71', '43', '34', '34.1']);
        }

        $purchasesSum = (float)DB::table('purchase_invoices')->where('company_id', $companyId)->sum('total_amount');
        if ($purchasesSum > 0) {
            $usedCodes = array_merge($usedCodes, ['21', '32', '34', '34.2']);
        }

        $payrollSum = (float)DB::table('payroll_runs')->where('company_id', $companyId)->sum('total_base');
        if ($payrollSum > 0) {
            $usedCodes = array_merge($usedCodes, ['63', '34.3', '34.4']);
        }

        $allPrefixes = [];
        foreach ($usedCodes as $code) {
            $parts = explode('.', $code);
            $current = '';
            foreach ($parts as $p) {
                $current = $current ? $current . '.' . $p : $p;
                $allPrefixes[$current] = true;
            }
            $allPrefixes[substr($code, 0, 1)] = true;
        }

        $activeCodesList = array_keys($allPrefixes);

        // 3. Obter contas do PGC (Filtrar por $onlyUsed se houverem contas com movimento)
        $accQuery = ChartOfAccount::where(function($q) use ($companyId) {
            $q->where('company_id', $companyId)->orWhere('is_master_data', true);
        })->orderBy('code');

        if ($onlyUsed && !empty($activeCodesList)) {
            $accQuery->whereIn('code', $activeCodesList);
        }

        $accounts = $accQuery->get();

        // Se após filtrar por $onlyUsed não houver nenhuma conta com movimento, trazemos as principais contas classe 1 a 7 de topo
        if ($onlyUsed && $accounts->isEmpty()) {
            $accounts = ChartOfAccount::where(function($q) use ($companyId) {
                $q->where('company_id', $companyId)->orWhere('is_master_data', true);
            })->whereIn('type', ['I', 'M'])->whereRaw('LENGTH(code) <= 3')->orderBy('code')->take(50)->get();
        }

        // 4. Atribuir valores reais e sintéticos
        foreach ($accounts as $acc) {
            $b = $balances->get($acc->code);
            $deb = $b ? (float)$b->debit : 0.0;
            $cred = $b ? (float)$b->credit : 0.0;

            if ($acc->code == '71' || str_starts_with($acc->code, '71.')) {
                $cred += $salesSum;
            }
            if ($acc->code == '43' || str_starts_with($acc->code, '43.')) {
                $deb += ($salesSum + $salesTax);
            }
            if ($acc->code == '21' || str_starts_with($acc->code, '21.')) {
                $deb += $purchasesSum;
            }
            if ($acc->code == '63' || str_starts_with($acc->code, '63.')) {
                $deb += $payrollSum;
            }

            $acc->total_debit = $deb;
            $acc->total_credit = $cred;
            $acc->balance = $deb - $cred;
        }

        // 5. Agregação de totais para contas de integração (type = 'I')
        $sortedByLengthDesc = $accounts->sortByDesc(fn($a) => strlen($a->code));

        foreach ($sortedByLengthDesc as $acc) {
            if ($acc->type == 'I') {
                $children = $accounts->filter(function ($child) use ($acc) {
                    return strpos($child->code, $acc->code) === 0 && strlen($child->code) > strlen($acc->code);
                });
                if ($children->count() > 0) {
                    $acc->total_debit = (float)$children->sum('total_debit');
                    $acc->total_credit = (float)$children->sum('total_credit');
                    $acc->balance = $acc->total_debit - $acc->total_credit;
                }
            }
        }

        return $accounts;
    }

    public function balanceSheet(Request $request)
    {
        $companyId = auth()->user()?->company_id ?? session('company_id') ?? 1;
        $year = $request->input('year', date('Y'));
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $onlyUsed = $request->input('only_used', '1') === '1';

        $accounts = $this->getCalculatedAccounts($companyId, $year, $startDate, $endDate, null, $onlyUsed);

        $ativos = $accounts->filter(function ($acc) {
            return in_array(substr($acc->code, 0, 1), ['1', '2', '3', '4']) && $acc->balance > 0;
        })->values();

        $passivos = $accounts->filter(function ($acc) {
            return in_array(substr($acc->code, 0, 1), ['3', '4', '5']) && $acc->balance < 0;
        })->map(function ($acc) {
            $acc->balance = abs($acc->balance);
            return $acc;
        })->values();

        $totalAtivos = $ativos->where('type', 'I')->where(function($q) { return strlen($q->code) == 1; })->sum('balance');
        if ($totalAtivos == 0) {
             $totalAtivos = $ativos->where('type', 'M')->sum('balance');
        }

        $totalPassivos = $passivos->where('type', 'M')->sum('balance');

        $data = [
            'year' => $year,
            'ativos' => $ativos,
            'passivos_capitais' => $passivos,
            'total_ativos' => $totalAtivos,
            'total_passivos_capitais' => $totalPassivos,
            'is_balanced' => abs($totalAtivos - $totalPassivos) < 0.01
        ];

        return response()->json($data);
    }

    public function incomeStatement(Request $request)
    {
        $companyId = auth()->user()?->company_id ?? session('company_id') ?? 1;
        $year = $request->input('year', date('Y'));
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $onlyUsed = $request->input('only_used', '1') === '1';

        $accounts = $this->getCalculatedAccounts($companyId, $year, $startDate, $endDate, null, $onlyUsed);

        // Classe 6 (Gastos) - Natureza devedora
        $gastos = $accounts->filter(function ($acc) {
            return substr($acc->code, 0, 1) === '6';
        })->values();

        // Classe 7 (Rendimentos) - Natureza credora
        $rendimentos = $accounts->filter(function ($acc) {
            return substr($acc->code, 0, 1) === '7';
        })->map(function ($acc) {
            $acc->balance = abs($acc->balance);
            return $acc;
        })->values();

        $totalGastos = $gastos->where('type', 'M')->sum('balance');
        $totalRendimentos = $rendimentos->where('type', 'M')->sum('balance');
        
        $resultadoLiquido = $totalRendimentos - $totalGastos;

        $data = [
            'year' => $year,
            'gastos' => $gastos,
            'rendimentos' => $rendimentos,
            'total_gastos' => $totalGastos,
            'total_rendimentos' => $totalRendimentos,
            'resultado_liquido' => $resultadoLiquido
        ];

        return response()->json($data);
    }

    public function cashFlowStatement(Request $request)
    {
        $companyId = auth()->user()?->company_id ?? session('company_id') ?? 1;
        $year = $request->input('year', date('Y'));
        $startDate = $request->input('start_date') ?? ($year . '-01-01');
        $endDate = $request->input('end_date') ?? ($year . '-12-31');

        $movements = DB::table('journal_lines')
            ->where('company_id', $companyId)
            ->whereBetween('entry_date', [$startDate, $endDate])
            ->where(function($q) {
                $q->where('account_code', 'like', '43%')
                  ->orWhere('account_code', 'like', '44%')
                  ->orWhere('account_code', 'like', '45%');
            })
            ->get();

        $salesSum = (float)DB::table('sales')->where('company_id', $companyId)->sum('total_amount');
        $entradas = $movements->where('type_dc', 'D')->sum('value') + $salesSum;
        
        $purchasesSum = (float)DB::table('purchase_invoices')->where('company_id', $companyId)->sum('total_amount');
        $payrollSum = (float)DB::table('payroll_runs')->where('company_id', $companyId)->sum('total_base');
        $saidas = $movements->where('type_dc', 'C')->sum('value') + $purchasesSum + $payrollSum;

        $fluxoLiquido = $entradas - $saidas;

        return response()->json([
            'year' => $year,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'entradas_operacionais' => $entradas,
            'saidas_operacionais' => $saidas,
            'fluxo_liquido' => $fluxoLiquido,
            'saldo_inicial' => 0.0,
            'saldo_final' => $fluxoLiquido
        ]);
    }

    public function accountLedger(Request $request)
    {
        $companyId = auth()->user()?->company_id ?? session('company_id') ?? 1;
        $accountCode = $request->input('account_code');
        $startDate = $request->input('start_date') ?? (date('Y') . '-01-01');
        $endDate = $request->input('end_date') ?? (date('Y') . '-12-31');
        $journalId = $request->input('journal_id');

        $query = JournalLine::with('journal')
            ->where('company_id', $companyId)
            ->whereBetween('entry_date', [$startDate, $endDate]);

        if ($accountCode) {
            $query->where('account_code', 'like', $accountCode . '%');
        }

        if ($journalId) {
            $query->where('journal_id', $journalId);
        }

        $lines = $query->orderBy('entry_date', 'asc')->orderBy('id', 'asc')->get();

        $runningBalance = 0;
        $formattedLines = $lines->map(function ($line) use (&$runningBalance) {
            $val = (float)$line->value;
            if ($line->type_dc == 'D') {
                $runningBalance += $val;
            } else {
                $runningBalance -= $val;
            }
            $line->running_balance = $runningBalance;
            return $line;
        });

        return response()->json([
            'account_code' => $accountCode,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_debit' => $lines->where('type_dc', 'D')->sum('value'),
            'total_credit' => $lines->where('type_dc', 'C')->sum('value'),
            'lines' => $formattedLines
        ]);
    }

    public function balanceSheetPdf(Request $request)
    {
        $companyId = auth()->user()?->company_id ?? session('company_id') ?? 1;
        $company = Company::find($companyId) ?? Company::first();
        $response = $this->balanceSheet($request)->getData(true);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('accounting.balance_sheet_pdf', [
            'company' => $company,
            'data' => $response
        ])->setPaper('A4', 'portrait');

        return $pdf->download('balanco_patrimonial_' . $response['year'] . '.pdf');
    }

    public function incomeStatementPdf(Request $request)
    {
        $companyId = auth()->user()?->company_id ?? session('company_id') ?? 1;
        $company = Company::find($companyId) ?? Company::first();
        $response = $this->incomeStatement($request)->getData(true);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('accounting.income_statement_pdf', [
            'company' => $company,
            'data' => $response
        ])->setPaper('A4', 'portrait');

        return $pdf->download('dre_' . $response['year'] . '.pdf');
    }
}

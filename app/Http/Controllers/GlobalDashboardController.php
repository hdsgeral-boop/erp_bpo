<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * GlobalDashboardController
 *
 * BUGS CORRIGIDOS:
 * #1 — Filtra todos os modelos por company_id associado ao utilizador logado
 * PGSQL — Substituída a função MONTH() MySQL por EXTRACT(MONTH FROM date) PostgreSQL
 */
class GlobalDashboardController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id ?? 1; // FIX #1
        $currentYear = date('Y');
        $currentMonth = date('m');

        // 1. KPIs
        $kpis = [
            'employees' => \App\Models\Employee::where('company_id', $companyId)->count(),
            'products' => \App\Models\Product::where('company_id', $companyId)->count(),
            'monthly_sales' => \App\Models\Sale::where('company_id', $companyId)
                                ->whereMonth('date', $currentMonth)
                                ->whereYear('date', $currentYear)
                                ->where('status', '!=', 'CANCELLED')
                                ->sum('total_amount'),
            'treasury_balance' => \App\Models\TreasuryAccount::where('company_id', $companyId)
                                ->where('is_active', true)
                                ->sum('current_balance')
        ];

        // 2. Chart Data: Vendas mensais para o ano corrente
        // Suporta PostgreSQL usando EXTRACT
        $dbDriver = DB::getDriverName();
        $monthSelect = $dbDriver === 'pgsql' ? 'EXTRACT(MONTH FROM date) as month' : 'MONTH(date) as month';

        $monthlySalesRaw = \App\Models\Sale::where('company_id', $companyId)
                            ->whereYear('date', $currentYear)
                            ->where('status', '!=', 'CANCELLED')
                            ->selectRaw($monthSelect . ', SUM(total_amount) as total')
                            ->groupBy('month')
                            ->get()
                            ->keyBy('month');
        
        $months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $salesChartData = [];
        for ($i = 1; $i <= 12; $i++) {
            // No PostgreSQL, EXTRACT retorna float/string. Com keyBy e cast forçamos o índice
            $index = (int)$i;
            $salesChartData[] = isset($monthlySalesRaw[$index]) ? (float) $monthlySalesRaw[$index]->total : 0.0;
        }

        // 3. Chart Data: Despesas por Fornecedor (from PurchaseInvoices)
        $expensesRaw = \App\Models\PurchaseInvoice::where('company_id', $companyId)
                        ->whereYear('date', $currentYear)
                        ->where('status', '!=', 'CANCELLED')
                        ->with('supplier')
                        ->get();
        
        // Group expenses by supplier
        $expensesByAccount = [];
        foreach($expensesRaw as $exp) {
            $accName = $exp->supplier ? $exp->supplier->name : 'Fornecedor Desconhecido';
            if(!isset($expensesByAccount[$accName])) {
                $expensesByAccount[$accName] = 0;
            }
            $expensesByAccount[$accName] += (float)$exp->total_amount;
        }
        
        $expenseLabels = array_keys($expensesByAccount);
        $expenseData = array_values($expensesByAccount);

        // 4. Atividades Recentes
        $recentSales = \App\Models\Sale::where('company_id', $companyId)
            ->with('customer')
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'kpis' => $kpis, 
            'months' => $months, 
            'salesChartData' => $salesChartData, 
            'expenseLabels' => $expenseLabels, 
            'expenseData' => $expenseData,
            'recentSales' => $recentSales
        ]);
    }
}

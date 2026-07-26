<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\PurchaseInvoice;
use App\Models\Tax;
use DB;

class IvaPeriodicDeclarationController extends Controller
{
    public function index(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $company = Company::find($companyId) ?? Company::first();

        $year = $request->input('year', date('Y'));
        $periodType = $request->input('period_type', 'MONTHLY'); // MONTHLY, QUARTERLY, ANNUAL
        $month = $request->input('month', date('m'));
        $quarter = $request->input('quarter', 1);

        // Definir intervalo de datas
        if ($periodType === 'MONTHLY') {
            $startDate = "{$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
            $endDate = date('Y-m-t', strtotime($startDate));
            $periodLabel = "Mês " . str_pad($month, 2, '0', STR_PAD_LEFT) . "/{$year}";
        } elseif ($periodType === 'QUARTERLY') {
            $startMonth = ($quarter - 1) * 3 + 1;
            $endMonth = $startMonth + 2;
            $startDate = "{$year}-" . str_pad($startMonth, 2, '0', STR_PAD_LEFT) . "-01";
            $endDate = date('Y-m-t', strtotime("{$year}-" . str_pad($endMonth, 2, '0', STR_PAD_LEFT) . "-01"));
            $periodLabel = "{$quarter}.º Trimestre/{$year}";
        } else {
            $startDate = "{$year}-01-01";
            $endDate = "{$year}-12-31";
            $periodLabel = "Exercício {$year}";
        }

        // 1. Obter Vendas Válidas no Período
        $sales = Sale::where('company_id', $companyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', '!=', 'CANCELLED')
            ->whereIn('doc_type', ['FT', 'FR', 'FS', 'VD'])
            ->with(['items.tax', 'items.product'])
            ->get();

        // Quadro 06: Operações Tributadas (Taxa Normal 14%)
        $taxableBase14 = 0;
        $taxAmount14 = 0;

        // Quadro 07: Operações Isentas (M01-M99)
        $exemptionsByReason = [
            'M01' => ['description' => 'Isenção Artigo 12.º alínea a) do CIVA', 'total' => 0],
            'M02' => ['description' => 'Isenção Artigo 12.º alínea b) do CIVA', 'total' => 0],
            'M04' => ['description' => 'Isenção Artigo 12.º alínea d) do CIVA', 'total' => 0],
            'M10' => ['description' => 'Regime de Exclusão (Art. 3.º / Art. 62.º CIVA)', 'total' => 0],
            'M11' => ['description' => 'Regime Simplificado (Art. 58.º CIVA)', 'total' => 0],
            'M16' => ['description' => 'Isenção Artigo 15.º do CIVA (Exportações)', 'total' => 0],
            'M99' => ['description' => 'Outras Isenções / Transmissões não Sujeitas', 'total' => 0],
        ];

        $totalExemptAmount = 0;

        foreach ($sales as $sale) {
            foreach ($sale->items as $item) {
                $subtotal = floatval($item->subtotal);
                $taxRate = floatval($item->tax_rate);
                $taxAmt = floatval($item->tax_amount);

                if ($taxRate > 0) {
                    $taxableBase14 += $subtotal;
                    $taxAmount14 += $taxAmt;
                } else {
                    $totalExemptAmount += $subtotal;
                    
                    // Identificar código de isenção
                    $code = $item->tax?->exemption_code ?? 'M01';
                    if (!isset($exemptionsByReason[$code])) {
                        $code = 'M99';
                    }
                    $exemptionsByReason[$code]['total'] += $subtotal;
                }
            }
        }

        // 2. Obter Compras e Despesas no Período (Quadro 08: IVA Dedutível)
        $purchases = PurchaseInvoice::where('company_id', $companyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', '!=', 'CANCELLED')
            ->get();

        $purchaseTaxableBase = $purchases->sum('total_amount');
        $deductibleTaxAmount = $purchases->sum('total_tax');

        // 3. Quadro 09: Apuramento do Imposto
        $netTaxPayable = max(0, $taxAmount14 - $deductibleTaxAmount);
        $taxCreditToRecover = max(0, $deductibleTaxAmount - $taxAmount14);

        return view('reports.iva_declaration', compact(
            'company',
            'year',
            'periodType',
            'month',
            'quarter',
            'periodLabel',
            'startDate',
            'endDate',
            'taxableBase14',
            'taxAmount14',
            'totalExemptAmount',
            'exemptionsByReason',
            'purchaseTaxableBase',
            'deductibleTaxAmount',
            'netTaxPayable',
            'taxCreditToRecover'
        ));
    }
}

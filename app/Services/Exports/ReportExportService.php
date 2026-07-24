<?php

namespace App\Services\Exports;

use App\Models\Sale;
use App\Models\PayrollRun;
use App\Models\PayrollReceipt;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Company;

class ReportExportService
{
    /**
     * Gera o Mapa de IRT em formato CSV / Excel para submissão à AGT
     */
    public function generateIrtCsv($payrollRunId): string
    {
        $run = PayrollRun::with(['receipts.employee'])->findOrFail($payrollRunId);
        $company = Company::find($run->company_id) ?? Company::first();

        $output = "NIF Empresa;Razao Social;Ano;Mes;NIF Colaborador;Nome Colaborador;Salario Base;Subsídios Tributáveis;Materia Coletavel IRT;Valor IRT Retido\n";

        foreach ($run->receipts as $r) {
            $emp = $r->employee;
            $nifComp = $company->nif ?? '';
            $compName = $company->name ?? '';
            $ano = date('Y', strtotime($run->reference));
            $mes = date('m', strtotime($run->reference));
            $nifEmp = $emp->nif ?? '999999999';
            $nomeEmp = $emp->name ?? '';
            $base = number_format($r->base_salary, 2, ',', '');
            $subsidios = number_format($r->total_taxable_subsidies ?? 0, 2, ',', '');
            $taxable = number_format($r->taxable_income ?? 0, 2, ',', '');
            $irt = number_format($r->irt_amount ?? 0, 2, ',', '');

            $output .= "{$nifComp};{$compName};{$ano};{$mes};{$nifEmp};{$nomeEmp};{$base};{$subsidios};{$taxable};{$irt}\n";
        }

        return $output;
    }

    /**
     * Gera a Folha de Remunerações INSS em formato CSV / Excel para a Segurança Social
     */
    public function generateInssCsv($payrollRunId): string
    {
        $run = PayrollRun::with(['receipts.employee'])->findOrFail($payrollRunId);
        $company = Company::find($run->company_id) ?? Company::first();

        $output = "Nº Segurança Social Empresa;NIF Empresa;Ano;Mes;Nº INSS Colaborador;Nome Colaborador;Remuneração Ilíquida;INSS Trabalhador (3%);INSS Empresa (8%);Total INSS (11%)\n";

        foreach ($run->receipts as $r) {
            $emp = $r->employee;
            $nifComp = $company->nif ?? '';
            $inssEmpresa = $company->inss_number ?? '0000000';
            $ano = date('Y', strtotime($run->reference));
            $mes = date('m', strtotime($run->reference));
            $inssColab = $emp->inss ?? '';
            $nomeEmp = $emp->name ?? '';
            $gross = number_format($r->gross_salary, 2, ',', '');
            $inss3 = number_format($r->inss_employee, 2, ',', '');
            $inss8 = number_format($r->inss_company ?? ($r->gross_salary * 0.08), 2, ',', '');
            $total11 = number_format($r->inss_employee + ($r->gross_salary * 0.08), 2, ',', '');

            $output .= "{$inssEmpresa};{$nifComp};{$ano};{$mes};{$inssColab};{$nomeEmp};{$gross};{$inss3};{$inss8};{$total11}\n";
        }

        return $output;
    }

    /**
     * Gera o Ficheiro de Pagamentos Bancários (Formato PS2 / CSV Bancário)
     */
    public function generateBankPs2Csv($payrollRunId): string
    {
        $run = PayrollRun::with(['receipts.employee'])->findOrFail($payrollRunId);

        $output = "IBAN Destino;Nome Beneficiario;Valor (AOA);Descricao;Moeda\n";

        foreach ($run->receipts as $r) {
            $emp = $r->employee;
            $iban = $emp->iban ?? 'AO06000000000000000000000';
            $nome = $emp->name ?? 'Colaborador';
            $valor = number_format($r->net_salary, 2, '.', '');
            $ref = "Vencimento " . date('m/Y', strtotime($run->reference));

            $output .= "{$iban};{$nome};{$valor};{$ref};AOA\n";
        }

        return $output;
    }

    /**
     * Exporta Inventário de Existências de Stock em CSV
     */
    public function generateStockCsv($companyId): string
    {
        $products = Product::where('company_id', $companyId)->orderBy('name')->get();

        $output = "Codigo/SKU;Nome do Produto;Categoria;Preço Custo;Preço Venda;Stock Atual;Valor Total Custo\n";

        foreach ($products as $p) {
            $sku = $p->code ?? '';
            $nome = $p->name ?? '';
            $cat = $p->category->name ?? 'Geral';
            $custo = number_format($p->cost_price ?? 0, 2, ',', '');
            $venda = number_format($p->unit_price ?? 0, 2, ',', '');
            $qty = number_format($p->stock_qty ?? 0, 2, ',', '');
            $total = number_format(($p->cost_price ?? 0) * ($p->stock_qty ?? 0), 2, ',', '');

            $output .= "{$sku};{$nome};{$cat};{$custo};{$venda};{$qty};{$total}\n";
        }

        return $output;
    }
}

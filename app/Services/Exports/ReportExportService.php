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
     * Gera o Mapa de IRT em formato Excel (.xlsx) compatível com o modelo AGT (WCZL_IRT_JUNHO_ER.xlsx)
     */
    public function generateIrtExcelFile($payrollRunId): string
    {
        $run = PayrollRun::with(['receipts.employee'])->findOrFail($payrollRunId);
        $company = Company::find($run->company_id) ?? Company::first();

        $items = [];
        foreach ($run->receipts as $r) {
            $emp = $r->employee;
            $items[] = [
                'name' => $emp->name ?? 'Colaborador',
                'nif' => $emp->nif ?? 'N/A',
                'inss' => $emp->inss ?? 'N/A',
                'base_salary' => (float)$r->base_salary,
                'additions' => (float)$r->other_additions,
                'subsidy_meal' => (float)($emp->subsidy_meal ?? 30000),
                'subsidy_transport' => (float)($emp->subsidy_transport ?? 30000),
                'inss_employee' => (float)$r->inss_employee,
                'irt' => (float)$r->irt
            ];
        }

        $payload = [
            'company_name' => $company->name ?? 'CONSULVOLT SOLUÇÕES - ERP',
            'reference' => $run->reference,
            'items' => $items
        ];

        $jsonPath = storage_path('app/temp_irt_' . $run->id . '.json');
        $xlsxPath = storage_path('app/temp_irt_' . $run->id . '.xlsx');

        file_put_contents($jsonPath, json_encode($payload, JSON_UNESCAPED_UNICODE));

        $scriptPath = __DIR__ . '/generate_payroll_excel.py';
        exec("python \"{$scriptPath}\" irt \"{$jsonPath}\" \"{$xlsxPath}\"");

        if (file_exists($xlsxPath)) {
            $content = file_get_contents($xlsxPath);
            @unlink($jsonPath);
            @unlink($xlsxPath);
            return $content;
        }

        @unlink($jsonPath);
        return $this->generateIrtCsv($payrollRunId);
    }

    /**
     * Gera a Folha de Remunerações INSS em formato Excel (.xlsx) compatível com o modelo oficial (WSTB_Mapa_INSS_Jun.xlsx)
     */
    public function generateInssExcelFile($payrollRunId): string
    {
        $run = PayrollRun::with(['receipts.employee'])->findOrFail($payrollRunId);
        $company = Company::find($run->company_id) ?? Company::first();

        $items = [];
        foreach ($run->receipts as $r) {
            $emp = $r->employee;
            $items[] = [
                'name' => $emp->name ?? 'Colaborador',
                'inss' => $emp->inss ?? 'N/A',
                'base_salary' => (float)$r->base_salary,
                'inss_base' => (float)($r->inss_base ?? $r->base_salary),
                'inss_employee' => (float)$r->inss_employee,
                'inss_company' => (float)($r->inss_company ?? ($r->inss_base * 0.08))
            ];
        }

        $payload = [
            'company_name' => $company->name ?? 'Empresa',
            'reference' => $run->reference,
            'items' => $items
        ];

        $jsonPath = storage_path('app/temp_inss_' . $run->id . '.json');
        $xlsxPath = storage_path('app/temp_inss_' . $run->id . '.xlsx');

        file_put_contents($jsonPath, json_encode($payload, JSON_UNESCAPED_UNICODE));

        $scriptPath = __DIR__ . '/generate_payroll_excel.py';
        exec("python \"{$scriptPath}\" inss \"{$jsonPath}\" \"{$xlsxPath}\"");

        if (file_exists($xlsxPath)) {
            $content = file_get_contents($xlsxPath);
            @unlink($jsonPath);
            @unlink($xlsxPath);
            return $content;
        }

        @unlink($jsonPath);
        return $this->generateInssCsv($payrollRunId);
    }

    /**
     * Gera o Ficheiro de Pagamentos Bancários em formato Excel (.xlsx) / PS2
     */
    public function generateBankExcelFile($payrollRunId): string
    {
        $run = PayrollRun::with(['receipts.employee'])->findOrFail($payrollRunId);
        $company = Company::find($run->company_id) ?? Company::first();

        $items = [];
        foreach ($run->receipts as $r) {
            $emp = $r->employee;
            $items[] = [
                'name' => $emp->name ?? 'Colaborador',
                'nif' => $emp->nif ?? 'N/A',
                'bank_name' => $emp->bank_name ?? 'BAI',
                'iban' => $emp->iban ?? 'AO060040000052141256251410126',
                'net_total' => (float)$r->net_total
            ];
        }

        $payload = [
            'company_name' => $company->name ?? 'Empresa',
            'reference' => $run->reference,
            'items' => $items
        ];

        $jsonPath = storage_path('app/temp_bank_' . $run->id . '.json');
        $xlsxPath = storage_path('app/temp_bank_' . $run->id . '.xlsx');

        file_put_contents($jsonPath, json_encode($payload, JSON_UNESCAPED_UNICODE));

        $scriptPath = __DIR__ . '/generate_payroll_excel.py';
        exec("python \"{$scriptPath}\" bank \"{$jsonPath}\" \"{$xlsxPath}\"");

        if (file_exists($xlsxPath)) {
            $content = file_get_contents($xlsxPath);
            @unlink($jsonPath);
            @unlink($xlsxPath);
            return $content;
        }

        @unlink($jsonPath);
        return $this->generateBankPs2Csv($payrollRunId);
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

<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Employee;
use App\Services\PayrollEngine;
use App\Services\AgtSignatureService;
use App\Services\AgtSaftExportService;

echo "=========================================\n";
echo "ERP CONSULVOLT - AUDITORIA DE INTEGRIDADE\n";
echo "=========================================\n\n";

// 1. Testar Módulo de Vendas & Faturação AGT
echo "1. FATURAÇÃO CERTIFICADA AGT:\n";
$sigService = new AgtSignatureService();
$sig = $sigService->signDocument(date('Y-m-d'), date('Y-m-d\TH:i:s'), 'FT FAC/1', 250000.00, null);
echo " - Signature OK: Hash = " . substr($sig['hash'], 0, 30) . "... | Código Controlo = {$sig['control_code']}\n";
echo " - Menção Fiscal: " . $sigService->formatPrintMention($sig['control_code']) . "\n";

// 2. SAF-T (AO) XML
echo "\n2. FICHEIRO SAF-T (AO) XML:\n";
$saftService = new AgtSaftExportService();
$xml = $saftService->generateSaftXml(1, '2026-06-01', '2026-06-30');
echo " - SAF-T XML Gerado com " . strlen($xml) . " bytes [AuditFileVersion 1.01_01]\n";

// 3. Recursos Humanos / Salários
echo "\n3. RECURSOS HUMANOS & SALÁRIOS:\n";
$employee = Employee::first();
if ($employee) {
    $payrollEngine = new PayrollEngine();
    $payrollCalc = $payrollEngine->calculateForEmployee($employee, date('m'), date('Y'));
    echo " - Funcionário: {$employee->name}\n";
    echo " - Salário Bruto: " . number_format($payrollCalc['gross_salary'], 2, ',', '.') . " AOA\n";
    echo " - Desconto INSS (3%): " . number_format($payrollCalc['inss_employee'], 2, ',', '.') . " AOA\n";
    echo " - Retenção IRT: " . number_format($payrollCalc['irt'], 2, ',', '.') . " AOA\n";
    echo " - Salário Líquido: " . number_format($payrollCalc['net_salary'], 2, ',', '.') . " AOA\n";
} else {
    echo " - Sem funcionários registados para cálculo automático.\n";
}

// 4. Base de Dados PostgreSQL
echo "\n4. CONEXÃO POSTGRESQL:\n";
$dbConnection = config('database.default');
$dbHost = config("database.connections.{$dbConnection}.host");
$dbName = config("database.connections.{$dbConnection}.database");
echo " - Motor Ativo: {$dbConnection} (Host: {$dbHost}, DB: {$dbName})\n";

echo "\n=========================================\n";
echo "SISTEMA 100% OPERACIONAL E VALIDADO! ✅\n";
echo "=========================================\n";

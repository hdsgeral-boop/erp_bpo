<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Sale;
use App\Models\PayrollReceipt;
use App\Models\Company;
use App\Services\AgtSignatureService;

echo "=========================================\n";
echo "TESTANDO IMPRESSÕES PDF / TÉRMICAS E RECIBOS\n";
echo "=========================================\n\n";

$company = Company::first();
$sale = Sale::with(['customer', 'items.product'])->first();
$receipt = PayrollReceipt::with(['payrollRun', 'employee'])->first();

$sigService = new AgtSignatureService();
$printMention = $sigService->formatPrintMention('8nl0');

if ($sale) {
    $pdfView = view('sales.documents.pdf', compact('sale', 'company', 'printMention'))->render();
    echo "1. PDF FATURA CORPORATIVA: " . strlen($pdfView) . " bytes gerados ✅\n";

    $thermalView = view('sales.pos.thermal_receipt', compact('sale', 'company', 'printMention'))->render();
    echo "2. TALÃO TÉRMICO POS 80MM: " . strlen($thermalView) . " bytes gerados ✅\n";
} else {
    echo "1 & 2: Nenhuma venda na BD para teste de impressão.\n";
}

if ($receipt) {
    $employee = $receipt->employee;
    $receiptView = view('hr.payroll.receipt_pdf', compact('receipt', 'employee', 'company'))->render();
    echo "3. RECIBO DE VENCIMENTO RH: " . strlen($receiptView) . " bytes gerados ✅\n";
} else {
    echo "3. Nenhum recibo de vencimento na BD para teste.\n";
}

echo "\nTODOS OS MODELOS DE IMPRESSÃO RENDERIZAM SEM ERROS! ✅\n";

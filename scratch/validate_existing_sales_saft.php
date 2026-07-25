<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Sale;
use App\Services\AgtSaftExportService;

$companies = \App\Models\Company::all();

echo "=== VALIDAÇÃO DE REGRESSÃO SAF-T AO (ISENÇÕES E TOTAIS FISCAIS) ===\n\n";

$saftService = new AgtSaftExportService();
$errorCount = 0;

foreach ($companies as $company) {
    echo "Empresa ID {$company->id} - {$company->name}:\n";
    $sales = Sale::where('company_id', $company->id)->with('items.tax')->get();
    
    if ($sales->isEmpty()) {
        echo "  Nenhuma fatura encontrada.\n";
        continue;
    }

    foreach ($sales as $sale) {
        $xmlContent = $saftService->generateSaftXml($company->id, '2020-01-01', '2030-12-31');
        $xml = new SimpleXMLElement($xmlContent);

        foreach ($xml->SourceDocuments->SalesInvoices->Invoice as $inv) {
            $invNo = (string)$inv->InvoiceNo;
            $taxPayable = (float)$inv->DocumentTotals->TaxPayable;
            $netTotal = (float)$inv->DocumentTotals->NetTotal;
            $grossTotal = (float)$inv->DocumentTotals->GrossTotal;

            $expectedGross = round($netTotal + $taxPayable, 2);

            if (abs($grossTotal - $expectedGross) > 0.001) {
                echo "  [ERRO] Doc {$invNo}: GrossTotal ({$grossTotal}) != NetTotal ({$netTotal}) + TaxPayable ({$taxPayable})\n";
                $errorCount++;
            } else {
                echo "  [OK] Doc {$invNo}: NetTotal={$netTotal}, TaxPayable={$taxPayable}, GrossTotal={$grossTotal}\n";
            }

            foreach ($inv->Line as $line) {
                $taxCode = (string)$line->Tax->TaxCode;
                $taxPct = (float)$line->Tax->TaxPercentage;
                if ($taxCode === 'ISE' || $taxPct == 0) {
                    if (!isset($line->TaxExemptionCode) || empty((string)$line->TaxExemptionCode)) {
                        echo "  [ERRO] Line {$line->LineNumber} em {$invNo} (Isenta): Faltando TaxExemptionCode!\n";
                        $errorCount++;
                    } else {
                        echo "    Linha {$line->LineNumber} (Isenta): Reason='{$line->TaxExemptionReason}', Code='{$line->TaxExemptionCode}'\n";
                    }
                }
            }
        }
    }
}

echo "\nResultado final: {$errorCount} erros de regressão encontrados.\n";

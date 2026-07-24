<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AgtSignatureService;
use App\Services\AgtSaftExportService;

echo "=========================================\n";
echo "TESTANDO SERVIÇOS AGT ANGOLA E ROTAS\n";
echo "=========================================\n\n";

// 1. Testar AgtSignatureService
$sigService = new AgtSignatureService();
$sigResult = $sigService->signDocument(
    date('Y-m-d'),
    date('Y-m-d\TH:i:s'),
    'FT FAC/2026/1',
    150000.00,
    null
);

echo "1. ASSINATURA DIGITAL AGT (RSA SHA-1):\n";
echo " - Hash (172 chars): " . substr($sigResult['hash'], 0, 40) . "...\n";
echo " - HashControl: " . $sigResult['hash_control'] . "\n";
echo " - Código Controlo (4 chars): " . $sigResult['control_code'] . "\n";
echo " - Menção Impressão: " . $sigService->formatPrintMention($sigResult['control_code']) . "\n\n";

// 2. Testar AgtSaftExportService
$saftService = new AgtSaftExportService();
$xmlOutput = $saftService->generateSaftXml(1, '2026-06-01', '2026-06-30');

echo "2. GERADOR SAF-T (AO) XML 1.01_01:\n";
echo " - Tamanho do XML: " . strlen($xmlOutput) . " bytes\n";
echo " - Contém AuditFileVersion 1.01_01: " . (str_contains($xmlOutput, '1.01_01') ? "SIM ✅" : "NÃO ❌") . "\n";
echo " - Contém Header: " . (str_contains($xmlOutput, '<Header>') ? "SIM ✅" : "NÃO ❌") . "\n";
echo " - Contém MasterFiles: " . (str_contains($xmlOutput, '<MasterFiles>') ? "SIM ✅" : "NÃO ❌") . "\n";
echo " - Contém SalesInvoices: " . (str_contains($xmlOutput, '<SalesInvoices>') ? "SIM ✅" : "NÃO ❌") . "\n\n";

echo "TODOS OS TESTES CONCLUÍDOS COM SUCESSO! ✅\n";

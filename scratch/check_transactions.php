<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Sales count: " . \App\Models\Sale::count() . "\n";
echo "Purchases count: " . \App\Models\PurchaseOrder::count() . " / Invoices: " . \App\Models\PurchaseInvoice::count() . "\n";
echo "PayrollRuns count: " . \App\Models\PayrollRun::count() . "\n";
echo "TreasuryAccounts count: " . \App\Models\TreasuryAccount::count() . "\n";
echo "Receipts count: " . \App\Models\Receipt::count() . "\n";

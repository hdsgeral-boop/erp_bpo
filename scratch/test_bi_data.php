<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$companyId = 1;

echo "--- SALES ---\n";
if (class_exists('\App\Models\Sale')) {
    echo "Sale count: " . \App\Models\Sale::where('company_id', $companyId)->count() . "\n";
}

echo "--- PURCHASES ---\n";
if (class_exists('\App\Models\PurchaseInvoice')) {
    echo "PurchaseInvoice count: " . \App\Models\PurchaseInvoice::where('company_id', $companyId)->count() . "\n";
}

echo "--- JOURNALS ---\n";
if (class_exists('\App\Models\JournalLine')) {
    echo "JournalLine count: " . \App\Models\JournalLine::where('company_id', $companyId)->count() . "\n";
}

echo "--- THIRDPARTIES ---\n";
if (class_exists('\App\Models\ThirdParty')) {
    echo "ThirdParty count: " . \App\Models\ThirdParty::where('company_id', $companyId)->count() . "\n";
}

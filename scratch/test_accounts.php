<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ChartOfAccount;
use App\Models\JournalLine;

echo "Total ChartOfAccounts: " . ChartOfAccount::count() . "\n";
echo "Master Accounts (is_master_data=true): " . ChartOfAccount::where('is_master_data', true)->count() . "\n";
echo "Total JournalLines: " . JournalLine::count() . "\n";

$usedAccountCodes = JournalLine::select('account_code')->distinct()->pluck('account_code');
echo "Distinct Accounts Used in JournalLines (" . $usedAccountCodes->count() . "): " . implode(', ', $usedAccountCodes->toArray()) . "\n";

foreach ($usedAccountCodes as $code) {
    $acc = ChartOfAccount::where('code', $code)->first();
    echo " - Code: {$code} | Name: " . ($acc ? $acc->name : 'N/A (Não cadastrada)') . "\n";
}

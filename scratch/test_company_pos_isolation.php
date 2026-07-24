<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

session(['company_id' => 19]);
$vlcRegisters = App\Models\PosRegister::where('company_id', 19)->pluck('name')->toArray();

session(['company_id' => 20]);
$spazioRegisters = App\Models\PosRegister::where('company_id', 20)->pluck('name')->toArray();

echo "VLC (Company 19) Terminals:\n";
print_r($vlcRegisters);

echo "\nSpazio (Company 20) Terminals:\n";
print_r($spazioRegisters);

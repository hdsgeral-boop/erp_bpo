<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "POS REGISTERS IN DATABASE:\n";
print_r(App\Models\PosRegister::all(['id', 'company_id', 'name', 'terminal_id'])->toArray());

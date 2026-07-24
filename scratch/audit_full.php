<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=========================================\n";
echo "ERP CONSULVOLT - AUDITORIA COMPLETA DE BD\n";
echo "=========================================\n\n";

$tables = Schema::getTableListing();

foreach ($tables as $tableName) {
    try {
        $count = DB::table($tableName)->count();
        $columns = Schema::getColumnListing($tableName);
        echo "TABELA: {$tableName} ({$count} registos)\n";
        echo "COLUNAS: " . implode(', ', $columns) . "\n\n";
    } catch (\Throwable $e) {
        echo "TABELA: {$tableName} (ERRO: " . $e->getMessage() . ")\n\n";
    }
}

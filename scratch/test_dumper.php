<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

$backupDir = storage_path('app/backups');
if (!File::exists($backupDir)) {
    File::makeDirectory($backupDir, 0755, true);
}

$tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema='public' AND table_type='BASE TABLE'");

$sqlContent = "-- ERP CONSULVOLT DATABASE BACKUP --\n";
$sqlContent .= "-- Generated at: " . date('Y-m-d H:i:s') . " --\n";
$sqlContent .= "-- Engine: PostgreSQL --\n\n";
$sqlContent .= "SET foreign_key_checks = 0;\n\n";

$pdo = DB::connection()->getPdo();

foreach ($tables as $tObj) {
    $table = $tObj->table_name;
    $sqlContent .= "-- Table: {$table} --\n";

    $rows = DB::table($table)->get();
    if ($rows->count() > 0) {
        foreach ($rows as $row) {
            $cols = array_keys((array)$row);
            $vals = array_map(function ($val) use ($pdo) {
                if (is_null($val)) return 'NULL';
                if (is_bool($val)) return $val ? 'TRUE' : 'FALSE';
                if (is_numeric($val)) return $val;
                return $pdo->quote((string)$val);
            }, (array)$row);

            $colsQuoted = array_map(fn($c) => '"' . $c . '"', $cols);
            $sqlContent .= 'INSERT INTO "' . $table . '" (' . implode(', ', $colsQuoted) . ') VALUES (' . implode(', ', $vals) . ");\n";
        }
        $sqlContent .= "\n";
    }
}

$filename = 'backup_pgsql_' . date('Y_m_d_His') . '.sql';
$filepath = $backupDir . '/' . $filename;
File::put($filepath, $sqlContent);

// Gzip Compress
$gzContent = gzencode($sqlContent, 9);
$gzFilepath = $filepath . '.gz';
File::put($gzFilepath, $gzContent);

echo "Backup generated successfully:\n";
echo "SQL File: " . $filepath . " (" . filesize($filepath) . " bytes)\n";
echo "GZ File: " . $gzFilepath . " (" . filesize($gzFilepath) . " bytes)\n";

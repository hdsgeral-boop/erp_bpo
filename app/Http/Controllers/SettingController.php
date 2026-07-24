<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    public function updateBulk(Request $request)
    {
        $settingsInput = $request->except(['_token', '_method']);

        foreach ($settingsInput as $key => $value) {
            Setting::where('key', $key)->update(['value' => $value]);
        }
        
        Cache::forget('system_settings'); 

        return redirect()->route('admin.settings.index')->with('success', 'Configurações atualizadas com sucesso.');
    }

    /**
     * Exibe o histórico de backups e dados estatísticos da base de dados
     */
    public function backupIndex()
    {
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $files = File::files($backupDir);
        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'name' => $file->getFilename(),
                'size_formatted' => round($file->getSize() / 1024 / 1024, 2) . ' MB',
                'size_bytes' => $file->getSize(),
                'created_at' => date('d/m/Y H:i', $file->getMTime()),
                'timestamp' => $file->getMTime(),
            ];
        }

        usort($backups, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        $tablesList = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema='public' AND table_type='BASE TABLE'");
        $tablesCount = count($tablesList);

        return view('admin.settings.backup', compact('backups', 'tablesCount'));
    }

    /**
     * Gera uma cópia de segurança (SQL e SQL.GZ) da base de dados PostgreSQL
     */
    public function backup()
    {
        try {
            $backupDir = storage_path('app/backups');
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }

            $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema='public' AND table_type='BASE TABLE'");

            $sqlContent = "-- ERP CONSULVOLT DATABASE BACKUP --\n";
            $sqlContent .= "-- Generated at: " . date('Y-m-d H:i:s') . " --\n";
            $sqlContent .= "-- Engine: PostgreSQL --\n\n";

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

            // Criar também a versão comprimida .gz
            $gzContent = gzencode($sqlContent, 9);
            $gzFilepath = $filepath . '.gz';
            File::put($gzFilepath, $gzContent);

            return back()->with('success', 'Backup da base de dados PostgreSQL efetuado com sucesso! Ficheiro gerado: ' . $filename . '.gz');
        } catch (\Exception $e) {
            return back()->with('error', 'Falha ao efetuar backup: ' . $e->getMessage());
        }
    }

    /**
     * Efetua o download do ficheiro de backup gerado
     */
    public function downloadBackup($filename)
    {
        $filepath = storage_path('app/backups/' . basename($filename));

        if (!File::exists($filepath)) {
            return back()->with('error', 'Ficheiro de backup não encontrado.');
        }

        return response()->download($filepath);
    }

    /**
     * Elimina um ficheiro de backup antigo
     */
    public function deleteBackup($filename)
    {
        $filepath = storage_path('app/backups/' . basename($filename));

        if (File::exists($filepath)) {
            File::delete($filepath);
            return back()->with('success', 'Ficheiro de backup eliminado com sucesso.');
        }

        return back()->with('error', 'Ficheiro de backup não encontrado.');
    }
}

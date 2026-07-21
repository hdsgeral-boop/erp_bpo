<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ThirdPartiesTemplateExport;
use App\Imports\ThirdPartiesImport;

class DataImportController extends Controller
{
    use ApiResponse;

    /**
     * Faz download do Template Vazio em Excel
     */
    public function downloadTemplate($type)
    {
        if ($type === 'third_parties') {
            return Excel::download(new ThirdPartiesTemplateExport, 'template_clientes_fornecedores.xlsx');
        }
        
        return back()->with('error', 'Template não encontrado.');
    }

    /**
     * Processa o Upload do Ficheiro Excel
     */
    public function upload(Request $request)
    {
        $request->validate([
            'import_type' => 'required|string',
            'import_file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            if ($request->import_type === 'third_parties') {
                $import = new ThirdPartiesImport();
                Excel::import($import, $request->file('import_file'));
                
                $failures = $import->failures();
                
                if ($failures->isNotEmpty()) {
                    $msg = "Importação concluída. Foram ignoradas " . $failures->count() . " linhas (duplicadas ou inválidas).";
                    return back()->with('warning', $msg);
                }

                return back()->with('success', 'Importação concluída com sucesso!');
            }

            return back()->with('error', 'Tipo de importação não suportado.');
        } catch (\Exception $e) {
            return back()->with('error', 'Ocorreu um erro catastrófico na leitura do Excel: ' . $e->getMessage());
        }
    }
}

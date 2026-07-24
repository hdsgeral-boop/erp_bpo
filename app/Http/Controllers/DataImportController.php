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
            $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
            $file = $request->file('import_file');

            if ($request->import_type === 'third_parties') {
                if (class_exists(ThirdPartiesImport::class)) {
                    $import = new ThirdPartiesImport();
                    Excel::import($import, $file);
                } else {
                    $this->importThirdPartiesFromCsv($file, $companyId);
                }
                return back()->with('success', 'Importação de Clientes e Fornecedores concluída com sucesso!');
            }

            if ($request->import_type === 'products') {
                $this->importProductsFromCsv($file, $companyId);
                return back()->with('success', 'Importação de Artigos e Produtos concluída com sucesso!');
            }

            if ($request->import_type === 'employees') {
                $this->importEmployeesFromCsv($file, $companyId);
                return back()->with('success', 'Importação de Colaboradores concluída com sucesso!');
            }

            return back()->with('error', 'Tipo de importação não suportado.');
        } catch (\Exception $e) {
            return back()->with('error', 'Ocorreu um erro ao processar o ficheiro: ' . $e->getMessage());
        }
    }

    private function importThirdPartiesFromCsv($file, $companyId)
    {
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle, 1000, ';');

        while (($row = fgetcsv($handle, 1000, ';')) !== false) {
            if (empty($row[0])) continue;
            \App\Models\ThirdParty::firstOrCreate(
                ['company_id' => $companyId, 'nif' => $row[1] ?? null],
                [
                    'name' => $row[0],
                    'type' => strtoupper($row[2] ?? 'CL'),
                    'is_customer' => strtoupper($row[2] ?? 'CL') === 'CL',
                    'is_supplier' => strtoupper($row[2] ?? 'CL') === 'FO',
                    'email' => $row[3] ?? null,
                    'phone' => $row[4] ?? null,
                    'address' => $row[5] ?? null,
                ]
            );
        }
        fclose($handle);
    }

    private function importProductsFromCsv($file, $companyId)
    {
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle, 1000, ';');

        while (($row = fgetcsv($handle, 1000, ';')) !== false) {
            if (empty($row[0])) continue;
            \App\Models\Product::firstOrCreate(
                ['company_id' => $companyId, 'code' => $row[0]],
                [
                    'name' => $row[1] ?? $row[0],
                    'unit_price' => (float)($row[2] ?? 0),
                    'cost_price' => (float)($row[3] ?? 0),
                    'stock_qty' => (float)($row[4] ?? 0),
                    'is_inventory' => true
                ]
            );
        }
        fclose($handle);
    }

    private function importEmployeesFromCsv($file, $companyId)
    {
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle, 1000, ';');

        while (($row = fgetcsv($handle, 1000, ';')) !== false) {
            if (empty($row[0])) continue;
            \App\Models\Employee::firstOrCreate(
                ['company_id' => $companyId, 'nif' => $row[1] ?? null],
                [
                    'name' => $row[0],
                    'inss' => $row[2] ?? null,
                    'base_salary' => (float)($row[3] ?? 0),
                    'subsidy_meal' => (float)($row[4] ?? 0),
                    'subsidy_transport' => (float)($row[5] ?? 0),
                    'bank_name' => $row[6] ?? null,
                    'iban' => $row[7] ?? null,
                    'is_active' => true
                ]
            );
        }
        fclose($handle);
    }
}

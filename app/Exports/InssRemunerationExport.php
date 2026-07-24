<?php

namespace App\Exports;

use App\Models\PayrollRun;
use App\Models\PayrollReceipt;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InssRemunerationExport implements FromCollection, WithHeadings, WithMapping
{
    protected $run_id;

    public function __construct($run_id)
    {
        $this->run_id = $run_id;
    }

    public function collection()
    {
        return PayrollReceipt::where('payroll_run_id', $this->run_id)->with('employee')->get();
    }

    public function headings(): array
    {
        return [
            'Nº Inscrição Segurança Social (NISS Empresa)',
            'NISS Trabalhador',
            'Nome Completo',
            'Nº Identificação Fiscale (NIF)',
            'Remuneração Base',
            'Outras Remunerações (Sujeitas)',
            'Total Base de Incidência (INSS)',
            'Taxa Trabalhador (3%)',
            'Taxa Entidade Empregadora (8%)',
            'Total Contribuição (11%)'
        ];
    }

    public function map($receipt): array
    {
        return [
            '1234567890', // NISS da Empresa
            $receipt->employee->social_security_number ?? '0000000000',
            $receipt->employee->first_name . ' ' . $receipt->employee->last_name,
            $receipt->employee->nif ?? '999999999',
            number_format($receipt->base_salary, 2, '.', ''),
            number_format($receipt->inss_base - $receipt->base_salary, 2, '.', ''),
            number_format($receipt->inss_base, 2, '.', ''),
            number_format($receipt->inss_employee, 2, '.', ''),
            number_format($receipt->inss_company, 2, '.', ''),
            number_format($receipt->inss_employee + $receipt->inss_company, 2, '.', '')
        ];
    }
}

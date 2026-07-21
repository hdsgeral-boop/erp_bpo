<?php

namespace App\Exports;

use App\Models\PayrollRun;
use App\Models\PayrollReceipt;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AgtRemunerationExport implements FromCollection, WithHeadings, WithMapping
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
            'NIF da Empresa',
            'NIF do Trabalhador',
            'Nome do Trabalhador',
            'Tipo de Contrato',
            'Vencimento Base',
            'Subsidios Sujeitos a IRT',
            'Subsidios Isentos',
            'Total Remuneração Bruta',
            'Desconto INSS (Trabalhador)',
            'Matéria Coletável (Base IRT)',
            'Imposto Retido (IRT)',
            'Líquido a Receber'
        ];
    }

    public function map($receipt): array
    {
        $details = json_decode($receipt->details, true);
        
        $subTaxaveis = 0;
        $subIsentos = 0;
        
        if (is_array($details)) {
            foreach ($details as $item) {
                if ($item['type'] == 'PROVENTO' && $item['code'] != 'BASE') {
                    // Simplificação: num cenário real usaríamos is_subject_to_irt do item
                    if (isset($item['is_subject_to_irt']) && $item['is_subject_to_irt']) {
                        $subTaxaveis += $item['value'];
                    } else {
                        $subIsentos += $item['value'];
                    }
                }
            }
        }

        // Se o detalhe não tiver as flags, fazemos uma aproximação:
        if ($subTaxaveis == 0 && $subIsentos == 0) {
            $subTaxaveis = $receipt->other_additions;
        }

        return [
            '123456789', // NIF da Empresa Ficticio
            $receipt->employee->nif ?? '999999999',
            $receipt->employee->first_name . ' ' . $receipt->employee->last_name,
            $receipt->employee->contract_type ?? 'TRABALHO POR CONTA DE OUTREM',
            number_format($receipt->base_salary, 2, '.', ''),
            number_format($subTaxaveis, 2, '.', ''),
            number_format($subIsentos, 2, '.', ''),
            number_format($receipt->base_salary + $receipt->other_additions, 2, '.', ''),
            number_format($receipt->inss_employee, 2, '.', ''),
            number_format($receipt->irt_base ?? ($receipt->base_salary + $subTaxaveis - $receipt->inss_employee), 2, '.', ''),
            number_format($receipt->irt, 2, '.', ''),
            number_format($receipt->net_total, 2, '.', '')
        ];
    }
}

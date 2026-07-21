<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ThirdPartiesTemplateExport implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return [
            'Tipo (C=Cliente, F=Fornecedor)',
            'Nome / Razão Social',
            'NIF',
            'Email',
            'Telefone',
            'Endereço'
        ];
    }

    public function collection()
    {
        // Retorna apenas uma coleção vazia, pois é apenas um template
        return collect([]);
    }
}

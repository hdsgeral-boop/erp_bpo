<?php

namespace App\Imports;

use App\Models\ThirdParty;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Validation\Rule;

class ThirdPartiesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
        return new ThirdParty([
            'type'    => strtoupper(trim($row['tipo_ccliente_ffornecedor'])) === 'F' ? 'supplier' : 'customer',
            'name'    => $row['nome_razao_social'],
            'nif'     => $row['nif'],
            'email'   => $row['email'],
            'phone'   => $row['telefone'],
            'address' => $row['endereco'],
            'status'  => 'active'
        ]);
    }

    public function rules(): array
    {
        return [
            // Validações rigorosas e proteção contra NIFs duplicados
            'nif' => [
                'required',
                'string',
                // Aqui reside a resposta à tua escolha (Ignorar duplicados em vez de atualizar)
                Rule::unique('third_parties', 'nif'),
            ],
            'nome_razao_social' => 'required|string|max:255',
            'tipo_ccliente_ffornecedor' => 'required|in:C,F,c,f',
            'email' => 'nullable|email',
        ];
    }
}

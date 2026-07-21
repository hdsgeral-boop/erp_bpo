<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\ChartOfAccount;

class ChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['code' => '11', 'description' => 'Meios Fixos Corpóreos', 'type' => 'I'],
            ['code' => '21', 'description' => 'Mercadorias', 'type' => 'I'],
            ['code' => '31', 'description' => 'Clientes', 'type' => 'I'],
            ['code' => '32', 'description' => 'Fornecedores', 'type' => 'I'],
            ['code' => '43', 'description' => 'Depósitos à Ordem', 'type' => 'I'],
            ['code' => '61', 'description' => 'Vendas', 'type' => 'I'],
            ['code' => '71', 'description' => 'Custo das Mercadorias Vendidas', 'type' => 'I'],
        ];

        foreach ($accounts as $acc) {
            ChartOfAccount::firstOrCreate(
                ['code' => $acc['code']],
                [
                    'company_id' => 1, // Assume company 1 exists
                    'description' => $acc['description'],
                    'type' => $acc['type'],
                    'is_master_data' => false,
                ]
            );
        }
    }
}

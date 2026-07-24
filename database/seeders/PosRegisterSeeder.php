<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\PosRegister;

class PosRegisterSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::where('name', 'not like', '%SISTEMA%')->get();

        foreach ($companies as $company) {
            $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $company->name), 0, 3));
            
            PosRegister::firstOrCreate(
                [
                    'company_id' => $company->id,
                    'name' => "Caixa Principal {$prefix}"
                ],
                [
                    'terminal_id' => "POS-{$prefix}-01",
                    'status' => 'CLOSED',
                    'is_active' => true,
                    'printer_type' => 'browser',
                    'printer_address' => 'Localhost Thermal 80mm'
                ]
            );

            PosRegister::firstOrCreate(
                [
                    'company_id' => $company->id,
                    'name' => "Caixa Secundária {$prefix}"
                ],
                [
                    'terminal_id' => "POS-{$prefix}-02",
                    'status' => 'CLOSED',
                    'is_active' => true,
                    'printer_type' => 'browser',
                    'printer_address' => 'Localhost Thermal 80mm'
                ]
            );
        }
    }
}

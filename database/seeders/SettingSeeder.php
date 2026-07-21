<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'app_name', 'value' => 'ERP Consulvolt', 'type' => 'text', 'group' => 'Geral', 'description' => 'Nome da Aplicação'],
            ['key' => 'company_nif', 'value' => '5000000000', 'type' => 'text', 'group' => 'Geral', 'description' => 'NIF da Empresa Principal'],
            ['key' => 'support_email', 'value' => 'suporte@consulvolt.com', 'type' => 'text', 'group' => 'Geral', 'description' => 'Email de Suporte'],
            
            ['key' => 'currency_symbol', 'value' => 'Kz', 'type' => 'text', 'group' => 'Financeiro', 'description' => 'Símbolo da Moeda Padrão'],
            ['key' => 'default_vat_rate', 'value' => '14', 'type' => 'integer', 'group' => 'Financeiro', 'description' => 'Taxa de IVA Padrão (%)'],
            ['key' => 'invoice_retention', 'value' => '6.5', 'type' => 'text', 'group' => 'Financeiro', 'description' => 'Retenção na Fonte (%)'],
            
            ['key' => 'session_timeout', 'value' => '120', 'type' => 'integer', 'group' => 'Segurança', 'description' => 'Tempo Limite de Sessão (minutos)'],
            ['key' => 'require_2fa', 'value' => '0', 'type' => 'boolean', 'group' => 'Segurança', 'description' => 'Exigir Autenticação de 2 Fatores'],
            ['key' => 'password_expiry', 'value' => '90', 'type' => 'integer', 'group' => 'Segurança', 'description' => 'Expiração de Palavra-passe (dias)'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}

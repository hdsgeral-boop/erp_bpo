<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CleanDemoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clean-demo {--force : Executa sem pedir confirmação}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove todos os dados de teste (seeders) e limpa a base de dados mantendo permissões, tabelas fiscais de IVA/IRT e o Plano de Contas.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('⚠️ Tem a certeza que pretende apagar TODOS os dados de teste para começar com dados reais?')) {
            $this->info('Operação cancelada pelo utilizador.');
            return 0;
        }

        $this->info('Iniciando limpeza segura dos dados de teste...');

        Schema::disableForeignKeyConstraints();

        $tables = [
            'sale_items',
            'sales',
            'purchase_items',
            'purchase_invoices',
            'purchase_deliveries',
            'purchase_orders',
            'purchase_requests',
            'stock_movements',
            'warehouse_stocks',
            'inventory_session_lines',
            'inventory_sessions',
            'receipt_items',
            'receipts',
            'bank_statement_lines',
            'reconciliations',
            'payroll_items',
            'payroll_receipts',
            'payroll_runs',
            'attendances',
            'absences',
            'overtimes',
            'contracts',
            'employees',
            'fixed_assets',
            'asset_depreciations',
            'asset_movements',
            'journal_lines',
            'journals',
            'products',
            'product_categories',
            'third_parties',
            'warehouses',
            'treasury_accounts',
            'document_series',
            'documents',
            'attachments',
            'ai_messages',
            'ai_conversations'
        ];

        foreach ($tables as $t) {
            if (Schema::hasTable($t)) {
                DB::table($t)->truncate();
                $this->line(" ✓ Tabela '{$t}' limpa.");
            }
        }

        Schema::enableForeignKeyConstraints();

        $this->info('✅ Limpeza concluída com sucesso! O sistema está pronto para receber dados reais dos utilizadores.');
        return 0;
    }
}

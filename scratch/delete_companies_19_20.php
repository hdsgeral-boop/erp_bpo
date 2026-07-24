<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\User;

echo "Eliminando Empresas ID 19 e ID 20 do Banco de Dados...\n";

// Tables with company_id column
$tables = [
    'sales', 'sale_items', 'purchase_invoices', 'purchase_items', 'purchase_orders', 'purchase_requests',
    'third_parties', 'products', 'warehouses', 'warehouse_stocks', 'pos_registers', 'pos_sessions',
    'employees', 'contracts', 'payroll_runs', 'payroll_receipts', 'treasury_accounts', 'receipts',
    'fixed_assets', 'asset_categories', 'chart_of_accounts', 'journals', 'journal_lines',
    'departments', 'document_series', 'company_user'
];

foreach ($tables as $table) {
    try {
        $count = DB::table($table)->whereIn('company_id', [19, 20])->delete();
        echo "   -> Eliminados {$count} registos da tabela '{$table}' para empresas 19 e 20.\n";
    } catch (\Exception $e) {
        // Table might not have company_id or might fail gracefully
    }
}

// Remove pivot records
DB::table('company_user')->whereIn('company_id', [19, 20])->delete();

// Delete companies
$delCount = Company::whereIn('id', [19, 20])->delete();
echo "   -> Eliminadas {$delCount} empresas da tabela 'companies'.\n";

// Update Celso Baptista user company_id to 1 (WSTB)
$celso = User::where('email', 'celso@consulvolt.com')->first();
if ($celso) {
    $celso->company_id = 1;
    $celso->save();

    // Attach all operational companies to Celso
    $opCompanies = Company::where('name', 'not like', '%SISTEMA%')->get();
    $celso->companies()->sync($opCompanies->pluck('id')->toArray());
    echo "   -> Usuário Celso Baptista atualizado para empresa principal ID 1 (WSTB) e associado a " . $opCompanies->count() . " empresas operacionais.\n";
}

echo "\nConcluído com sucesso!\n";

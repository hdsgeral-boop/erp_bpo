<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\User;

echo "=========================================\n";
echo "1. LIST OF COMPANIES (companies table)\n";
echo "=========================================\n";
$companies = Company::all();
foreach ($companies as $c) {
    echo "ID: {$c->id} | Name: {$c->name} | NIF: {$c->nif} | MasterData: " . ($c->is_master_data ? 'YES' : 'NO') . "\n";
}

echo "\n=========================================\n";
echo "2. LIST OF USERS & THEIR COMPANIES\n";
echo "=========================================\n";
$users = User::with('company', 'companies', 'roles')->get();
foreach ($users as $u) {
    $rolesStr = implode(', ', $u->roles->pluck('name')->toArray());
    $assignedComps = implode(', ', $u->companies->pluck('name')->toArray());
    echo "ID: {$u->id} | Name: {$u->name} | Email: {$u->email} | Main Company: {$u->company?->name} (ID {$u->company_id})\n";
    echo "  Roles: [{$rolesStr}]\n";
    echo "  Assigned Companies: [{$assignedComps}]\n\n";
}

echo "=========================================\n";
echo "3. DATA COUNTS PER OPERATIONAL COMPANY\n";
echo "=========================================\n";
$opCompanies = Company::where('name', 'not like', '%SISTEMA%')->get();
foreach ($opCompanies as $comp) {
    echo "--- Company ID {$comp->id}: {$comp->name} ---\n";
    echo "  Customers/Suppliers (third_parties): " . DB::table('third_parties')->where('company_id', $comp->id)->count() . "\n";
    echo "  Products (products): " . DB::table('products')->where('company_id', $comp->id)->count() . "\n";
    echo "  Warehouses (warehouses): " . DB::table('warehouses')->where('company_id', $comp->id)->count() . "\n";
    echo "  Sales Documents (sales): " . DB::table('sales')->where('company_id', $comp->id)->count() . "\n";
    echo "  POS Registers (pos_registers): " . DB::table('pos_registers')->where('company_id', $comp->id)->count() . "\n";
    echo "  Employees (employees): " . DB::table('employees')->where('company_id', $comp->id)->count() . "\n";
    echo "  Contracts (contracts): " . DB::table('contracts')->where('company_id', $comp->id)->count() . "\n";
    echo "  Treasury Accounts (treasury_accounts): " . DB::table('treasury_accounts')->where('company_id', $comp->id)->count() . "\n";
    echo "  Document Series (document_series): " . DB::table('document_series')->where('company_id', $comp->id)->count() . "\n\n";
}

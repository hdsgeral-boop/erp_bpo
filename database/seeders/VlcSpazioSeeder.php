<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use App\Models\Position;
use App\Models\ChartOfAccount;
use App\Models\Journal;
use App\Models\JournalLine;
use App\Models\ThirdParty;
use App\Models\Warehouse;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\WarehouseStock;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseItem;
use App\Models\Employee;
use App\Models\Contract;
use App\Models\PayrollRun;
use App\Models\PayrollReceipt;
use App\Models\FixedAsset;
use App\Models\AssetCategory;
use App\Models\TreasuryAccount;
use App\Models\Receipt;
use App\Models\BankStatementLine;
use Spatie\Permission\Models\Role;

class VlcSpazioSeeder extends Seeder
{
    public function run(): void
    {
        echo "Iniciando Seeder para Celso Baptista e Empresas VLC / Spazio...\n";

        // 1. Criar/Garantir Perfil Admin
        $roleAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

        // 2. Criar Usuário Celso Baptista
        $celso = User::firstOrCreate(
            ['email' => 'celso@consulvolt.com'],
            [
                'name' => 'Celso Baptista',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $celso->assignRole($roleAdmin);

        // Reset PostgreSQL sequences safely for all tables
        $tables = [
            'companies', 'users', 'chart_of_accounts', 'journals', 'journal_lines',
            'third_parties', 'warehouses', 'product_categories', 'products',
            'warehouse_stocks', 'sales', 'sale_items', 'purchase_invoices',
            'purchase_items', 'employees', 'payroll_runs', 'payroll_receipts',
            'asset_categories', 'fixed_assets', 'treasury_accounts', 'departments'
        ];

        foreach ($tables as $t) {
            try {
                DB::statement("SELECT setval(pg_get_serial_sequence('{$t}', 'id'), coalesce((SELECT MAX(id) FROM {$t}), 0) + 1, false);");
            } catch (\Exception $e) {}
        }

        // 1. Criar/Garantir Perfil Admin
        $roleAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

        // 2. Criar Usuário Celso Baptista
        $celso = User::firstOrCreate(
            ['email' => 'celso@consulvolt.com'],
            [
                'name' => 'Celso Baptista',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $celso->assignRole($roleAdmin);

        // 3. Criar Empresa 1: VLC
        $vlc = Company::where('name', 'like', '%VLC%')->orWhere('nif', '5418920192')->first();
        if (!$vlc) {
            $vlc = Company::create([
                'name' => 'VLC — Volt Light Consulvolt, Lda',
                'nif' => '5418920192',
                'province' => 'Luanda',
                'municipality' => 'Belas',
                'commune' => 'Talatona',
                'is_master_data' => false
            ]);
        }

        // 4. Criar Empresa 2: Spazio
        $spazio = Company::where('name', 'like', '%Spazio%')->orWhere('nif', '5419820381')->first();
        if (!$spazio) {
            $spazio = Company::create([
                'name' => 'Spazio — Spazio Design & Serviços, Lda',
                'nif' => '5419820381',
                'province' => 'Luanda',
                'municipality' => 'Luanda',
                'commune' => 'Maianga',
                'is_master_data' => false
            ]);
        }

        // Associar Celso Baptista a todas as empresas operacionais
        $opCompanies = Company::where('name', 'not like', '%SISTEMA%')->get();
        $celso->companies()->syncWithoutDetaching($opCompanies->pluck('id')->toArray());

        echo "   -> Usuário Celso Baptista associado a " . $opCompanies->count() . " empresas operacionais.\n";

        foreach ($opCompanies as $comp) {
            $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $comp->name), 0, 3));
            
            $this->seedCompanyData($comp, [
                'type' => strtolower($prefix),
                'customers' => ["Cliente A - {$comp->name}", "Cliente B - {$comp->name}", "Cliente C - {$comp->name}"],
                'suppliers' => ["Fornecedor X - {$comp->name}", "Fornecedor Y - {$comp->name}"],
                'products' => [
                    ['code' => "{$prefix}-PRD-01", 'name' => "Serviço Principal {$prefix}", 'price' => 250000],
                    ['code' => "{$prefix}-PRD-02", 'name' => "Artigo de Consumo {$prefix}", 'price' => 45000],
                    ['code' => "{$prefix}-PRD-03", 'name' => "Consultoria Técnica {$prefix}", 'price' => 850000],
                    ['code' => "{$prefix}-PRD-04", 'name' => "Manutenção Preventiva {$prefix}", 'price' => 175000]
                ],
                'employees' => [
                    ['name' => "Operador Principal {$prefix}", 'nif' => rand(100000000, 999999999) . 'LA01', 'role' => 'Gestor Operacional', 'base' => 650000, 'iban' => 'AO06.0006.0000.' . rand(1000, 9999) . '.' . rand(1000, 9999) . '.1011.1'],
                    ['name' => "Assistente {$prefix}", 'nif' => rand(100000000, 999999999) . 'LA02', 'role' => 'Técnico Administrativo', 'base' => 380000, 'iban' => 'AO06.0006.0000.' . rand(1000, 9999) . '.' . rand(1000, 9999) . '.1011.2']
                ],
                'assets' => [
                    ['code' => "{$prefix}-ATV-01", 'name' => "Viatura de Serviço {$prefix}", 'val' => 18000000],
                    ['code' => "{$prefix}-ATV-02", 'name' => "Equipamento Informático {$prefix}", 'val' => 3500000]
                ]
            ]);
        }

        echo "Seeder de empresas concluído com sucesso!\n";
    }

    private function seedCompanyData(Company $company, array $cfg)
    {
        $cId = $company->id;
        echo "   -> Gerando dados fictícios para {$company->name} (ID {$cId})...\n";

        // Plano de Contas básico PGC
        $accList = [
            ['code' => '1', 'name' => 'MEIOS FIXOS E INVESTIMENTOS', 'type' => 'I'],
            ['code' => '11', 'name' => 'Imobilizações Corpóreas', 'type' => 'I'],
            ['code' => '11.1', 'name' => 'Equipamento de Transporte', 'type' => 'M'],
            ['code' => '2', 'name' => 'EXISTÊNCIAS / STOCKS', 'type' => 'I'],
            ['code' => '21', 'name' => 'Mercadorias', 'type' => 'M'],
            ['code' => '3', 'name' => 'TERCEIROS', 'type' => 'I'],
            ['code' => '31', 'name' => 'Clientes', 'type' => 'M'],
            ['code' => '32', 'name' => 'Fornecedores', 'type' => 'M'],
            ['code' => '34', 'name' => 'Estado e Outros Entes Públicos', 'type' => 'M'],
            ['code' => '4', 'name' => 'MEIOS FINANCEIROS LÍQUIDOS', 'type' => 'I'],
            ['code' => '43', 'name' => 'Depósitos à Ordem (Bancos)', 'type' => 'M'],
            ['code' => '45', 'name' => 'Caixa Geral', 'type' => 'M'],
            ['code' => '5', 'name' => 'CAPITAL E RESERVAS', 'type' => 'I'],
            ['code' => '51', 'name' => 'Capital Social', 'type' => 'M'],
            ['code' => '6', 'name' => 'GASTOS E PERDAS', 'type' => 'I'],
            ['code' => '61', 'name' => 'Custo das Mercadorias Vendidas', 'type' => 'M'],
            ['code' => '62', 'name' => 'Gastos com Pessoal', 'type' => 'M'],
            ['code' => '7', 'name' => 'RENDIMENTOS E GANHOS', 'type' => 'I'],
            ['code' => '71', 'name' => 'Vendas e Serviços Prestados', 'type' => 'M'],
        ];

        foreach ($accList as $acc) {
            ChartOfAccount::firstOrCreate(
                ['company_id' => $cId, 'code' => $acc['code']],
                ['description' => $acc['name'], 'type' => $acc['type']]
            );
        }

        // Diários
        $journals = [
            ['code' => 'CX', 'name' => 'Diário de Caixa Geral'],
            ['code' => 'BC', 'name' => 'Diário de Bancos'],
            ['code' => 'VD', 'name' => 'Diário de Vendas'],
            ['code' => 'CP', 'name' => 'Diário de Compras'],
            ['code' => 'OD', 'name' => 'Diário de Operações Diversas']
        ];
        foreach ($journals as $j) {
            Journal::firstOrCreate(
                ['company_id' => $cId, 'code' => $j['code']],
                ['description' => $j['name']]
            );
        }

        // Departamentos
        $deptList = ['Engenharia e Projetos', 'Financeiro & Contabilidade', 'Recursos Humanos', 'Comercial & Vendas'];
        $depts = [];
        foreach ($deptList as $idx => $dName) {
            $code = strtoupper($cfg['type']) . '_' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $dName), 0, 4)) . '_' . ($idx + 1);
            $depts[] = Department::firstOrCreate(
                ['company_id' => $cId, 'name' => $dName],
                ['code' => $code]
            );
        }

        // Armazém
        $wh = Warehouse::firstOrCreate(
            ['company_id' => $cId, 'name' => 'Armazém Central (' . $company->name . ')'],
            ['location' => 'Luanda']
        );

        // Categoria de Produto
        $cat = ProductCategory::firstOrCreate(
            ['company_id' => $cId, 'name' => 'Geral ' . strtoupper($cfg['type'])]
        );

        // Produtos
        $products = [];
        foreach ($cfg['products'] as $pData) {
            $p = Product::firstOrCreate(
                ['company_id' => $cId, 'code' => $pData['code']],
                [
                    'name' => $pData['name'],
                    'category_id' => $cat->id,
                    'unit_price' => $pData['price'],
                    'stock_qty' => 50,
                    'is_inventory' => true
                ]
            );
            $products[] = $p;

            WarehouseStock::firstOrCreate(
                ['warehouse_id' => $wh->id, 'product_id' => $p->id],
                ['stock_qty' => 50]
            );
        }

        // Terceiros (Clientes)
        $custEntities = [];
        foreach ($cfg['customers'] as $idx => $cName) {
            $custEntities[] = ThirdParty::firstOrCreate(
                ['company_id' => $cId, 'name' => $cName],
                [
                    'nif' => '5400' . rand(100000, 999999),
                    'is_customer' => true,
                    'is_supplier' => false,
                    'email' => 'contacto@' . strtolower(str_replace([' ', '.'], '', $cName)) . '.co.ao',
                    'phone' => '+244 923 ' . rand(100000, 999999)
                ]
            );
        }

        // Terceiros (Fornecedores)
        $suppEntities = [];
        foreach ($cfg['suppliers'] as $idx => $sName) {
            $suppEntities[] = ThirdParty::firstOrCreate(
                ['company_id' => $cId, 'name' => $sName],
                [
                    'nif' => '5409' . rand(100000, 999999),
                    'is_customer' => false,
                    'is_supplier' => true,
                    'email' => 'comercial@' . strtolower(str_replace([' ', '.'], '', $sName)) . '.co.ao',
                    'phone' => '+244 934 ' . rand(100000, 999999)
                ]
            );
        }

        // Criar Faturas de Venda fictícias (FT, FR, OR)
        foreach ($custEntities as $i => $cust) {
            $prod = $products[$i % count($products)];
            $qty = rand(2, 10);
            $subtotal = $prod->unit_price * $qty;
            $tax = $subtotal * 0.14;
            $total = $subtotal + $tax;

            $sale = Sale::create([
                'company_id' => $cId,
                'customer_id' => $cust->id,
                'doc_type' => $i % 2 == 0 ? 'FT' : 'FR',
                'doc_number' => strtoupper($cfg['type']) . '-FT ' . date('Y') . '/' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'date' => date('Y-m-d', strtotime("-{$i} days")),
                'total_amount' => $subtotal,
                'total_tax' => $tax,
                'amount_paid' => $i % 2 == 0 ? $total : 0,
                'status' => 'ISSUED'
            ]);

            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $prod->id,
                'quantity' => $qty,
                'unit_price' => $prod->unit_price,
                'tax_amount' => $tax,
                'subtotal' => $subtotal
            ]);
        }

        // Compras (Purchase Invoices)
        foreach ($suppEntities as $i => $supp) {
            $prod = $products[$i % count($products)];
            $qty = rand(5, 20);
            $subtotal = ($prod->unit_price * 0.7) * $qty;
            $tax = $subtotal * 0.14;

            $pi = PurchaseInvoice::create([
                'company_id' => $cId,
                'supplier_id' => $supp->id,
                'invoice_number' => 'FORN-FT-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'date' => date('Y-m-d', strtotime("-{$i} days")),
                'total_amount' => $subtotal,
                'status' => 'ISSUED'
            ]);

            PurchaseItem::create([
                'parent_id' => $pi->id,
                'parent_type' => 'Invoice',
                'product_id' => $prod->id,
                'quantity' => $qty,
                'unit_price' => $prod->unit_price * 0.7
            ]);
        }

        // Funcionários e Processamento Salarial
        $employees = [];
        foreach ($cfg['employees'] as $idx => $eData) {
            $emp = Employee::firstOrCreate(
                ['company_id' => $cId, 'nif' => $eData['nif']],
                [
                    'name' => $eData['name'],
                    'inss' => 'INSS' . rand(100000, 999999),
                    'department_id' => $depts[$idx % count($depts)]->id,
                    'base_salary' => $eData['base'],
                    'subsidy_meal' => 30000,
                    'subsidy_transport' => 35000,
                    'bank_name' => 'BFA',
                    'iban' => $eData['iban'],
                    'is_active' => true
                ]
            );
            $employees[] = $emp;
        }

        // Criar uma PayrollRun (Processamento de Salários) para o mês atual
        $refMonth = date('m-Y');
        $run = PayrollRun::create([
            'company_id' => $cId,
            'reference' => $refMonth,
            'month' => date('m'),
            'year' => date('Y'),
            'status' => 'CLOSED',
            'total_base' => 0,
            'total_net_paid' => 0,
            'total_inss' => 0,
            'total_irt' => 0
        ]);

        $totGross = 0; $totNet = 0; $totInss = 0; $totIrt = 0;
        foreach ($employees as $emp) {
            $base = $emp->base_salary;
            $inssEmp = $base * 0.03;
            $inssComp = $base * 0.08;
            $irt = ($base - $inssEmp) * 0.10;
            $net = ($base + 65000) - ($inssEmp + $irt);

            PayrollReceipt::create([
                'payroll_run_id' => $run->id,
                'employee_id' => $emp->id,
                'base_salary' => $base,
                'inss_base' => $base,
                'inss_employee' => $inssEmp,
                'inss_company' => $inssComp,
                'irt' => $irt,
                'net_total' => $net
            ]);

            $totGross += ($base + 65000);
            $totNet += $net;
            $totInss += ($inssEmp + $inssComp);
            $totIrt += $irt;
        }

        $run->update([
            'total_base' => $totGross,
            'total_net_paid' => $totNet,
            'total_inss' => $totInss,
            'total_irt' => $totIrt
        ]);

        // Ativos Fixos
        $assetCat = AssetCategory::firstOrCreate(
            ['name' => 'Equipamentos e Veículos ' . strtoupper($cfg['type'])],
            ['depreciation_rate' => 20]
        );

        foreach ($cfg['assets'] as $aData) {
            FixedAsset::firstOrCreate(
                ['code' => $aData['code']],
                [
                    'company_id' => $cId,
                    'name' => $aData['name'],
                    'category_id' => $assetCat->id,
                    'purchase_date' => date('Y-01-15'),
                    'purchase_value' => $aData['val'],
                    'status' => 'active'
                ]
            );
        }

        // Tesouraria (Contas Bancárias)
        $tAccount = TreasuryAccount::firstOrCreate(
            ['company_id' => $cId, 'name' => 'Conta Ordem BFA ' . $company->name],
            [
                'currency' => 'AOA',
                'initial_balance' => 15000000,
                'current_balance' => 15000000
            ]
        );

        // Movimento Contábil Geral (Journal Lines)
        $jVendas = Journal::where('company_id', $cId)->where('code', 'VD')->first();
        if ($jVendas) {
            JournalLine::create([
                'company_id' => $cId,
                'journal_id' => $jVendas->id,
                'doc_date' => date('Y-m-d'),
                'entry_date' => date('Y-m-d'),
                'doc_number' => 'VD-001',
                'account_code' => '31',
                'description' => 'Faturação Geral ' . $company->name,
                'type_dc' => 'D',
                'value' => 2500000
            ]);
            JournalLine::create([
                'company_id' => $cId,
                'journal_id' => $jVendas->id,
                'doc_date' => date('Y-m-d'),
                'entry_date' => date('Y-m-d'),
                'doc_number' => 'VD-001',
                'account_code' => '71',
                'description' => 'Vendas de Mercadorias e Serviços',
                'type_dc' => 'C',
                'value' => 2500000
            ]);
        }
    }
}

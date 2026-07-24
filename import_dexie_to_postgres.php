<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$jsonPath = 'C:\\xampp\\htdocs\\ERP\\Dados Antigos\\wstb_payroll_backup_2026-05-23.json';

if (!file_exists($jsonPath)) {
    die("Erro: Arquivo JSON de backup não encontrado em: {$jsonPath}\n");
}

echo "Lendo arquivo JSON (11MB)...\n";
$jsonStr = file_get_contents($jsonPath);
$decoded = json_decode($jsonStr, true);

if (!$decoded || !isset($decoded['data']['data'])) {
    die("Erro: Formato JSON inválido ou corrompido.\n");
}

$tablesData = $decoded['data']['data'];

// Desativar todas as restrições de chaves estrangeiras no PostgreSQL temporariamente
DB::statement("SET session_replication_role = 'replica';");

// Alterar a coluna logo em companies para TEXT caso ela seja VARCHAR(255) para suportar base64
DB::statement("ALTER TABLE companies ALTER COLUMN logo TYPE TEXT;");

// Alterar colunas de descrição/referência para TEXT para evitar erros de truncamento
DB::statement("ALTER TABLE journal_lines ALTER COLUMN description TYPE TEXT;");
DB::statement("ALTER TABLE journal_lines ALTER COLUMN reference TYPE TEXT;");
DB::statement("ALTER TABLE bank_statement_lines ALTER COLUMN description TYPE TEXT;");

// Ajustar chaves únicas de chart_of_accounts e products para serem por empresa
try {
    DB::statement("ALTER TABLE chart_of_accounts DROP CONSTRAINT IF EXISTS chart_of_accounts_code_unique;");
} catch (\Exception $e) {}
try {
    DB::statement("ALTER TABLE chart_of_accounts ADD CONSTRAINT chart_of_accounts_company_id_code_unique UNIQUE (company_id, code);");
} catch (\Exception $e) {}

try {
    DB::statement("ALTER TABLE products DROP CONSTRAINT IF EXISTS products_code_unique;");
} catch (\Exception $e) {}
try {
    DB::statement("ALTER TABLE products ADD CONSTRAINT products_company_id_code_unique UNIQUE (company_id, code);");
} catch (\Exception $e) {}

// Arrays para garantir unicidade
$usedNifs = [];
$usedPositionCodes = [];
$usedProductCodes = [];

// Lista de tabelas a importar com mapeamentos específicos
$tableMappings = [
    'companies' => [
        'target' => 'companies',
        'map' => function($row) use (&$usedNifs) {
            $nif = trim($row['nif'] ?? '000000000');
            if (empty($nif)) {
                $nif = '000000000';
            }
            if (in_array($nif, $usedNifs)) {
                $nif = $nif . '_' . $row['id'];
            }
            $usedNifs[] = $nif;
            return [
                'id' => $row['id'],
                'name' => $row['name'],
                'nif' => $nif,
                'logo' => $row['logo'] ?? null,
                'province' => $row['province'] ?? null,
                'municipality' => $row['municipality'] ?? null,
                'commune' => $row['commune'] ?? null,
                'is_master_data' => (bool)($row['is_master_data'] ?? false),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
    ],
    'roles' => [
        'target' => 'positions',
        'map' => function($row) use (&$usedPositionCodes) {
            $baseCode = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $row['name'])) ?: 'POS';
            $code = substr($baseCode, 0, 20);
            if (in_array($code, $usedPositionCodes)) {
                $code = substr($code, 0, 15) . '_' . $row['id'];
            }
            $usedPositionCodes[] = $code;
            return [
                'id' => $row['id'],
                'code' => $code,
                'title' => $row['name'],
                'description' => 'Cargo importado historicamente do backup.',
                'department_id' => null,
                'is_management' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
    ],
    'employees' => [
        'target' => 'employees',
        'map' => function($row) {
            $names = explode(' ', trim($row['name']), 2);
            $firstName = $names[0] ?? 'Funcionário';
            $lastName = $names[1] ?? '';
            return [
                'id' => $row['id'],
                'company_id' => $row['company_id'],
                'name' => $row['name'],
                'first_name' => $firstName,
                'last_name' => $lastName,
                'nif' => $row['nif'] ?? null,
                'inss' => $row['inss'] ?? null,
                'position_id' => $row['role_id'] ?? null,
                'is_active' => ($row['status'] ?? 'Ativo') === 'Ativo',
                'work_days' => $row['work_days'] ?? 22,
                'is_retired' => (bool)($row['is_retired'] ?? false),
                'is_master_data' => (bool)($row['is_master_data'] ?? false),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
    ],
    'infotypes' => [
        'target' => 'infotypes',
        'map' => function($row) {
            return [
                'id' => $row['id'],
                'company_id' => $row['company_id'],
                'type' => $row['type'] ?? 'PROVENTO',
                'name' => $row['name'],
                'inss' => (bool)($row['inss'] ?? false),
                'irt' => $row['irt'] ?? 'false',
                'is_master_data' => (bool)($row['is_master_data'] ?? false),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
    ],
    'contracts' => [
        'target' => 'contracts',
        'map' => function($row) {
            $infotypeId = $row['infotype_id'] ?? null;
            if (!$infotypeId) {
                $firstInfotype = DB::table('infotypes')->where('company_id', $row['company_id'])->first();
                $infotypeId = $firstInfotype ? $firstInfotype->id : 1;
            }
            return [
                'id' => $row['id'],
                'company_id' => $row['company_id'],
                'employee_id' => $row['employee_id'],
                'infotype_id' => $infotypeId,
                'value_per_day' => $row['value_per_day'] ?? 0,
                'contract_days_month' => $row['contract_days_month'] ?? 22,
                'start_date' => $row['start_date'] ?? now()->toDateString(),
                'end_date' => $row['end_date'] ?? null,
                'status' => $row['status'] ?? 'Ativo',
                'is_master_data' => (bool)($row['is_master_data'] ?? false),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
    ],
    'chart_of_accounts' => [
        'target' => 'chart_of_accounts',
        'columns' => ['id', 'company_id', 'code', 'description', 'type', 'is_master_data'],
        'bools' => ['is_master_data'],
    ],
    'journals' => [
        'target' => 'journals',
        'columns' => ['id', 'company_id', 'code', 'description', 'is_master_data'],
        'bools' => ['is_master_data'],
    ],
    'journal_lines' => [
        'target' => 'journal_lines',
        'map' => function($row) {
            $reconId = $row['reconciliation_id'] ?? null;
            if ($reconId !== null && !is_numeric($reconId)) {
                $reconId = null;
            }
            return [
                'id' => $row['id'],
                'company_id' => $row['company_id'],
                'journal_id' => $row['journal_id'],
                'doc_date' => $row['doc_date'] ?? null,
                'entry_date' => $row['entry_date'] ?? null,
                'reference' => $row['reference'] ?? null,
                'doc_number' => $row['doc_number'] ?? null,
                'value' => $row['value'] ?? 0,
                'type_dc' => $row['type_dc'] ?? 'D',
                'account_code' => $row['account_code'],
                'third_party_id' => $row['third_party_id'] ?? null,
                'description' => $row['description'] ?? null,
                'reconciliation_id' => $reconId,
                'is_master_data' => (bool)($row['is_master_data'] ?? false),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
    ],
    'third_parties' => [
        'target' => 'third_parties',
        'map' => function($row) {
            $isCustomer = ($row['type'] ?? '') === 'CLIENTE';
            $isSupplier = ($row['type'] ?? '') === 'FORNECEDOR';
            return [
                'id' => $row['id'],
                'company_id' => $row['company_id'],
                'nif' => $row['nif'] ?? null,
                'name' => $row['name'],
                'account_code' => $row['account_code'] ?? null,
                'is_customer' => $isCustomer,
                'is_supplier' => $isSupplier,
                'is_active' => true,
                'is_master_data' => (bool)($row['is_master_data'] ?? false),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
    ],
    'treasury_documents' => [
        'target' => 'treasury_documents',
        'columns' => ['id', 'company_id', 'type', 'doc_date', 'account_fin', 'description', 'total_value', 'status', 'reference', 'is_master_data'],
        'bools' => ['is_master_data'],
    ],
    'bank_statement_lines' => [
        'target' => 'bank_statement_lines',
        'map' => function($row) {
            $reconId = $row['reconciliation_id'] ?? null;
            if ($reconId !== null && !is_numeric($reconId)) {
                $reconId = null;
            }
            return [
                'id' => $row['id'],
                'company_id' => $row['company_id'],
                'account_code' => $row['account_code'],
                'date' => $row['date'],
                'reference' => $row['reference'] ?? null,
                'description' => $row['description'] ?? '',
                'value' => $row['value'] ?? 0,
                'type_dc' => $row['type_dc'] ?? 'D',
                'status' => $row['status'] ?? 'PENDING',
                'reconciliation_id' => $reconId,
                'is_master_data' => (bool)($row['is_master_data'] ?? false),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
    ],
    'reconciliations' => [
        'target' => 'reconciliations',
        'map' => function($row) {
            return [
                'id' => $row['id'],
                'company_id' => $row['company_id'],
                'reconciliation_date' => $row['date'] ?? now()->toDateString(),
                'status' => 'Concluído',
                'opening_balance' => 0,
                'closing_balance' => $row['total_value'] ?? 0,
                'account_code' => $row['account_code'] ?? '12',
                'is_master_data' => (bool)($row['is_master_data'] ?? false),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
    ],
    'products' => [
        'target' => 'products',
        'map' => function($row) use (&$usedProductCodes) {
            $code = trim($row['code'] ?? 'PROD_' . $row['id']);
            if (empty($code)) {
                $code = 'PROD_' . $row['id'];
            }
            if (in_array($code, $usedProductCodes)) {
                $code = $code . '_' . $row['id'];
            }
            $usedProductCodes[] = $code;
            return [
                'id' => $row['id'],
                'company_id' => $row['company_id'],
                'code' => $code,
                'name' => $row['name'],
                'unit_price' => $row['unit_price'] ?? 0,
                'tax_rate' => $row['tax_rate'] ?? 0,
                'account_code' => $row['account_code'] ?? null,
                'account_iva' => $row['account_iva'] ?? null,
                'account_iva_liquidado' => $row['account_iva_liquidado'] ?? null,
                'account_iva_dedutivel' => $row['account_iva_dedutivel'] ?? null,
                'stock_qty' => $row['stock_qty'] ?? 0,
                'category_id' => $row['category_id'] ?? null,
                'is_inventory' => (bool)($row['is_inventory'] ?? true),
                'is_master_data' => (bool)($row['is_master_data'] ?? false),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
    ],
    'product_categories' => [
        'target' => 'product_categories',
        'columns' => ['id', 'company_id', 'name', 'is_master_data'],
        'bools' => ['is_master_data'],
    ],
    'sales' => [
        'target' => 'sales',
        'columns' => ['id', 'company_id', 'customer_id', 'doc_type', 'doc_number', 'date', 'status', 'is_posted', 'related_doc_id', 'is_master_data'],
        'bools' => ['is_posted', 'is_master_data'],
    ],
    'sale_items' => [
        'target' => 'sale_items',
        'columns' => ['id', 'sale_id', 'product_id', 'quantity', 'billed_qty', 'delivered_qty', 'purchase_request_id', 'is_master_data'],
        'bools' => ['is_master_data'],
    ],
    'receipts' => [
        'target' => 'receipts',
        'map' => function($row) {
            return [
                'id' => $row['id'],
                'company_id' => $row['company_id'],
                'third_party_id' => $row['customer_id'] ?? null,
                'doc_number' => $row['receipt_number'] ?? 'REC_' . $row['id'],
                'date' => $row['date'] ?? now()->toDateString(),
                'total_amount' => $row['total_amount'] ?? 0,
                'payment_method' => $row['payment_method'] ?? 'Dinheiro',
                'bank_id' => $row['bank_id'] ?? null,
                'is_posted' => (bool)($row['is_posted'] ?? false),
                'payment_reference' => $row['payment_reference'] ?? null,
                'is_master_data' => (bool)($row['is_master_data'] ?? false),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
    ],
    'receipt_items' => [
        'target' => 'receipt_items',
        'columns' => ['id', 'receipt_id', 'sale_id', 'amount_paid', 'is_master_data'],
        'bools' => ['is_master_data'],
    ],
    'purchase_requests' => [
        'target' => 'purchase_requests',
        'columns' => ['id', 'company_id', 'requester_name', 'date', 'status', 'source_sale_id', 'is_master_data'],
        'bools' => ['is_master_data'],
    ],
    'purchase_quotes' => [
        'target' => 'purchase_quotes',
        'columns' => ['id', 'company_id', 'request_id', 'supplier_id', 'reference', 'total_amount', 'status', 'is_master_data'],
        'bools' => ['is_master_data'],
    ],
    'purchase_orders' => [
        'target' => 'purchase_orders',
        'columns' => ['id', 'company_id', 'quote_id', 'supplier_id', 'order_number', 'date', 'status', 'source_sale_id', 'is_master_data'],
        'bools' => ['is_master_data'],
    ],
    'purchase_deliveries' => [
        'target' => 'purchase_deliveries',
        'columns' => ['id', 'company_id', 'order_id', 'delivery_number', 'date', 'status', 'is_posted', 'is_validated', 'warehouse_id', 'is_master_data'],
        'bools' => ['is_posted', 'is_validated', 'is_master_data'],
    ],
    'purchase_invoices' => [
        'target' => 'purchase_invoices',
        'columns' => ['id', 'company_id', 'order_id', 'supplier_id', 'invoice_number', 'date', 'total_amount', 'status', 'is_posted', 'is_master_data'],
        'bools' => ['is_posted', 'is_master_data'],
    ],
    'purchase_items' => [
        'target' => 'purchase_items',
        'columns' => ['id', 'parent_id', 'parent_type', 'product_id', 'quantity', 'unit_price', 'received_qty', 'is_master_data'],
        'bools' => ['is_master_data'],
    ],
    'warehouses' => [
        'target' => 'warehouses',
        'columns' => ['id', 'company_id', 'name', 'location', 'is_master_data'],
        'bools' => ['is_master_data'],
    ],
    'inventory_movements' => [
        'target' => 'inventory_movements',
        'columns' => ['id', 'company_id', 'product_id', 'warehouse_id', 'date', 'type', 'is_master_data'],
        'bools' => ['is_master_data'],
    ],
    'delivery_notes' => [
        'target' => 'delivery_notes',
        'columns' => ['id', 'company_id', 'doc_number', 'date', 'type', 'warehouse_id', 'receiving_area', 'status', 'is_master_data'],
        'bools' => ['is_master_data'],
    ],
    'fixed_assets' => [
        'target' => 'fixed_assets',
        'map' => function($row) {
            return [
                'id' => $row['id'],
                'category_id' => $row['category_id'] ?? null,
                'code' => $row['code'] ?? 'ASSET_' . $row['id'],
                'name' => 'Ativo Importado #' . ($row['code'] ?? $row['id']),
                'purchase_date' => $row['acquisition_date'] ?? now()->toDateString(),
                'purchase_value' => 0,
                'residual_value' => 0,
                'useful_life_years' => 5,
                'status' => $row['status'] ?? 'Ativo',
                'vendor_id' => $row['supplier_id'] ?? null,
                'company_id' => $row['company_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
    ],
    'asset_categories' => [
        'target' => 'asset_categories',
        'columns' => ['id', 'company_id', 'name', 'account_expense', 'account_accumulated', 'is_master_data'],
        'bools' => ['is_master_data'],
    ],
    'asset_depreciations' => [
        'target' => 'asset_depreciations',
        'columns' => ['id', 'company_id', 'asset_id', 'period_id', 'is_posted', 'is_master_data'],
        'bools' => ['is_posted', 'is_master_data'],
    ],
    'accounting_maps' => [
        'target' => 'accounting_payroll_maps',
        'map' => function($row) {
            return [
                'id' => $row['id'],
                'company_id' => $row['company_id'],
                'type' => 'ITEM',
                'payroll_item_id' => null,
                'debit_account' => $row['account_number'] ?? null,
                'credit_account' => null,
                'description' => "Mapa importado do backup: Infotype #" . ($row['infotype_id'] ?? 'N/A') . ", Org Type #" . ($row['org_type_id'] ?? 'N/A'),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
    ],
    'users' => [
        'target' => 'users',
        'map' => function($row) {
            $email = $row['username'] . '@consulvolt.com';
            $exists = DB::table('users')->where('email', $email)->exists();
            if ($exists) {
                return null;
            }
            return [
                'id' => $row['id'],
                'name' => $row['username'],
                'email' => $email,
                'password' => $row['password'] ?? bcrypt('admin123'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
    ],
];

// Limpar tabelas de destino para evitar conflitos de ID (exceto users)
$clearedTables = [];
foreach ($tableMappings as $oldTable => $config) {
    $targetTable = $config['target'];
    if ($targetTable === 'users') {
        continue;
    }
    if (!in_array($targetTable, $clearedTables)) {
        echo "Limpando tabela: {$targetTable}...\n";
        DB::table($targetTable)->delete();
        $clearedTables[] = $targetTable;
    }
}
// Limpar também tabelas de folha de pagamento derivadas
DB::table('payroll_receipts')->delete();
DB::table('payroll_runs')->delete();

// Importação das tabelas diretas mapeadas
foreach ($tablesData as $tableInfo) {
    $oldTable = $tableInfo['tableName'];
    $rows = $tableInfo['rows'];
    
    if (!isset($tableMappings[$oldTable])) {
        continue;
    }
    
    $config = $tableMappings[$oldTable];
    $targetTable = $config['target'];
    
    echo "Importando tabela {$oldTable} -> {$targetTable} (" . count($rows) . " registros)...\n";
    
    $batch = [];
    foreach ($rows as $row) {
        $dataToInsert = [];
        
        if (isset($config['map'])) {
            $dataToInsert = $config['map']($row);
            if ($dataToInsert === null) {
                continue;
            }
        } else {
            foreach ($config['columns'] as $col) {
                $val = $row[$col] ?? null;
                if (isset($config['bools']) && in_array($col, $config['bools'])) {
                    $val = (bool)$val;
                }
                $dataToInsert[$col] = $val;
            }
            $dataToInsert['created_at'] = now();
            $dataToInsert['updated_at'] = now();
        }
        
        $batch[] = $dataToInsert;
        
        if (count($batch) >= 500) {
            try {
                DB::table($targetTable)->insert($batch);
            } catch (\Exception $e) {
                echo "ERRO ao inserir lote na tabela {$targetTable}: " . $e->getMessage() . "\n";
                die();
            }
            $batch = [];
        }
    }
    
    if (count($batch) > 0) {
        try {
            DB::table($targetTable)->insert($batch);
        } catch (\Exception $e) {
            echo "ERRO ao inserir lote final na tabela {$targetTable}: " . $e->getMessage() . "\n";
            die();
        }
    }
}

// -------------------------------------------------------------
// PROCESSAMENTO DE DADOS COMPLEXOS: FOLHA DE PAGAMENTO
// -------------------------------------------------------------
echo "Reconstruindo históricos de Folhas de Pagamento (payroll_runs & payroll_receipts)...\n";

$periodsData = collect();
$entriesData = collect();
$infotypesList = collect();

foreach ($tablesData as $tableInfo) {
    if ($tableInfo['tableName'] === 'payroll_periods') {
        $periodsData = collect($tableInfo['rows']);
    } elseif ($tableInfo['tableName'] === 'payroll_entries') {
        $entriesData = collect($tableInfo['rows']);
    } elseif ($tableInfo['tableName'] === 'infotypes') {
        $infotypesList = collect($tableInfo['rows']);
    }
}

// Criar Payroll Runs
foreach ($periodsData as $period) {
    $parts = explode('-', $period['month_year']);
    $month = (int)($parts[0] ?? 5);
    $year = (int)($parts[1] ?? 2026);
    
    DB::table('payroll_runs')->insert([
        'id' => $period['id'],
        'company_id' => $period['company_id'],
        'reference' => 'RUN-' . $period['month_year'],
        'month' => $month,
        'year' => $year,
        'status' => $period['status'] === 'CONCLUIDO' ? 'FECHADO' : 'ABERTO',
        'total_base' => 0,
        'total_additions' => 0,
        'total_deductions' => 0,
        'total_inss' => 0,
        'total_irt' => 0,
        'total_net_paid' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

// Criar Payroll Receipts agrupando por period_id e employee_id
$groupedEntries = $entriesData->groupBy(function($entry) {
    return $entry['period_id'] . '_' . $entry['employee_id'];
});

foreach ($groupedEntries as $key => $entries) {
    $first = $entries->first();
    $periodId = $first['period_id'];
    $employeeId = $first['employee_id'];
    
    $baseSalary = 0;
    $otherAdditions = 0;
    $otherDeductions = 0;
    $inssEmployee = 0;
    $inssCompany = 0;
    $irt = 0;
    
    $details = [];
    
    foreach ($entries as $entry) {
        $infotype = $infotypesList->firstWhere('id', $entry['infotype_id']);
        $infoName = $infotype ? $infotype['name'] : 'Item Desconhecido';
        $infoType = $infotype ? $infotype['type'] : 'PROVENTO';
        $value = (float)($entry['value'] ?? 0);
        
        $details[] = [
            'infotype_id' => $entry['infotype_id'],
            'name' => $infoName,
            'value' => $value,
            'type' => $infoType,
        ];
        
        if ($infoType === 'PROVENTO') {
            if (stripos($infoName, 'base') !== false || stripos($infoName, 'vencimento') !== false) {
                $baseSalary += $value;
            } else {
                $otherAdditions += $value;
            }
        } else {
            if (stripos($infoName, 'inss') !== false) {
                $inssEmployee += $value;
                $inssCompany += $value * 2.66; // estimativa patronal
            } elseif (stripos($infoName, 'irt') !== false) {
                $irt += $value;
            } else {
                $otherDeductions += $value;
            }
        }
    }
    
    $netTotal = ($baseSalary + $otherAdditions) - ($inssEmployee + $irt + $otherDeductions);
    
    DB::table('payroll_receipts')->insert([
        'payroll_run_id' => $periodId,
        'employee_id' => $employeeId,
        'base_salary' => $baseSalary,
        'other_additions' => $otherAdditions,
        'inss_base' => $baseSalary,
        'inss_employee' => $inssEmployee,
        'inss_company' => $inssCompany,
        'irt' => $irt,
        'other_deductions' => $otherDeductions,
        'net_total' => $netTotal,
        'details' => json_encode($details),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

// Atualizar Totais nas Runs
$runs = DB::table('payroll_runs')->get();
foreach ($runs as $run) {
    $receipts = DB::table('payroll_receipts')->where('payroll_run_id', $run->id)->get();
    DB::table('payroll_runs')->where('id', $run->id)->update([
        'total_base' => $receipts->sum('base_salary'),
        'total_additions' => $receipts->sum('other_additions'),
        'total_deductions' => $receipts->sum('other_deductions') + $receipts->sum('inss_employee') + $receipts->sum('irt'),
        'total_inss' => $receipts->sum('inss_employee'),
        'total_irt' => $receipts->sum('irt'),
        'total_net_paid' => $receipts->sum('net_total'),
    ]);
}

// Restaurar chaves estrangeiras no PostgreSQL
DB::statement("SET session_replication_role = 'origin';");

// -------------------------------------------------------------
// RESETANDO SEQUENCIAS DE AUTO-INCREMENT DO POSTGRESQL
// -------------------------------------------------------------
echo "Resetando sequências de ID no PostgreSQL...\n";
foreach ($clearedTables as $tableName) {
    $seqCheck = DB::select("SELECT pg_get_serial_sequence('{$tableName}', 'id') as seq");
    $seq = $seqCheck[0]->seq ?? null;
    if ($seq) {
        $maxIdCheck = DB::select("SELECT MAX(id) as max_id FROM {$tableName}");
        $maxId = $maxIdCheck[0]->max_id ?? 1;
        DB::statement("SELECT setval('{$seq}', {$maxId});");
    }
}
// Resetar também para tabelas de folha de pagamento derivadas e users
$seqCheck = DB::select("SELECT pg_get_serial_sequence('payroll_runs', 'id') as seq");
$seq = $seqCheck[0]->seq ?? null;
if ($seq) {
    $maxIdCheck = DB::select("SELECT MAX(id) as max_id FROM payroll_runs");
    $maxId = $maxIdCheck[0]->max_id ?? 1;
    DB::statement("SELECT setval('{$seq}', {$maxId});");
}

$seqCheck = DB::select("SELECT pg_get_serial_sequence('payroll_receipts', 'id') as seq");
$seq = $seqCheck[0]->seq ?? null;
if ($seq) {
    $maxIdCheck = DB::select("SELECT MAX(id) as max_id FROM payroll_receipts");
    $maxId = $maxIdCheck[0]->max_id ?? 1;
    DB::statement("SELECT setval('{$seq}', {$maxId});");
}

$seqCheck = DB::select("SELECT pg_get_serial_sequence('users', 'id') as seq");
$seq = $seqCheck[0]->seq ?? null;
if ($seq) {
    $maxIdCheck = DB::select("SELECT MAX(id) as max_id FROM users");
    $maxId = $maxIdCheck[0]->max_id ?? 1;
    DB::statement("SELECT setval('{$seq}', {$maxId});");
}

echo "Migração e parametrização de dados históricos efetuada com sucesso!\n";

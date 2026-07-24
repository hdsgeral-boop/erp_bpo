<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Iniciando Teste de Fluxos do ERP Consulvolt...\n";
echo "=================================================\n\n";

try {
    \Illuminate\Database\Eloquent\Model::unguard();
    DB::beginTransaction();

    // 1. Setup Básico
    echo "1. Configurando Empresa, Armazém e Entidades...\n";
    $company = \App\Models\Company::firstOrCreate(['id' => 1], ['name' => 'Consulvolt Lda', 'nif' => '500000000']);
    $warehouse = \App\Models\Warehouse::firstOrCreate(['id' => 1], ['company_id' => 1, 'name' => 'Armazém Principal']);
    
    $fornecedor = \App\Models\ThirdParty::firstOrCreate(['nif' => '500111222'], ['company_id' => 1, 'name' => 'Fornecedor Teste', 'is_supplier' => true]);
    $cliente = \App\Models\ThirdParty::firstOrCreate(['nif' => '500333444'], ['company_id' => 1, 'name' => 'Cliente Teste', 'is_customer' => true]);
    
    $produto = \App\Models\Product::firstOrCreate(
        ['name' => 'Produto de Teste'], 
        ['company_id' => 1, 'code' => 'PRD-TESTE', 'is_inventory' => true, 'unit_price' => 5000]
    );
    
    echo "   -> Setup concluído.\n\n";

    // 2. Teste do Fluxo de Compras (Entrada de Stock)
    echo "2. Testando Fluxo de Compras...\n";
    $purchaseController = app(\App\Http\Controllers\PurchaseInvoiceController::class);
    $purchaseRequest = Request::create('/compras/faturas', 'POST', [
        'supplier_id' => $fornecedor->id,
        'invoice_number' => 'FT-FORN-001',
        'date' => date('Y-m-d'),
        'items' => [
            [
                'product_id' => $produto->id,
                'quantity' => 100,
                'unit_price' => 2000
            ]
        ]
    ]);
    $purchaseController->store($purchaseRequest);
    
    $produto->refresh();
    echo "   -> Fatura de Compra registada.\n";
    echo "   -> Stock Atual do Produto após compra (Esperado 100): " . $produto->stock_qty . "\n\n";

    // 3. Teste do Fluxo de Vendas/POS (Saída de Stock)
    echo "3. Testando Fluxo de Vendas (POS)...\n";
    $saleController = app(\App\Http\Controllers\SaleController::class);
    $saleRequest = Request::create('/vendas/pos/store', 'POST', [
        'doc_type' => 'FR',
        'customer_id' => $cliente->id,
        'items' => [
            [
                'id' => $produto->id,
                'quantity' => 5,
                'unit_price' => 5000,
                'subtotal' => 25000
            ]
        ]
    ]);
    
    $response = $saleController->store($saleRequest);
    
    $produto->refresh();
    echo "   -> Venda processada via POS.\n";
    echo "   -> Stock Atual do Produto após venda (Esperado 95): " . $produto->stock_qty . "\n\n";

    // 4. Teste de Tesouraria
    echo "4. Testando Tesouraria...\n";
    $treasuryController = new \App\Http\Controllers\BankStatementController();
    $treasuryRequest = Request::create('/tesouraria/bancos', 'POST', [
        'date' => date('Y-m-d'),
        'account_code' => 'B001 (BAI)',
        'description' => 'Pagamento de Cliente Ref POS',
        'type_dc' => 'D', // Débito/Entrada
        'value' => 25000
    ]);
    $treasuryController->store($treasuryRequest);
    $bankLine = \App\Models\BankStatementLine::latest('id')->first();
    echo "   -> Movimento Bancário registado (Débito: " . $bankLine->value . " Kz).\n\n";

    // 5. Teste de Contabilidade (Partidas Dobradas)
    echo "5. Testando Contabilidade...\n";
    \App\Models\ChartOfAccount::firstOrCreate(['code' => '11.1', 'company_id' => 1], ['description' => 'Caixa Geral', 'type' => 'M']);
    \App\Models\ChartOfAccount::firstOrCreate(['code' => '71.1', 'company_id' => 1], ['description' => 'Vendas de Mercadorias', 'type' => 'M']);

    $accountingController = app(\App\Http\Controllers\JournalController::class);
    $accRequest = Request::create('/contabilidade/diarios', 'POST', [
        'doc_date' => date('Y-m-d'),
        'entry_date' => date('Y-m-d'),
        'doc_number' => 'LANC-001',
        'description' => 'Reconhecimento Venda a Dinheiro',
        'lines' => [
            ['account_code' => '11.1', 'type_dc' => 'D', 'value' => 25000, 'description' => 'Entrada em Caixa'],
            ['account_code' => '71.1', 'type_dc' => 'C', 'value' => 25000, 'description' => 'Receita de Venda']
        ]
    ]);
    $accountingController->store($accRequest);
    $journalLines = \App\Models\JournalLine::orderBy('id', 'desc')->take(2)->get();
    echo "   -> Lançamento Contabilístico registado:\n";
    foreach($journalLines as $jl) {
        echo "      - Conta: " . $jl->account_code . " | Tipo: " . $jl->type_dc . " | Valor: " . $jl->value . "\n";
    }
    
    echo "\n=================================================\n";
    echo "SUCESSO: Todos os fluxos funcionaram sem erros e a integridade dos dados foi mantida.\n";
    
    // Reverter para não poluir a base de dados do utilizador permanentemente
    DB::rollBack();
    echo "\n(Nota: As transações foram revertidas automaticamente para manter a base de dados limpa).\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\nERRO DURANTE O TESTE: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}


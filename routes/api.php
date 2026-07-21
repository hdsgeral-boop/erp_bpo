<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ERP Consulvolt — API Routes v1
|--------------------------------------------------------------------------
| Todas as rotas usam:
| - Autenticação: Laravel Sanctum (Bearer Token)
| - company_id: auth()->user()->company_id (nunca hardcoded)
| - Prefixo: /api/v1
|--------------------------------------------------------------------------
*/

// ═══════════════════════════════════════════════════════
// ROTAS PÚBLICAS (sem autenticação)
// ═══════════════════════════════════════════════════════
Route::prefix('v1')->group(function () {

    // Auth
    Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
    Route::post('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'sendResetLinkEmail']);
    Route::post('/reset-password', [\App\Http\Controllers\PasswordResetController::class, 'reset']);

    // Health
    Route::get('/ping', fn() => response()->json(['pong' => true, 'ts' => now()->toIso8601String()]));

    // ═══════════════════════════════════════════════════════
    // ROTAS PROTEGIDAS (Sanctum)
    // ═══════════════════════════════════════════════════════
    Route::middleware('auth:sanctum')->group(function () {

        // ─── Utilizador atual ────────────────────────────
        Route::get('/me', fn(Request $request) => response()->json($request->user()->load('company', 'roles')));
        Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
        Route::post('/change-password', [\App\Http\Controllers\SettingsController::class, 'updatePassword']);
        Route::post('/generate-api-token', [\App\Http\Controllers\SettingsController::class, 'generateToken']);
        Route::delete('/revoke-api-token/{id}', [\App\Http\Controllers\SettingsController::class, 'revokeToken']);

        // ─── Dashboard Global ─────────────────────────────
        Route::get('/dashboard', [\App\Http\Controllers\GlobalDashboardController::class, 'index']);

        // ─── Business Intelligence ────────────────────────
        Route::get('/bi/dataset', [\App\Http\Controllers\BiController::class, 'dataset']);

        // ─── AI Agent ─────────────────────────────────────
        Route::post('/ai/process', [\App\Http\Controllers\AiAgentController::class, 'process']);
        Route::prefix('ai/admin')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\AiAdminController::class, 'dashboard']);
            Route::get('/agents', [\App\Http\Controllers\AiAdminController::class, 'agents']);
            Route::get('/models', [\App\Http\Controllers\AiAdminController::class, 'models']);
            Route::get('/providers', [\App\Http\Controllers\AiAdminController::class, 'providers']);
            Route::post('/providers/test', [\App\Http\Controllers\AiAdminController::class, 'testConnection']);
            Route::get('/conversations', [\App\Http\Controllers\AiAdminController::class, 'conversations']);
        });

        // ═══════════════════════════════════════════════════
        // MÓDULO DE ENTIDADES (Terceiros)
        // ═══════════════════════════════════════════════════
        Route::prefix('entidades')->group(function () {
            Route::get('/', [\App\Http\Controllers\ThirdPartyController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\ThirdPartyController::class, 'store']);
            Route::get('/{id}', [\App\Http\Controllers\ThirdPartyController::class, 'show']);
            Route::put('/{id}', [\App\Http\Controllers\ThirdPartyController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\ThirdPartyController::class, 'destroy']);
            Route::delete('/{id}/anexos/{anexo}', [\App\Http\Controllers\ThirdPartyController::class, 'destroyAttachment']);
        });

        // ═══════════════════════════════════════════════════
        // MÓDULO DE VENDAS
        // ═══════════════════════════════════════════════════
        Route::prefix('vendas')->group(function () {
            // Documentos Comerciais (FT, FR, OR, PP, NC, ND, GT, GR)
            Route::get('/documentos', [\App\Http\Controllers\CommercialDocumentController::class, 'index']);
            Route::get('/documentos/{category}', [\App\Http\Controllers\CommercialDocumentController::class, 'listByCategory']);
            Route::post('/documentos', [\App\Http\Controllers\CommercialDocumentController::class, 'store']);
            Route::get('/documentos/show/{id}', [\App\Http\Controllers\CommercialDocumentController::class, 'show']);
            Route::post('/documentos/{id}/anular', [\App\Http\Controllers\CommercialDocumentController::class, 'cancel']);
            Route::get('/documentos/{id}/pdf', [\App\Http\Controllers\CommercialDocumentController::class, 'pdf']);

            // POS
            Route::get('/pos/session', [\App\Http\Controllers\SalesPOSController::class, 'currentSession']);
            Route::post('/pos/open', [\App\Http\Controllers\SalesPOSController::class, 'openSession']);
            Route::post('/pos/close', [\App\Http\Controllers\SalesPOSController::class, 'closeSession']);
            Route::post('/pos/store', [\App\Http\Controllers\SalesPOSController::class, 'store']);

            // SAF-T
            Route::get('/saft', [\App\Http\Controllers\ReportController::class, 'saftData']);
            Route::post('/saft/export', [\App\Http\Controllers\ReportController::class, 'generateSaft']);
        });

        // ═══════════════════════════════════════════════════
        // MÓDULO DE COMPRAS
        // ═══════════════════════════════════════════════════
        Route::prefix('compras')->group(function () {
            // Pedidos Internos
            Route::get('/pedidos', [\App\Http\Controllers\PurchaseRequestController::class, 'index']);
            Route::post('/pedidos', [\App\Http\Controllers\PurchaseRequestController::class, 'store']);
            Route::get('/pedidos/{id}', [\App\Http\Controllers\PurchaseRequestController::class, 'show']);
            Route::put('/pedidos/{id}', [\App\Http\Controllers\PurchaseRequestController::class, 'update']);
            Route::delete('/pedidos/{id}', [\App\Http\Controllers\PurchaseRequestController::class, 'destroy']);
            Route::post('/pedidos/{id}/aprovar', [\App\Http\Controllers\PurchaseRequestController::class, 'approve']);
            Route::post('/pedidos/{id}/rejeitar', [\App\Http\Controllers\PurchaseRequestController::class, 'reject']);

            // Encomendas a Fornecedores
            Route::get('/encomendas', [\App\Http\Controllers\PurchaseOrderController::class, 'index']);
            Route::post('/encomendas', [\App\Http\Controllers\PurchaseOrderController::class, 'store']);
            Route::get('/encomendas/{id}', [\App\Http\Controllers\PurchaseOrderController::class, 'show']);
            Route::put('/encomendas/{id}', [\App\Http\Controllers\PurchaseOrderController::class, 'update']);
            Route::post('/encomendas/{id}/aprovar', [\App\Http\Controllers\PurchaseOrderController::class, 'approve']);

            // Receções de Mercadoria
            Route::get('/rececoes', [\App\Http\Controllers\PurchaseDeliveryController::class, 'index']);
            Route::post('/rececoes', [\App\Http\Controllers\PurchaseDeliveryController::class, 'store']);
            Route::get('/rececoes/{id}', [\App\Http\Controllers\PurchaseDeliveryController::class, 'show']);
            Route::delete('/rececoes/{id}', [\App\Http\Controllers\PurchaseDeliveryController::class, 'destroy']);

            // Faturas de Fornecedor
            Route::get('/faturas', [\App\Http\Controllers\PurchaseInvoiceController::class, 'index']);
            Route::post('/faturas', [\App\Http\Controllers\PurchaseInvoiceController::class, 'store']);
            Route::get('/faturas/{id}', [\App\Http\Controllers\PurchaseInvoiceController::class, 'show']);
            Route::put('/faturas/{id}', [\App\Http\Controllers\PurchaseInvoiceController::class, 'update']);
        });

        // ═══════════════════════════════════════════════════
        // MÓDULO DE RH
        // ═══════════════════════════════════════════════════
        Route::prefix('rh')->group(function () {
            // Funcionários
            Route::get('/funcionarios', [\App\Http\Controllers\EmployeeController::class, 'index']);
            Route::post('/funcionarios', [\App\Http\Controllers\EmployeeController::class, 'store']);
            Route::get('/funcionarios/{id}', [\App\Http\Controllers\EmployeeController::class, 'show']);
            Route::put('/funcionarios/{id}', [\App\Http\Controllers\EmployeeController::class, 'update']);
            Route::delete('/funcionarios/{id}', [\App\Http\Controllers\EmployeeController::class, 'destroy']);
            Route::delete('/funcionarios/{id}/anexos/{anexo}', [\App\Http\Controllers\EmployeeController::class, 'destroyAttachment']);

            // Contratos
            Route::apiResource('contratos', \App\Http\Controllers\ContractController::class);

            // Infotipos
            Route::apiResource('infotipos', \App\Http\Controllers\InfotypeController::class);

            // Assiduidade, Ausências, Horas Extra, Benefícios
            Route::apiResource('assiduidade', \App\Http\Controllers\AttendanceController::class);
            Route::apiResource('ausencias', \App\Http\Controllers\AbsenceController::class);
            Route::apiResource('horas-extra', \App\Http\Controllers\OvertimeController::class);
            Route::apiResource('beneficios', \App\Http\Controllers\BenefitController::class);

            // Salários
            Route::get('/salarios', [\App\Http\Controllers\PayrollController::class, 'index']);
            Route::get('/salarios/{id}', [\App\Http\Controllers\PayrollController::class, 'show']);
            Route::get('/salarios/wizard', [\App\Http\Controllers\PayrollController::class, 'wizardData']);
            Route::post('/salarios/processar', [\App\Http\Controllers\PayrollController::class, 'process']);
            Route::post('/salarios/{id}/fechar', [\App\Http\Controllers\PayrollController::class, 'close']);
            Route::post('/salarios/{id}/estornar', [\App\Http\Controllers\PayrollController::class, 'reverse']);
            Route::get('/salarios/{id}/exportar-agt', [\App\Http\Controllers\PayrollController::class, 'exportAgt']);
            Route::get('/recibos/{id}/pdf', [\App\Http\Controllers\PayrollController::class, 'downloadReceipt']);

            // Configurações Fiscais
            Route::apiResource('escaloes-irt', \App\Http\Controllers\TaxBracketController::class);
            Route::apiResource('taxas-salariais', \App\Http\Controllers\PayrollTaxController::class);
        });

        // ═══════════════════════════════════════════════════
        // MÓDULO DE ATIVOS IMOBILIZADOS
        // ═══════════════════════════════════════════════════
        Route::prefix('ativos')->group(function () {
            Route::get('/', [\App\Http\Controllers\AssetController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\AssetController::class, 'store']);
            Route::get('/{id}', [\App\Http\Controllers\AssetController::class, 'show']);
            Route::put('/{id}', [\App\Http\Controllers\AssetController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\AssetController::class, 'destroy']);
            Route::delete('/{id}/anexos/{anexo}', [\App\Http\Controllers\AssetController::class, 'destroyAttachment']);
        });

        // ═══════════════════════════════════════════════════
        // MÓDULO DE INVENTÁRIO E LOGÍSTICA
        // ═══════════════════════════════════════════════════
        Route::prefix('logistica')->group(function () {
            // Artigos (Produtos)
            Route::get('/artigos', [\App\Http\Controllers\ProductController::class, 'index']);
            Route::post('/artigos', [\App\Http\Controllers\ProductController::class, 'store']);
            Route::get('/artigos/{id}', [\App\Http\Controllers\ProductController::class, 'show']);
            Route::put('/artigos/{id}', [\App\Http\Controllers\ProductController::class, 'update']);
            Route::delete('/artigos/{id}', [\App\Http\Controllers\ProductController::class, 'destroy']);
            Route::delete('/artigos/{id}/anexos/{anexo}', [\App\Http\Controllers\ProductController::class, 'destroyAttachment']);

            // Categorias
            Route::apiResource('categorias', \App\Http\Controllers\ProductCategoryController::class);

            // Armazéns
            Route::apiResource('armazens', \App\Http\Controllers\WarehouseController::class);

            // Movimentos de Stock
            Route::get('/movimentos', [\App\Http\Controllers\StockMovementController::class, 'index']);
            Route::post('/movimentos', [\App\Http\Controllers\StockMovementController::class, 'store']);

            // Inventário Físico
            Route::get('/inventario', [\App\Http\Controllers\InventorySessionController::class, 'index']);
            Route::post('/inventario', [\App\Http\Controllers\InventorySessionController::class, 'store']);
            Route::get('/inventario/{id}', [\App\Http\Controllers\InventorySessionController::class, 'show']);
            Route::get('/inventario/{id}/contagem', [\App\Http\Controllers\InventorySessionController::class, 'contagem']);
            Route::post('/inventario/{id}/contagem', [\App\Http\Controllers\InventorySessionController::class, 'saveContagem']);
            Route::get('/inventario/{id}/review', [\App\Http\Controllers\InventorySessionController::class, 'review']);
            Route::post('/inventario/{id}/close', [\App\Http\Controllers\InventorySessionController::class, 'close']);

            // POS Armazém
            Route::post('/pos/store', [\App\Http\Controllers\WarehousePOSController::class, 'store']);

            // Guias
            Route::apiResource('guias', \App\Http\Controllers\WaybillController::class);

            // Receções (Entradas)
            Route::get('/rececoes', [\App\Http\Controllers\WarehouseReceiptController::class, 'index']);
            Route::post('/rececoes', [\App\Http\Controllers\WarehouseReceiptController::class, 'store']);
            Route::delete('/rececoes/{id}', [\App\Http\Controllers\WarehouseReceiptController::class, 'destroy']);

            // Níveis de Stock
            Route::get('/stock', [\App\Http\Controllers\LogisticsController::class, 'stockLevels']);
        });

        // ═══════════════════════════════════════════════════
        // MÓDULO DE TESOURARIA
        // ═══════════════════════════════════════════════════
        Route::prefix('tesouraria')->group(function () {
            // Contas de Tesouraria (Bancos/Caixas)
            Route::apiResource('accounts', \App\Http\Controllers\TreasuryAccountController::class)->except(['show', 'destroy']);

            // Recibos e Pagamentos
            Route::get('/documentos/{category}', [\App\Http\Controllers\ReceiptController::class, 'index']);
            Route::post('/documentos/{category}', [\App\Http\Controllers\ReceiptController::class, 'store']);
            Route::get('/documentos/{category}/{id}', [\App\Http\Controllers\ReceiptController::class, 'show']);
            Route::post('/documentos/{category}/{id}/anular', [\App\Http\Controllers\ReceiptController::class, 'cancel']);

            // Contas Correntes
            Route::get('/contas-correntes', [\App\Http\Controllers\CurrentAccountController::class, 'index']);
            Route::get('/contas-correntes/{id}', [\App\Http\Controllers\CurrentAccountController::class, 'show']);

            // Extratos Bancários
            Route::apiResource('bank-statements', \App\Http\Controllers\BankStatementController::class);

            // Reconciliação Bancária
            Route::get('/reconciliations', [\App\Http\Controllers\ReconciliationController::class, 'index']);
            Route::post('/reconciliations', [\App\Http\Controllers\ReconciliationController::class, 'store']);
            Route::get('/reconciliations/{id}', [\App\Http\Controllers\ReconciliationController::class, 'show']);
            Route::post('/reconciliations/{id}/match', [\App\Http\Controllers\ReconciliationController::class, 'match']);
            Route::post('/reconciliations/{id}/close', [\App\Http\Controllers\ReconciliationController::class, 'close']);
        });

        // ═══════════════════════════════════════════════════
        // MÓDULO DE CONTABILIDADE
        // ═══════════════════════════════════════════════════
        Route::prefix('contabilidade')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\AccountingController::class, 'dashboard']);
            Route::get('/trial-balance', [\App\Http\Controllers\AccountingController::class, 'trialBalance']);
            Route::apiResource('plano-contas', \App\Http\Controllers\ChartOfAccountController::class);
            Route::apiResource('diarios', \App\Http\Controllers\JournalController::class);
            Route::apiResource('mapas', \App\Http\Controllers\AccountingMapController::class);
            Route::get('/rotinas', [\App\Http\Controllers\AccountingRoutineController::class, 'index']);
            Route::post('/rotinas/stamp-duty', [\App\Http\Controllers\AccountingRoutineController::class, 'processStampDuty']);
        });

        // ═══════════════════════════════════════════════════
        // MÓDULO DE SGD (Sistema de Gestão Documental)
        // ═══════════════════════════════════════════════════
        Route::apiResource('documentos', \App\Http\Controllers\DocumentController::class);

        // ═══════════════════════════════════════════════════
        // MÓDULO DE ADMINISTRAÇÃO
        // ═══════════════════════════════════════════════════
        Route::prefix('admin')->group(function () {
            Route::apiResource('users', \App\Http\Controllers\UserController::class);
            Route::apiResource('companies', \App\Http\Controllers\CompanyController::class);
            Route::apiResource('departments', \App\Http\Controllers\DepartmentController::class);
            Route::apiResource('positions', \App\Http\Controllers\PositionController::class);
            Route::apiResource('roles', \App\Http\Controllers\RoleController::class);
            Route::apiResource('document-series', \App\Http\Controllers\DocumentSeriesController::class)
                ->parameters(['document-series' => 'documentSeries']);
            Route::apiResource('taxes', \App\Http\Controllers\TaxController::class);
            Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index']);
            Route::put('/settings/bulk', [\App\Http\Controllers\SettingController::class, 'updateBulk']);
            Route::get('/logs', [\App\Http\Controllers\SystemLogController::class, 'index']);
            Route::get('/audit-logs', [\App\Http\Controllers\AuditLogController::class, 'index']);

            // Importação de Dados
            Route::get('/import/template/{type}', [\App\Http\Controllers\DataImportController::class, 'downloadTemplate']);
            Route::post('/import/upload', [\App\Http\Controllers\DataImportController::class, 'upload']);
        });

    }); // fim auth:sanctum

}); // fim v1

// ═══════════════════════════════════════════════════════
// ROTAS API EXTERNAS (PowerBI, Mobile — token Sanctum)
// ═══════════════════════════════════════════════════════
Route::middleware(['auth:sanctum', 'throttle:60,1'])->prefix('v1/external')->group(function () {
    Route::get('/third-parties', [\App\Http\Controllers\Api\ThirdPartyController::class, 'index']);
    Route::get('/sales', [\App\Http\Controllers\Api\SaleController::class, 'index']);
    Route::get('/hr/payroll/runs', [\App\Http\Controllers\Api\V1\PayrollApiController::class, 'getRuns']);
    Route::get('/hr/payroll/runs/{id}/receipts', [\App\Http\Controllers\Api\V1\PayrollApiController::class, 'getReceipts']);
    Route::get('/hr/payroll/items', [\App\Http\Controllers\Api\V1\PayrollApiController::class, 'getPayrollItems']);
    Route::get('/hr/employees/{id}/receipts', [\App\Http\Controllers\Api\V1\PayrollApiController::class, 'getEmployeeReceipts']);
    Route::post('/hr/attendance', [\App\Http\Controllers\Api\HrController::class, 'storeAttendance']);
});

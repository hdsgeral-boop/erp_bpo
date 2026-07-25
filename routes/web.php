<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GlobalDashboardController;
use App\Http\Controllers\CommercialDocumentController;
use App\Http\Controllers\SalesPOSController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\PurchaseDeliveryController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\BenefitController;
use App\Http\Controllers\TreasuryAccountController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\CurrentAccountController;
use App\Http\Controllers\ChartOfAccountController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\PosRegisterController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\BiController;
use App\Http\Controllers\ThirdPartyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\Admin\PaymentManagementController;

// ─── Health Check ────────────────────────────────────────
Route::get('/health', fn() => response()->json(['status' => 'ok', 'service' => 'ERP Consulvolt API']));

// ─── Website Público (Consulvolt Soluções) ────────────────
Route::get('/', [WebsiteController::class, 'index'])->name('home');
Route::get('/sobre', [WebsiteController::class, 'about'])->name('website.about');
Route::get('/servicos', [WebsiteController::class, 'services'])->name('website.services');
Route::get('/contacto', [WebsiteController::class, 'contactView'])->name('website.contact');
Route::post('/contacto', [WebsiteController::class, 'contact'])->name('contact.submit');
Route::get('/termos', [WebsiteController::class, 'terms'])->name('website.terms');

// ─── Módulos ERP Explicação ──────────────────────────────
Route::get('/modulos/vendas-pos', [WebsiteController::class, 'moduleVendasPos'])->name('website.modules.vendas-pos');
Route::get('/modulos/recursos-humanos', [WebsiteController::class, 'moduleRecursosHumanos'])->name('website.modules.recursos-humanos');
Route::get('/modulos/contabilidade-pgc', [WebsiteController::class, 'moduleContabilidadePgc'])->name('website.modules.contabilidade-pgc');
Route::get('/modulos/tesouraria-bancos', [WebsiteController::class, 'moduleTesourariaBancos'])->name('website.modules.tesouraria-bancos');
Route::get('/modulos/powerbi-direct', [WebsiteController::class, 'modulePowerbiDirect'])->name('website.modules.powerbi-direct');

use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\CompanySelectionController;

// ─── Web Auth Routes (Login / Register / Logout) ─────────
Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'loginWeb'])->name('login.post');
Route::get('/register', [\App\Http\Controllers\AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [\App\Http\Controllers\AuthController::class, 'registerWeb'])->name('register.post');
Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('password.request');
Route::post('/forgot-password', fn(\Illuminate\Http\Request $request) => back()->with('status', 'Enviámos um endereço de reposição para o seu email.'))->name('password.email');
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logoutWeb'])->name('logout');

// ─── Google OAuth 2.0 / OpenID Connect Routes ───────────
Route::middleware(['throttle:10,1'])->group(function () {
    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
    Route::get('/auth/google/onboarding', [GoogleAuthController::class, 'showOnboardingForm'])->name('auth.google.onboarding');
    Route::post('/auth/google/onboarding', [GoogleAuthController::class, 'processOnboarding'])->name('auth.google.onboarding.submit');
});

// ─── Seleção de Empresa no Login ──────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/select-company', [CompanySelectionController::class, 'showSelectForm'])->name('company.select');
    Route::post('/select-company', [CompanySelectionController::class, 'selectCompany'])->name('company.select.post');
});

// ─── Switch Company ──────────────────────────────────────
Route::match(['get', 'post'], '/switch-company', [CompanyController::class, 'switchCompany'])->name('company.switch');

// Dashboard Global
Route::middleware(['can:dashboard.view'])->group(function () {
    Route::get('/dashboard', [GlobalDashboardController::class, 'index'])->name('dashboard');
});

// Business Intelligence
Route::middleware(['can:bi.view'])->group(function () {
    Route::get('/bi', [BiController::class, 'indexView'])->name('bi.view');
    Route::get('/bi/dataset', [BiController::class, 'dataset'])->name('bi.dataset');
});

// POS (Frente de Caixa & Balcão)
Route::middleware(['can:pos.access'])->group(function () {
    Route::get('/vendas/pos', [SalesPOSController::class, 'indexView'])->name('vendas.pos.index');
    Route::get('/logistica/pos-balcao', [SalesPOSController::class, 'indexView'])->name('logistica.pos.balcao');
});

// Vendas & Faturação
Route::middleware(['can:sales.view'])->group(function () {
    Route::get('/vendas/documentos-novo/{category?}', [CommercialDocumentController::class, 'create'])->name('vendas.documentos.create');
    Route::get('/vendas/documentos-detalhes/{id}', [CommercialDocumentController::class, 'show'])->name('vendas.documentos.show');
    Route::post('/vendas/documentos-anular/{category}/{id}', [CommercialDocumentController::class, 'cancel'])->name('vendas.documentos.cancel');
    Route::post('/vendas/documentos-anular/{id}', [CommercialDocumentController::class, 'cancel'])->name('vendas.documentos.cancel_direct');
    Route::post('/vendas/documentos-converter/{id}', [CommercialDocumentController::class, 'convert'])->name('vendas.documentos.convert');
    Route::get('/vendas/documentos/{id}/pdf', [CommercialDocumentController::class, 'pdf'])->name('vendas.documentos.pdf');
    Route::get('/vendas/documentos/{id}/talao', [CommercialDocumentController::class, 'thermal'])->name('vendas.documentos.thermal');
    Route::get('/vendas/documentos/{category?}', [CommercialDocumentController::class, 'index'])->name('vendas.documentos.index');
    Route::post('/vendas/documentos/{category?}', [CommercialDocumentController::class, 'store'])->name('vendas.documentos.store');
    Route::post('/vendas/documentos', [CommercialDocumentController::class, 'store'])->name('vendas.store');
    Route::get('/vendas/saft', fn() => view('sales.saft'))->name('vendas.saft');
    Route::post('/vendas/saft/export', function(\Illuminate\Http\Request $request) {
        $companyId = session('company_id') ?? 1;
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-t'));

        $service = new \App\Services\AgtSaftExportService();
        $xmlContent = $service->generateSaftXml($companyId, $startDate, $endDate);
        
        $fileName = "SAFT-AO-{$companyId}-{$startDate}-{$endDate}.xml";

        return response($xmlContent, 200, [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\""
        ]);
    })->name('vendas.saft.export');
});

// Logística & Inventário
Route::middleware(['can:inventory.view'])->group(function () {
    Route::get('/logistica/stock', [ProductController::class, 'indexView'])->name('logistica.stock');
    Route::get('/logistica/guias', fn() => view('logistica.guias.index'))->name('logistica.guias.index');
    Route::get('/logistica/movements', fn() => view('logistica.movements.index'))->name('logistica.movements.index');
    Route::get('/logistica/warehouses', [WarehouseController::class, 'indexView'])->name('logistica.warehouses.index');
    Route::get('/logistica/warehouses/create', [WarehouseController::class, 'create'])->name('logistica.warehouses.create');
    Route::get('/logistica/inventario/armazens', [WarehouseController::class, 'indexView'])->name('inventario.armazens.index');
    Route::get('/logistica/inventario/armazens/create', [WarehouseController::class, 'create'])->name('inventario.armazens.create');
    Route::post('/logistica/inventario/armazens', [WarehouseController::class, 'store'])->name('inventario.armazens.store');
    Route::get('/logistica/inventario/armazens/{id}', [WarehouseController::class, 'show'])->name('inventario.armazens.show');
    Route::get('/logistica/inventario/armazens/{id}/edit', [WarehouseController::class, 'edit'])->name('inventario.armazens.edit');
    Route::put('/logistica/inventario/armazens/{id}', [WarehouseController::class, 'update'])->name('inventario.armazens.update');
    Route::delete('/logistica/inventario/armazens/{id}', [WarehouseController::class, 'destroy'])->name('inventario.armazens.destroy');
    Route::get('/logistica/categories', [ProductCategoryController::class, 'index'])->name('logistica.categories.index');
    Route::get('/logistica/categories/create', [ProductCategoryController::class, 'create'])->name('logistica.categories.create');
    Route::post('/logistica/categories', [ProductCategoryController::class, 'store'])->name('logistica.categories.store');
    Route::get('/logistica/categories/{id}/edit', [ProductCategoryController::class, 'edit'])->name('logistica.categories.edit');
    Route::put('/logistica/categories/{id}', [ProductCategoryController::class, 'update'])->name('logistica.categories.update');
    Route::delete('/logistica/categories/{id}', [ProductCategoryController::class, 'destroy'])->name('logistica.categories.destroy');
    Route::get('/product_categories', [ProductCategoryController::class, 'index'])->name('product_categories.index');
    Route::get('/logistica/products', [ProductController::class, 'indexView'])->name('logistica.products.index');
    Route::get('/logistica/products/create', [ProductController::class, 'create'])->name('logistica.products.create');
    Route::get('/logistica/inventario/artigos', [ProductController::class, 'indexView'])->name('inventario.artigos.index');
    Route::get('/logistica/inventario/artigos/create', [ProductController::class, 'create'])->name('inventario.artigos.create');
    Route::post('/logistica/inventario/artigos', [ProductController::class, 'store'])->name('inventario.artigos.store');
    Route::get('/logistica/inventario/artigos/{id}', [ProductController::class, 'show'])->name('inventario.artigos.show');
    Route::get('/logistica/inventario/artigos/{id}/edit', [ProductController::class, 'edit'])->name('inventario.artigos.edit');
    Route::put('/logistica/inventario/artigos/{id}', [ProductController::class, 'update'])->name('inventario.artigos.update');
    Route::delete('/logistica/inventario/artigos/{id}', [ProductController::class, 'destroy'])->name('inventario.artigos.destroy');
    Route::get('/logistica/inventario', fn() => view('logistica.inventario.index', ['sessions' => \App\Models\InventorySession::with('warehouse')->get(), 'warehouses' => \App\Models\Warehouse::all()]))->name('logistica.inventario.index');
    Route::post('/logistica/inventario', fn() => back()->with('success', 'Sessão iniciada.'))->name('logistica.inventario.store');
    Route::get('/logistica/inventario/contagem/{id}', fn($id) => view('logistica.inventario.contagem', compact('id')))->name('logistica.inventario.contagem');
    Route::get('/logistica/inventario/review/{id}', fn($id) => view('logistica.inventario.review', compact('id')))->name('logistica.inventario.review');
});

// Compras
Route::middleware(['can:purchases.view'])->group(function () {
    // Pedidos Internos (Requisições)
    Route::get('/compras/pedidos', [PurchaseRequestController::class, 'indexView'])->name('compras.pedidos.index');
    Route::get('/compras/pedidos/create', [PurchaseRequestController::class, 'create'])->name('compras.pedidos.create');
    Route::post('/compras/pedidos', [PurchaseRequestController::class, 'store'])->name('compras.pedidos.store');
    Route::get('/compras/pedidos/{id}', [PurchaseRequestController::class, 'show'])->name('compras.pedidos.show');
    Route::get('/compras/pedidos/{id}/pdf', [PurchaseRequestController::class, 'pdf'])->name('compras.pedidos.pdf');
    Route::post('/compras/pedidos/{id}/aprovar', [PurchaseRequestController::class, 'approve'])->name('compras.pedidos.approve');
    Route::post('/compras/pedidos/{id}/rejeitar', [PurchaseRequestController::class, 'reject'])->name('compras.pedidos.reject');

    // Encomendas a Fornecedores (Ordens de Compra)
    Route::get('/compras/encomendas', [PurchaseOrderController::class, 'indexView'])->name('compras.encomendas.index');
    Route::get('/compras/encomendas/create', [PurchaseOrderController::class, 'create'])->name('compras.encomendas.create');
    Route::post('/compras/encomendas', [PurchaseOrderController::class, 'store'])->name('compras.encomendas.store');
    Route::get('/compras/encomendas/{id}', [PurchaseOrderController::class, 'show'])->name('compras.encomendas.show');
    Route::get('/compras/encomendas/{id}/pdf', [PurchaseOrderController::class, 'pdf'])->name('compras.encomendas.pdf');
    Route::post('/compras/encomendas/{id}/aprovar', [PurchaseOrderController::class, 'approve'])->name('compras.encomendas.approve');
    // Receções de Mercadoria (Entrada em Armazém)
    Route::get('/compras/rececoes', [PurchaseDeliveryController::class, 'indexView'])->name('compras.rececoes.index');
    Route::get('/compras/rececoes/create', [PurchaseDeliveryController::class, 'create'])->name('compras.rececoes.create');
    Route::post('/compras/rececoes', [PurchaseDeliveryController::class, 'store'])->name('compras.rececoes.store');
    Route::get('/compras/rececoes/{id}', [PurchaseDeliveryController::class, 'show'])->name('compras.rececoes.show');
    Route::get('/compras/faturas', [PurchaseInvoiceController::class, 'indexView'])->name('compras.faturas.index');
    Route::get('/compras/faturas/create', [PurchaseInvoiceController::class, 'create'])->name('compras.faturas.create');
    Route::post('/compras/faturas', [PurchaseInvoiceController::class, 'store'])->name('compras.faturas.store');
    Route::get('/compras/faturas/{id}', [PurchaseInvoiceController::class, 'show'])->name('compras.faturas.show');
    Route::get('/compras/faturas/{id}/pdf', [PurchaseInvoiceController::class, 'pdf'])->name('compras.faturas.pdf');
    Route::post('/compras/faturas/{id}/anular', [PurchaseInvoiceController::class, 'cancel'])->name('compras.faturas.cancel');
});

// Recursos Humanos (Salários & RH)
Route::middleware(['can:hr.view'])->group(function () {
    Route::get('/rh/funcionarios', [EmployeeController::class, 'indexView'])->name('rh.funcionarios.index');
    Route::get('/rh/funcionarios/create', [EmployeeController::class, 'create'])->name('rh.funcionarios.create');
    Route::post('/rh/funcionarios', [EmployeeController::class, 'store'])->name('rh.funcionarios.store');
    Route::get('/rh/funcionarios/{id}', [EmployeeController::class, 'show'])->name('rh.funcionarios.show');
    Route::get('/rh/funcionarios/{id}/edit', [EmployeeController::class, 'edit'])->name('rh.funcionarios.edit');
    Route::put('/rh/funcionarios/{id}', [EmployeeController::class, 'update'])->name('rh.funcionarios.update');
    Route::delete('/rh/funcionarios/{id}', [EmployeeController::class, 'destroy'])->name('rh.funcionarios.destroy');
    Route::get('/rh/contratos', [ContractController::class, 'indexView'])->name('rh.contratos.index');
    Route::get('/rh/contratos/create', fn() => view('hr.contracts.create'))->name('rh.contratos.create');
    Route::get('/rh/contratos/{id}/edit', fn($id) => back())->name('rh.contratos.edit');
    Route::delete('/rh/contratos/{id}', [ContractController::class, 'destroy'])->name('rh.contratos.destroy');
    // Assiduidade e Ponto
    Route::get('/rh/assiduidade', [AttendanceController::class, 'index'])->name('rh.assiduidade.index');
    Route::post('/rh/assiduidade', [AttendanceController::class, 'store'])->name('rh.assiduidade.store');
    Route::put('/rh/assiduidade/{assiduidade}', [AttendanceController::class, 'update'])->name('rh.assiduidade.update');
    Route::delete('/rh/assiduidade/{assiduidade}', [AttendanceController::class, 'destroy'])->name('rh.assiduidade.destroy');

    // Férias & Ausências
    Route::get('/rh/ausencias', [AbsenceController::class, 'index'])->name('rh.ausencias.index');
    Route::post('/rh/ausencias', [AbsenceController::class, 'store'])->name('rh.ausencias.store');
    Route::put('/rh/ausencias/{ausencia}', [AbsenceController::class, 'update'])->name('rh.ausencias.update');
    Route::delete('/rh/ausencias/{ausencia}', [AbsenceController::class, 'destroy'])->name('rh.ausencias.destroy');

    // Horas Extras
    Route::get('/rh/horas-extra', [OvertimeController::class, 'index'])->name('rh.horas-extra.index');
    Route::post('/rh/horas-extra', [OvertimeController::class, 'store'])->name('rh.horas-extra.store');
    Route::put('/rh/horas-extra/{horas_extra}', [OvertimeController::class, 'update'])->name('rh.horas-extra.update');
    Route::delete('/rh/horas-extra/{horas_extra}', [OvertimeController::class, 'destroy'])->name('rh.horas-extra.destroy');

    // Benefícios e Deduções
    Route::get('/rh/beneficios', [BenefitController::class, 'index'])->name('rh.beneficios.index');
    Route::post('/rh/beneficios', [BenefitController::class, 'store'])->name('rh.beneficios.store');
    Route::put('/rh/beneficios/{beneficio}', [BenefitController::class, 'update'])->name('rh.beneficios.update');
    Route::delete('/rh/beneficios/{beneficio}', [BenefitController::class, 'destroy'])->name('rh.beneficios.destroy');
    Route::get('/rh/salarios', [PayrollController::class, 'indexView'])->name('rh.salarios.wizard');
    Route::get('/rh/salarios/simulacao', [PayrollController::class, 'simulation'])->name('rh.salarios.simulation');
    Route::get('/rh/salarios/wizard', [PayrollController::class, 'indexView'])->name('rh.salarios.wizard_step');
    Route::post('/rh/salarios/process', [PayrollController::class, 'calculate'])->name('rh.salarios.process');
    Route::post('/rh/salarios/close', [PayrollController::class, 'calculate'])->name('rh.salarios.close');
    Route::get('/rh/salarios/export-agt/{id}', [PayrollController::class, 'exportAgt'])->name('rh.salarios.export_agt');
    Route::get('/rh/salarios/export-inss/{id}', [PayrollController::class, 'exportInss'])->name('rh.salarios.export_inss');
    Route::get('/rh/salarios/export-banco/{id}', [PayrollController::class, 'exportBankPs2'])->name('rh.salarios.export_banco');
    Route::get('/rh/salarios/recibo/{id}', [PayrollController::class, 'generatePdfReceipt'])->name('rh.salarios.recibo');
    Route::get('/rh/relatorios/inss', [PayrollController::class, 'inssReportView'])->name('rh.reports.inss');
    Route::get('/rh/relatorios/banco', [PayrollController::class, 'bankReportView'])->name('rh.reports.bank');
    Route::get('/rh/infotipos', fn() => view('hr.infotipos'))->name('rh.infotipos.index');
    Route::get('/rh/escaloes-irt', fn() => view('hr.escaloes_irt'))->name('rh.escaloes-irt.index');
    Route::get('/rh/taxas-salariais', fn() => view('hr.taxas_salariais'))->name('rh.taxas-salariais.index');
});

// Entidades (Terceiros)
Route::middleware(['can:third_parties.view'])->group(function () {
    Route::get('/entidades', [ThirdPartyController::class, 'index'])->name('entidades.index');
    Route::get('/entidades/create', [ThirdPartyController::class, 'create'])->name('entidades.create');
    Route::post('/entidades', [ThirdPartyController::class, 'store'])->name('entidades.store');
    Route::get('/entidades/{id}', [ThirdPartyController::class, 'show'])->name('entidades.show');
    Route::get('/entidades/{id}/edit', [ThirdPartyController::class, 'edit'])->name('entidades.edit');
    Route::put('/entidades/{id}', [ThirdPartyController::class, 'update'])->name('entidades.update');
    Route::delete('/entidades/{id}', [ThirdPartyController::class, 'destroy'])->name('entidades.destroy');
});

// Tesouraria
Route::middleware(['can:treasury.view'])->group(function () {
    Route::get('/tesouraria/accounts', [TreasuryAccountController::class, 'indexView'])->name('tesouraria.accounts.index');
    Route::get('/tesouraria/accounts/create', [TreasuryAccountController::class, 'create'])->name('tesouraria.accounts.create');
    Route::post('/tesouraria/accounts', [TreasuryAccountController::class, 'store'])->name('tesouraria.accounts.store');
    Route::get('/tesouraria/accounts/{account}/edit', [TreasuryAccountController::class, 'edit'])->name('tesouraria.accounts.edit');
    Route::put('/tesouraria/accounts/{account}', [TreasuryAccountController::class, 'update'])->name('tesouraria.accounts.update');
    Route::delete('/tesouraria/accounts/{account}', [TreasuryAccountController::class, 'destroy'])->name('tesouraria.accounts.destroy');
    Route::get('/tesouraria/accounts/{account}/statement', [TreasuryAccountController::class, 'statement'])->name('tesouraria.accounts.statement');
    Route::get('/tesouraria/accounts/{account}/pdf', [TreasuryAccountController::class, 'exportPdf'])->name('tesouraria.accounts.pdf');
    Route::post('/tesouraria/accounts/{account}/movement', [TreasuryAccountController::class, 'quickMovement'])->name('tesouraria.accounts.movement');
    Route::get('/tesouraria/documentos-novo/{category?}', [ReceiptController::class, 'create'])->name('tesouraria.documentos.create');
    Route::get('/tesouraria/documentos-detalhes/{category}/{id}', [ReceiptController::class, 'show'])->name('tesouraria.documentos.show');
    Route::get('/tesouraria/documentos-pdf/{category}/{id}', [ReceiptController::class, 'pdf'])->name('tesouraria.documentos.pdf');
    Route::post('/tesouraria/documentos-anular/{category}/{id}', [ReceiptController::class, 'cancel'])->name('tesouraria.documentos.cancel');
    Route::get('/tesouraria/documentos/{category?}', [ReceiptController::class, 'index'])->name('tesouraria.documents.index');
    Route::get('/tesouraria/docs/{category?}', [ReceiptController::class, 'index'])->name('tesouraria.documentos.index');
    Route::post('/tesouraria/documentos/{category?}', [ReceiptController::class, 'store'])->name('tesouraria.documentos.store');
    Route::get('/tesouraria/bank-statements', fn() => view('treasury.bank_statements'))->name('tesouraria.bank_statements.index');
    Route::get('/tesouraria/aging', [CurrentAccountController::class, 'agingView'])->name('tesouraria.aging');
});

// Ativos Fixos
Route::middleware(['can:assets.view'])->group(function () {
    Route::get('/ativos', [AssetController::class, 'indexView'])->name('ativos.index');
    Route::get('/ativos/create', [AssetController::class, 'create'])->name('ativos.create');
    Route::post('/ativos', [AssetController::class, 'store'])->name('ativos.store');
    Route::get('/ativos/{id}', fn($id) => back())->name('ativos.show');
    Route::get('/ativos/{id}/edit', fn($id) => back())->name('ativos.edit');
    Route::delete('/ativos/{id}', fn($id) => back())->name('ativos.destroy');
});

// Contabilidade PGC
Route::middleware(['can:accounting.view'])->group(function () {
    Route::get('/contabilidade/relatorios', [AccountingController::class, 'reportsIndex'])->name('contabilidade.relatorios');
    Route::get('/contabilidade/trial-balance', [AccountingController::class, 'trialBalance'])->name('contabilidade.trial_balance');
    Route::get('/contabilidade/balance-sheet', [AccountingController::class, 'balanceSheet'])->name('contabilidade.balance_sheet');
    Route::get('/contabilidade/income-statement', [AccountingController::class, 'incomeStatement'])->name('contabilidade.income_statement');
    Route::get('/contabilidade/cash-flow', [AccountingController::class, 'cashFlowStatement'])->name('contabilidade.cash_flow');
    Route::get('/contabilidade/ledger', [AccountingController::class, 'accountLedger'])->name('contabilidade.ledger');
    Route::get('/contabilidade/balance-sheet/pdf', [AccountingController::class, 'balanceSheetPdf'])->name('contabilidade.balance_sheet.pdf');
    Route::get('/contabilidade/income-statement/pdf', [AccountingController::class, 'incomeStatementPdf'])->name('contabilidade.income_statement.pdf');
    Route::get('/contabilidade/chart-of-accounts', [ChartOfAccountController::class, 'indexView'])->name('contabilidade.chart_of_accounts.index');
    Route::get('/contabilidade/chart-of-accounts/create', [ChartOfAccountController::class, 'create'])->name('contabilidade.chart_of_accounts.create');
    Route::post('/contabilidade/chart-of-accounts', [ChartOfAccountController::class, 'store'])->name('contabilidade.chart_of_accounts.store');
    Route::get('/contabilidade/chart-of-accounts/{id}/edit', [ChartOfAccountController::class, 'edit'])->name('contabilidade.chart_of_accounts.edit');
    Route::put('/contabilidade/chart-of-accounts/{id}', [ChartOfAccountController::class, 'update'])->name('contabilidade.chart_of_accounts.update');
    Route::delete('/contabilidade/chart-of-accounts/{id}', [ChartOfAccountController::class, 'destroy'])->name('contabilidade.chart_of_accounts.destroy');
    Route::get('/contabilidade/journals', [JournalController::class, 'indexView'])->name('contabilidade.journals.index');
    Route::get('/contabilidade/journals/create', fn() => view('accounting.journals.create'))->name('contabilidade.journals.create');
    Route::post('/contabilidade/journals', fn() => redirect()->route('contabilidade.journals.index')->with('success', 'Lançamento efetuado.'))->name('contabilidade.journals.store');
    Route::get('/contabilidade/maps', fn() => view('accounting.maps'))->name('contabilidade.maps.index');
});

// SGD (Gestão Documental) & Perfil Pessoal
Route::middleware(['can:documents.view'])->group(function () {
    Route::get('/documents', fn() => view('sgd.index'))->name('documents.index');
});

Route::get('/perfil', [ProfileController::class, 'show'])->name('profile.show');
Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
Route::put('/perfil/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

// Subscrição & Faturação SaaS
Route::get('/billing/plans', [BillingController::class, 'plans'])->name('billing.plans');
Route::get('/billing/checkout/{plan}', [BillingController::class, 'checkout'])->name('billing.checkout');
Route::post('/billing/pay', [BillingController::class, 'storePayment'])->name('billing.store_payment');
Route::get('/billing/history', [BillingController::class, 'history'])->name('billing.history');
Route::get('/billing/invoice/{id}/pdf', [BillingController::class, 'downloadInvoicePdf'])->name('billing.invoice.pdf');

// Administração & Configurações
Route::middleware(['can:users.view'])->group(function () {
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
});

Route::middleware(['can:roles.view'])->group(function () {
    Route::get('/admin/roles', [\App\Http\Controllers\RoleController::class, 'index'])->name('admin.roles.index');
    Route::get('/admin/roles/create', [\App\Http\Controllers\RoleController::class, 'create'])->name('admin.roles.create');
    Route::post('/admin/roles', [\App\Http\Controllers\RoleController::class, 'store'])->name('admin.roles.store');
    Route::get('/admin/roles/{id}', [\App\Http\Controllers\RoleController::class, 'show'])->name('admin.roles.show');
    Route::get('/admin/roles/{id}/edit', [\App\Http\Controllers\RoleController::class, 'edit'])->name('admin.roles.edit');
    Route::put('/admin/roles/{id}', [\App\Http\Controllers\RoleController::class, 'update'])->name('admin.roles.update');
    Route::delete('/admin/roles/{id}', [\App\Http\Controllers\RoleController::class, 'destroy'])->name('admin.roles.destroy');
});

Route::middleware(['can:companies.view'])->group(function () {
    Route::get('/admin/companies', [CompanyController::class, 'index'])->name('admin.companies.index');
    Route::get('/admin/companies/create', [CompanyController::class, 'create'])->name('admin.companies.create');
    Route::post('/admin/companies', [CompanyController::class, 'store'])->name('admin.companies.store');
    Route::get('/admin/companies/{company}', [CompanyController::class, 'show'])->name('admin.companies.show');
    Route::get('/admin/companies/{company}/edit', [CompanyController::class, 'edit'])->name('admin.companies.edit');
    Route::put('/admin/companies/{company}', [CompanyController::class, 'update'])->name('admin.companies.update');
    Route::delete('/admin/companies/{company}', [CompanyController::class, 'destroy'])->name('admin.companies.destroy');
});

Route::middleware(['can:settings.view'])->group(function () {
    Route::get('/admin/pos-registers', [PosRegisterController::class, 'indexView'])->name('admin.pos_registers.index');
    Route::get('/admin/settings', fn() => view('admin.settings'))->name('admin.settings.index');
    Route::get('/admin/integrations', [IntegrationController::class, 'index'])->name('admin.integrations.index');
    Route::post('/admin/integrations/api-keys', [IntegrationController::class, 'generateKey'])->name('admin.integrations.keys.store');
    Route::delete('/admin/integrations/api-keys/{id}', [IntegrationController::class, 'revokeKey'])->name('admin.integrations.keys.destroy');
    Route::get('/admin/backups', [SettingController::class, 'backupIndex'])->name('admin.backups.index');
    Route::post('/admin/settings/backup', [SettingController::class, 'backup'])->name('admin.settings.backup');
    Route::get('/admin/backups/download/{filename}', [SettingController::class, 'downloadBackup'])->name('admin.backups.download');
    Route::delete('/admin/backups/delete/{filename}', [SettingController::class, 'deleteBackup'])->name('admin.backups.delete');
    Route::get('/admin/agt-audit', fn() => view('admin.agt_audit', ['sales' => \App\Models\Sale::orderBy('id', 'desc')->take(10)->get()]))->name('admin.agt_audit.index');
    Route::get('/admin/performance', fn() => view('admin.performance'))->name('admin.performance.index');
    Route::get('/admin/logs', fn() => view('admin.logs'))->name('admin.logs.index');
});

// BackOffice Payments Admin
Route::middleware(['can:billing.manage'])->group(function () {
    Route::get('/admin/payments', [PaymentManagementController::class, 'index'])->name('admin.payments.index');
    Route::post('/admin/payments/{id}/approve', [PaymentManagementController::class, 'approve'])->name('admin.payments.approve');
    Route::post('/admin/payments/{id}/reject', [PaymentManagementController::class, 'reject'])->name('admin.payments.reject');
    Route::post('/admin/companies/{company}/extend-license', [PaymentManagementController::class, 'extendLicense'])->name('admin.companies.extend_license');
});

// AI Admin
Route::middleware(['can:settings.view'])->group(function () {
    Route::get('/ai/admin/dashboard', fn() => view('ai.admin.dashboard'))->name('ai.admin.dashboard');
    Route::get('/ai/admin/agents', fn() => view('ai.admin.agents'))->name('ai.admin.agents');
    Route::get('/ai/admin/providers', fn() => view('ai.admin.providers'))->name('ai.admin.providers');
    Route::get('/ai/admin/models', fn() => view('ai.admin.models'))->name('ai.admin.models');
    Route::get('/ai/admin/tools', fn() => view('ai.admin.tools'))->name('ai.admin.tools');
    Route::get('/ai/admin/conversations', fn() => view('ai.admin.conversations'))->name('ai.admin.conversations');
});

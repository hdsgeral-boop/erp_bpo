<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP Consulvolt - Sistema de Gestão Empresarial</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap 5.3 Framework -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- CSS Nativo Personalizado -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
    <!-- jQuery (Compatibilidade) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    @stack('styles')
</head>
<body>
    <!-- Mobile Sidebar Backdrop Overlay -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="app-layout">
        <!-- Sidebar Navigation (Fixed Layout) -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-building text-primary" style="font-size: 1.5rem; color: #3b82f6;"></i>
                <div>
                    <h1 class="brand-title" style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #f8fafc; letter-spacing: -0.5px;">ERP_CONSULT</h1>
                    <span class="brand-subtitle" style="font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Gestão Empresarial</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <!-- Dashboard Global -->
                @can('dashboard.view')
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Dashboard Global">
                    <i class="fas fa-chart-line main-icon"></i> <span class="nav-label">Dashboard Global</span>
                </a>
                @endcan

                <!-- Business Intelligence -->
                @can('bi.view')
                <a href="{{ route('bi.view') }}" class="nav-item {{ request()->routeIs('bi.*') ? 'active' : '' }}" title="Business Intelligence">
                    <i class="fas fa-chart-pie main-icon"></i> <span class="nav-label">Business Intelligence</span>
                </a>
                @endcan

                <!-- Módulo de Logística -->
                @can('inventory.view')
                <div class="nav-group {{ request()->is('logistica*') ? 'open' : '' }}">
                    <div class="nav-group-header" title="Logística">
                        <i class="fas fa-boxes main-icon"></i>
                        <span class="nav-label">Logística</span>
                        <i class="fas fa-chevron-down arrow-icon"></i>
                    </div>
                    <div class="nav-group-items">
                        <div class="nav-group {{ request()->is('logistica/stock*') || request()->is('logistica/products*') ? 'open' : '' }}">
                            <div class="nav-group-header" title="Gestão de Armazém">
                                <i class="fas fa-warehouse main-icon"></i>
                                <span class="nav-label">Gestão de Armazém</span>
                                <i class="fas fa-chevron-down arrow-icon"></i>
                            </div>
                            <div class="nav-group-items">
                                <a href="{{ route('logistica.stock') }}" class="nav-item"><i class="fas fa-cubes"></i> <span class="nav-label">Níveis de Stock</span></a>
                                <a href="{{ route('logistica.guias.index') }}" class="nav-item"><i class="fas fa-truck-loading"></i> <span class="nav-label">Guias de Saída</span></a>
                                <a href="{{ route('logistica.movements.index') }}" class="nav-item"><i class="fas fa-history"></i> <span class="nav-label">Histórico Movimentos</span></a>
                                <a href="{{ route('logistica.warehouses.index') }}" class="nav-item"><i class="fas fa-store"></i> <span class="nav-label">Armazéns</span></a>
                                <a href="{{ route('product_categories.index') }}" class="nav-item"><i class="fas fa-tags"></i> <span class="nav-label">Categorias</span></a>
                                <a href="{{ route('logistica.products.index') }}" class="nav-item"><i class="fas fa-box"></i> <span class="nav-label">Produtos</span></a>
                            </div>
                        </div>

                        @can('pos.access')
                        <a href="{{ route('vendas.pos.index') }}" class="nav-item"><i class="fas fa-cash-register"></i> <span class="nav-label">POS de Armazém</span></a>
                        @endcan

                        <div class="nav-group {{ request()->is('logistica/inventario*') ? 'open' : '' }}">
                            <div class="nav-group-header" title="Inventário de Stock">
                                <i class="fas fa-clipboard-check main-icon"></i>
                                <span class="nav-label">Inventário de Stock</span>
                                <i class="fas fa-chevron-down arrow-icon"></i>
                            </div>
                            <div class="nav-group-items">
                                <a href="{{ route('logistica.inventario.index') }}" class="nav-item"><i class="fas fa-list-alt"></i> <span class="nav-label">Sessões de Inventário</span></a>
                                <a href="{{ route('logistica.inventario.contagem', 0) }}" class="nav-item"><i class="fas fa-barcode"></i> <span class="nav-label">Efetuar Contagem</span></a>
                                <a href="{{ route('logistica.inventario.review', 0) }}" class="nav-item"><i class="fas fa-balance-scale"></i> <span class="nav-label">Revisão e Regularização</span></a>
                            </div>
                        </div>
                    </div>
                </div>
                @endcan

                <!-- Módulo de Vendas -->
                @canany(['sales.view', 'pos.access'])
                <div class="nav-group {{ request()->is('vendas*') ? 'open' : '' }}">
                    <div class="nav-group-header" title="Vendas & Faturação">
                        <i class="fas fa-shopping-cart main-icon"></i>
                        <span class="nav-label">Vendas & Faturação</span>
                        <i class="fas fa-chevron-down arrow-icon"></i>
                    </div>
                    <div class="nav-group-items">
                        @can('pos.access')
                        <a href="{{ route('vendas.pos.index') }}" class="nav-item"><i class="fas fa-desktop"></i> <span class="nav-label">Frente de Caixa (POS)</span></a>
                        @endcan
                        @can('sales.view')
                        <a href="{{ route('vendas.documentos.index', 'orcamentos') }}" class="nav-item"><i class="fas fa-file-signature"></i> <span class="nav-label">Orçamentos e Cotações</span></a>
                        <a href="{{ route('vendas.documentos.index', 'faturas') }}" class="nav-item"><i class="fas fa-file-invoice-dollar"></i> <span class="nav-label">Faturas e Emitidos</span></a>
                        <a href="{{ route('vendas.documentos.index', 'guias') }}" class="nav-item"><i class="fas fa-truck-loading"></i> <span class="nav-label">Guias de Transporte</span></a>
                        <a href="{{ route('vendas.documentos.index', 'notas') }}" class="nav-item"><i class="fas fa-file-invoice"></i> <span class="nav-label">Notas Cr./Débito</span></a>
                        @endcan
                        @can('saft.export')
                        <a href="{{ route('vendas.saft') }}" class="nav-item"><i class="fas fa-file-code"></i> <span class="nav-label">Exportar SAF-T AO</span></a>
                        @endcan
                    </div>
                </div>
                @endcanany

                <!-- Salários e RH -->
                @can('hr.view')
                <div class="nav-group {{ request()->is('rh*') ? 'open' : '' }}">
                    <div class="nav-group-header" title="Salários e RH">
                        <i class="fas fa-users-cog main-icon"></i>
                        <span class="nav-label">Salários e RH</span>
                        <i class="fas fa-chevron-down arrow-icon"></i>
                    </div>
                    <div class="nav-group-items">
                        <a href="{{ route('rh.funcionarios.index') }}" class="nav-item"><i class="fas fa-user-friends"></i> <span class="nav-label">Colaboradores</span></a>
                        <a href="{{ route('rh.contratos.index') }}" class="nav-item"><i class="fas fa-file-contract"></i> <span class="nav-label">Contratos</span></a>
                        <a href="{{ route('rh.assiduidade.index') }}" class="nav-item"><i class="fas fa-clock"></i> <span class="nav-label">Assiduidade</span></a>
                        <a href="{{ route('rh.ausencias.index') }}" class="nav-item"><i class="fas fa-plane-departure"></i> <span class="nav-label">Férias & Ausências</span></a>
                        <a href="{{ route('rh.horas-extra.index') }}" class="nav-item"><i class="fas fa-stopwatch"></i> <span class="nav-label">Horas Extras</span></a>
                        <a href="{{ route('rh.beneficios.index') }}" class="nav-item"><i class="fas fa-gift"></i> <span class="nav-label">Benefícios/Deduções</span></a>
                        <a href="{{ route('rh.salarios.wizard') }}" class="nav-item"><i class="fas fa-calculator"></i> <span class="nav-label">Processamento Salarial</span></a>
                        <a href="{{ route('rh.reports.inss') }}" class="nav-item"><i class="fas fa-shield-alt text-primary"></i> <span class="nav-label">Mapa de INSS</span></a>
                        <a href="{{ route('rh.reports.bank') }}" class="nav-item"><i class="fas fa-university text-success"></i> <span class="nav-label">Transferência Bancária</span></a>
                        <hr class="dropdown-divider my-2 border-secondary" style="border-color: #334155;">
                        <a href="{{ route('rh.infotipos.index') }}" class="nav-item small text-muted"><i class="fas fa-list-ul"></i> <span class="nav-label">Infotipos</span></a>
                        <a href="{{ route('rh.escaloes-irt.index') }}" class="nav-item small text-muted"><i class="fas fa-layer-group"></i> <span class="nav-label">Escalões IRT</span></a>
                        <a href="{{ route('rh.taxas-salariais.index') }}" class="nav-item small text-muted"><i class="fas fa-percent"></i> <span class="nav-label">Taxas (INSS)</span></a>
                    </div>
                </div>
                @endcan

                <!-- Entidades -->
                @can('third_parties.view')
                <a href="{{ route('entidades.index') }}" class="nav-item {{ request()->routeIs('entidades.*') ? 'active' : '' }}" title="Entidades (Terceiros)">
                    <i class="fas fa-address-book main-icon"></i> <span class="nav-label">Entidades (Terceiros)</span>
                </a>
                @endcan

                <!-- Compras -->
                @can('purchases.view')
                <div class="nav-group {{ request()->is('compras*') ? 'open' : '' }}">
                    <div class="nav-group-header" title="Compras">
                        <i class="fas fa-shopping-bag main-icon"></i>
                        <span class="nav-label">Compras</span>
                        <i class="fas fa-chevron-down arrow-icon"></i>
                    </div>
                    <div class="nav-group-items">
                        <a href="{{ route('compras.pedidos.index') }}" class="nav-item"><i class="fas fa-clipboard-list"></i> <span class="nav-label">Pedidos de Compra</span></a>
                        <a href="{{ route('compras.encomendas.index') }}" class="nav-item"><i class="fas fa-file-contract"></i> <span class="nav-label">Encomendas (PO)</span></a>
                        <a href="{{ route('compras.rececoes.index') }}" class="nav-item"><i class="fas fa-box-open"></i> <span class="nav-label">Receção (Guias)</span></a>
                        <a href="{{ route('compras.faturas.index') }}" class="nav-item"><i class="fas fa-file-invoice-dollar"></i> <span class="nav-label">Faturas Fornecedor</span></a>
                    </div>
                </div>
                @endcan

                <!-- Tesouraria -->
                @can('treasury.view')
                <div class="nav-group {{ request()->is('tesouraria*') ? 'open' : '' }}">
                    <div class="nav-group-header" title="Tesouraria">
                        <i class="fas fa-university main-icon"></i>
                        <span class="nav-label">Tesouraria</span>
                        <i class="fas fa-chevron-down arrow-icon"></i>
                    </div>
                    <div class="nav-group-items">
                        <a href="{{ route('tesouraria.accounts.index') }}" class="nav-item"><i class="fas fa-landmark"></i> <span class="nav-label">Contas Bancárias / Caixas</span></a>
                        <a href="{{ route('tesouraria.documents.index', 'recebimentos') }}" class="nav-item"><i class="fas fa-hand-holding-usd text-success"></i> <span class="nav-label">Recebimentos (Clientes)</span></a>
                        <a href="{{ route('tesouraria.documents.index', 'pagamentos') }}" class="nav-item"><i class="fas fa-money-check-alt text-danger"></i> <span class="nav-label">Pagamentos (Fornecedores)</span></a>
                        <a href="{{ route('tesouraria.bank_statements.index') }}" class="nav-item"><i class="fas fa-file-invoice-dollar"></i> <span class="nav-label">Extratos de Contas</span></a>
                        <a href="{{ route('tesouraria.aging') }}" class="nav-item"><i class="fas fa-clock text-warning"></i> <span class="nav-label">Idade dos Saldos (Aging)</span></a>
                    </div>
                </div>
                @endcan

                <!-- Ativos Fixos -->
                @can('assets.view')
                <a href="{{ route('ativos.index') }}" class="nav-item {{ request()->routeIs('ativos.*') ? 'active' : '' }}" title="Ativos Fixos">
                    <i class="fas fa-boxes main-icon"></i> <span class="nav-label">Ativos Fixos</span>
                </a>
                @endcan

                <!-- Contabilidade -->
                @can('accounting.view')
                <div class="nav-group {{ request()->is('contabilidade*') ? 'open' : '' }}">
                    <div class="nav-group-header" title="Contabilidade PGC">
                        <i class="fas fa-book main-icon"></i>
                        <span class="nav-label">Contabilidade PGC</span>
                        <i class="fas fa-chevron-down arrow-icon"></i>
                    </div>
                    <div class="nav-group-items">
                        <a href="{{ route('contabilidade.relatorios') }}" class="nav-item"><i class="fas fa-chart-line text-info"></i> <span class="nav-label">Relatórios & Balanços</span></a>
                        <a href="{{ route('contabilidade.chart_of_accounts.index') }}" class="nav-item"><i class="fas fa-list-ol"></i> <span class="nav-label">Plano de Contas PGC</span></a>
                        <a href="{{ route('contabilidade.journals.index') }}" class="nav-item"><i class="fas fa-book"></i> <span class="nav-label">Diários / Lançamentos</span></a>
                        <a href="{{ route('contabilidade.maps.index') }}" class="nav-item"><i class="fas fa-project-diagram"></i> <span class="nav-label">Mapeamentos</span></a>
                    </div>
                </div>
                @endcan

                <!-- SGD / Arquivo -->
                @can('documents.view')
                <a href="{{ route('documents.index') }}" class="nav-item {{ request()->routeIs('documents.*') ? 'active' : '' }}" title="Gestão Documental">
                    <i class="fas fa-archive main-icon"></i> <span class="nav-label">Gestão Documental</span>
                </a>
                @endcan

                <!-- Configurações & Administração -->
                @canany(['users.view', 'roles.view', 'companies.view', 'settings.view', 'billing.view', 'billing.manage'])
                <div class="nav-group {{ request()->is('admin*') || request()->is('billing*') ? 'open' : '' }}">
                    <div class="nav-group-header" title="Configurações & Adm.">
                        <i class="fas fa-cog main-icon"></i>
                        <span class="nav-label">Configurações & Adm.</span>
                        <i class="fas fa-chevron-down arrow-icon"></i>
                    </div>
                    <div class="nav-group-items">
                        @can('billing.view')
                        <a href="{{ route('billing.plans') }}" class="nav-item {{ request()->routeIs('billing.*') ? 'active' : '' }}"><i class="fas fa-credit-card text-success"></i> <span class="nav-label">Subscrição & Licenças</span></a>
                        @endcan
                        @can('billing.manage')
                        <a href="{{ route('admin.payments.index') }}" class="nav-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}"><i class="fas fa-user-shield text-warning"></i> <span class="nav-label">Gestão de Pagamentos (BackOffice)</span></a>
                        @endcan
                        @can('settings.view')
                        <a href="{{ route('admin.settings.index') }}" class="nav-item"><i class="fas fa-sliders-h"></i> <span class="nav-label">Definições Globais</span></a>
                        <a href="{{ route('admin.integrations.index') }}" class="nav-item {{ request()->routeIs('admin.integrations.*') ? 'active' : '' }}"><i class="fas fa-plug text-primary"></i> <span class="nav-label">Integrações (API/PowerBI)</span></a>
                        @endcan
                        @can('users.view')
                        <a href="{{ route('admin.users.index') }}" class="nav-item"><i class="fas fa-users"></i> <span class="nav-label">Utilizadores</span></a>
                        @endcan
                        @can('roles.view')
                        <a href="{{ route('admin.roles.index') }}" class="nav-item"><i class="fas fa-user-shield"></i> <span class="nav-label">Perfis e Permissões</span></a>
                        @endcan
                        @can('companies.view')
                        <a href="{{ route('admin.companies.index') }}" class="nav-item"><i class="fas fa-building"></i> <span class="nav-label">Empresas</span></a>
                        @endcan
                        @can('settings.view')
                        <a href="{{ route('admin.backups.index') }}" class="nav-item"><i class="fas fa-database text-info"></i> <span class="nav-label">Gestão de Backups</span></a>
                        <a href="{{ route('admin.agt_audit.index') }}" class="nav-item"><i class="fas fa-shield-alt text-success"></i> <span class="nav-label">Auditoria AGT</span></a>
                        <a href="{{ route('admin.performance.index') }}" class="nav-item"><i class="fas fa-tachometer-alt text-warning"></i> <span class="nav-label">Desempenho & Cache</span></a>
                        <a href="{{ route('admin.logs.index') }}" class="nav-item"><i class="fas fa-history"></i> <span class="nav-label">Logs de Auditoria</span></a>
                        @endcan
                    </div>
                </div>
                @endcanany

                <hr style="border-color: rgba(255, 255, 255, 0.08); margin: 1rem 1rem;">

                <!-- Botão Sair do Sistema (Sidebar) -->
                <form method="POST" action="{{ route('logout') }}" style="margin: 0 1rem 1.5rem;">
                    @csrf
                    <button type="submit" class="nav-item w-100 border-0" title="Sair do Sistema" style="background: rgba(239, 68, 68, 0.12); color: #f87171; border-radius: 10px; cursor: pointer; padding: 0.75rem 1rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s;">
                        <i class="fas fa-sign-out-alt main-icon"></i> <span class="nav-label">Sair do Sistema</span>
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content (Adjusted Margin for Fixed Sidebar) -->
        <main class="main-content">
            <!-- Header Topbar (Sticky Layout) -->
            <header class="top-header">
                <div class="d-flex align-items-center gap-3">
                    <button id="sidebar-toggle" class="btn btn-light border shadow-sm p-2 d-flex justify-content-center align-items-center" style="width: 40px; height: 40px; border-radius: 10px;">
                        <i class="fas fa-bars text-secondary fs-5"></i>
                    </button>

                    @php
                        $userObj = auth()->user();
                        $opCompanies = $userObj ? ($userObj->hasRole('Super Admin') ? \App\Models\Company::all() : $userObj->companies) : collect([]);
                        if ($opCompanies->isEmpty()) {
                            $opCompanies = \App\Models\Company::where('name', 'not like', '%SISTEMA%')->get();
                        }
                        $activeCompanyId = session('company_id') ?? ($opCompanies->first()?->id ?? 1);
                        $activeCompany = \App\Models\Company::find($activeCompanyId) ?? $opCompanies->first();
                        $daysRemaining = $activeCompany ? $activeCompany->remaining_days : 30;
                        $isLicenseActive = $activeCompany ? $activeCompany->isLicenseActive() : true;
                        $isTrial = $activeCompany ? ($activeCompany->subscription_status === 'trial') : false;
                    @endphp

                    <div class="d-flex align-items-center gap-2">
                        <form id="company-switch-form" method="POST" action="{{ route('company.switch') }}" class="m-0">
                            @csrf
                            <select name="company_id" onchange="this.form.submit()" class="form-select border shadow-sm fw-bold text-truncate" style="background: #f8fafc; border-color: #cbd5e1; font-size: 0.85rem; border-radius: 10px; cursor: pointer; max-width: 220px;" title="Alternar Organização Ativa">
                                @foreach($opCompanies as $comp)
                                    <option value="{{ $comp->id }}" {{ $comp->id == $activeCompanyId ? 'selected' : '' }}>
                                        🏢 {{ $comp->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>

                        <a href="{{ route('billing.plans') }}" class="btn btn-sm d-flex align-items-center gap-1 gap-md-2 fw-bold text-decoration-none px-2 px-md-3 py-2 shadow-sm" style="border-radius: 10px; background: {{ $isLicenseActive ? ($daysRemaining <= 5 ? '#fef3c7' : '#dcfce7') : '#fee2e2' }}; color: {{ $isLicenseActive ? ($daysRemaining <= 5 ? '#b45309' : '#15803d') : '#b91c1c' }}; border: 1px solid {{ $isLicenseActive ? ($daysRemaining <= 5 ? '#fde68a' : '#bbf7d0') : '#fecaca' }}; font-size: 0.8rem;" title="Clique para gerir a subscrição da empresa">
                            <i class="fas {{ $isLicenseActive ? ($daysRemaining <= 5 ? 'fa-clock' : 'fa-check-circle') : 'fa-lock' }}"></i>
                            <span class="d-none d-sm-inline">{{ $isTrial ? 'Trial: ' : 'Licença: ' }}</span><span>{{ $daysRemaining }}d</span>
                        </a>
                    </div>
                </div>

                <!-- Dropdown Utilizador Logado (Estilizado & Responsivo) -->
                <div class="header-actions">
                    <div class="dropdown">
                        <button class="btn border-0 bg-transparent p-1 d-flex align-items-center gap-3 shadow-none" type="button" id="userProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar-circle">
                                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
                            </div>
                            <div class="d-none d-md-block text-start">
                                <div class="fw-bold text-dark fs-7 lh-1 mb-1">
                                    @auth {{ auth()->user()->name }} @else Administrador / Gestor @endauth
                                </div>
                                <div class="text-primary fs-8 fw-semibold lh-1">
                                    @auth {{ auth()->user()->roles->first()?->name ?? 'Super Admin' }} @else Perfil Principal @endauth
                                </div>
                            </div>
                            <i class="fas fa-chevron-down text-muted fs-8"></i>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-2" aria-labelledby="userProfileDropdown" style="border-radius: 16px; min-width: 250px;">
                            <li class="px-3 py-2 border-bottom mb-2 bg-light rounded-3">
                                <div class="fw-bold text-dark fs-7">
                                    @auth {{ auth()->user()->name }} @else Administrador / Gestor @endauth
                                </div>
                                <div class="text-muted fs-8 text-truncate">
                                    @auth {{ auth()->user()->email }} @else admin@consulvolt.com @endauth
                                </div>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-2 py-2 fs-7 fw-semibold" href="{{ route('profile.show') }}">
                                    <i class="fas fa-user-circle text-primary me-2 width-20"></i> Dados Pessoais
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-2 py-2 fs-7 fw-semibold" href="{{ route('admin.users.index') }}">
                                    <i class="fas fa-users-cog text-success me-2 width-20"></i> Gerir Utilizadores
                                </a>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item rounded-2 py-2 fs-7 fw-semibold" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                    <i class="fas fa-key text-warning me-2 width-20"></i> Alterar Senha
                                </button>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-2 py-2 fs-7 fw-semibold" href="{{ route('admin.companies.index') }}">
                                    <i class="fas fa-building text-info me-2 width-20"></i> Minha Empresa
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-2 py-2 fs-7 fw-semibold" href="{{ route('admin.logs.index') }}">
                                    <i class="fas fa-history text-secondary me-2 width-20"></i> Logs de Atividade
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-2 border-light"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item rounded-2 py-2 fs-7 fw-bold text-danger">
                                        <i class="fas fa-sign-out-alt me-2 width-20"></i> Sair do Sistema
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Dynamic Views Container -->
            <div id="view-container" class="view-container">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Modal Alterar Senha -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-key text-warning me-2"></i>Alterar Palavra-passe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form action="{{ route('admin.users.index') }}" method="GET">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Palavra-passe Atual <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" class="form-control" placeholder="••••••••" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Nova Palavra-passe <span class="text-danger">*</span></label>
                            <input type="password" name="new_password" class="form-control" placeholder="Mínimo 8 caracteres" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Confirmar Nova Palavra-passe <span class="text-danger">*</span></label>
                            <input type="password" name="new_password_confirmation" class="form-control" placeholder="Repita a nova palavra-passe" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light border fw-bold" data-bs-dismiss="modal" style="border-radius:8px;">Cancelar</button>
                        <button type="submit" class="btn btn-primary fw-bold" style="border-radius:8px;"><i class="fas fa-save me-1"></i> Atualizar Palavra-passe</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    if (window.innerWidth <= 1024) {
                        document.body.classList.toggle('sidebar-active');
                    } else {
                        document.body.classList.toggle('sidebar-collapsed');
                    }
                });
            }

            if (sidebarBackdrop) {
                sidebarBackdrop.addEventListener('click', function() {
                    document.body.classList.remove('sidebar-active');
                });
            }

            // Accordion Lógica para Menus Laterais
            $('.nav-group:not(.open) > .nav-group-items').hide();
            
            $('.nav-group-header').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const currentGroup = $(this).parent('.nav-group');
                const currentItems = currentGroup.children('.nav-group-items');
                
                currentGroup.siblings('.nav-group').removeClass('open').children('.nav-group-items').slideUp(200);
                currentGroup.toggleClass('open');
                currentItems.slideToggle(200);
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

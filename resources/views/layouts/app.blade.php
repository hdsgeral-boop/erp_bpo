<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- 
        Segurança CSRF: O Token CSRF é injetado globalmente para ser 
        acessível via JavaScript (Axios/Fetch), impedindo ataques de falsificação
        de requisições cross-site.
    -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ERP_CONSULT v2.2') }}</title>

    <!-- External CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    
    <!-- Core Libraries -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    @stack('styles')
</head>
<body>
    <div class="app-container" id="main-app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header" style="display: flex; align-items: center; gap: 12px; padding: 1.5rem;">
                <img src="{{ asset('img/logo_erp.png') }}" onerror="this.style.visibility='hidden'" style="height: 40px; width: 40px; object-fit: contain; border-radius: 4px;">
                <div>
                    <h2 style="margin: 0; font-size: 1.2rem; font-weight: 700; color: white;">ERP_CONSULT</h2>
                    <p style="margin: 0; font-size: 0.7rem; color: #94a3b8;">Gestão Empresarial</p>
                </div>
            </div>
            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" style="margin: 0.5rem 1rem; border-radius: 12px; margin-bottom: 0.5rem;">
                    <i class="fas fa-chart-line"></i> Dashboard Global
                </a>

                <!-- Logística -->
                <div class="nav-group {{ request()->is('logistica*') ? 'open' : '' }}">
                    <div class="nav-group-header">
                        <span><i class="fas fa-warehouse"></i> Logística</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="nav-group-items">
                        <div class="nav-group">
                            <div class="nav-group-header">
                                <span><i class="fas fa-boxes"></i> Gestão de Armazém</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="nav-group-items">
                                <a href="{{ route('logistica.stock') }}" class="nav-item">Níveis de Stock</a>
                                <a href="{{ route('logistica.rececoes.index') }}" class="nav-item">Validar Entradas</a>
                                <a href="{{ route('logistica.guias.index') }}" class="nav-item">Guias de Saída</a>
                                <a href="{{ route('logistica.movements.index') }}" class="nav-item">Histórico</a>
                                <a href="{{ route('logistica.warehouses.index') }}" class="nav-item">Config. Armazéns</a>
                                <a href="{{ route('logistica.categories.index') }}" class="nav-item">Categorias</a>
                                <a href="{{ route('logistica.products.index') }}" class="nav-item">Produtos</a>
                            </div>
                        </div>

                        <a href="{{ route('logistica.pos.balcao') }}" class="nav-item"><i class="fas fa-cash-register"></i> POS de Armazém</a>
                        
                        <div class="nav-group">
                            <div class="nav-group-header">
                                <span><i class="fas fa-clipboard-check"></i> Inventário de Stock</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="nav-group-items">
                                <a href="{{ route('logistica.inventario.index') }}" class="nav-item"><i class="fas fa-tasks"></i> Sessões de Inventário</a>
                                <a href="{{ route('logistica.inventario.contagem', 0) }}" class="nav-item"><i class="fas fa-list-ol"></i> Efetuar Contagem</a>
                                <a href="{{ route('logistica.inventario.review', 0) }}" class="nav-item"><i class="fas fa-balance-scale"></i> Revisão e Regularização</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Módulo de Vendas -->
                @can('sales.view')
                <div class="nav-group {{ request()->is('vendas*') ? 'open' : '' }}">
                    <div class="nav-group-header">
                        <span><i class="fas fa-shopping-cart"></i> Vendas</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="nav-group-items" style="padding-left: 0.5rem;">
                        <div class="nav-group {{ request()->is('vendas/pos*') ? 'open' : '' }}">
                            <div class="nav-group-header">
                                <span><i class="fas fa-cash-register"></i> Ponto de Venda (POS)</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="nav-group-items">
                                <a href="{{ route('vendas.pos.index') }}" class="nav-item"><i class="fas fa-desktop"></i> Frente de Caixa</a>
                            </div>
                        </div>
                        <a href="{{ route('vendas.documentos.index', 'orcamentos') }}" class="nav-item"><i class="fas fa-file-signature"></i> Orçamentos e Cotações</a>
                        <a href="{{ route('vendas.documentos.index', 'faturas') }}" class="nav-item"><i class="fas fa-file-invoice-dollar"></i> Faturas e Emitidos</a>
                        <a href="{{ route('vendas.documentos.index', 'guias') }}" class="nav-item"><i class="fas fa-truck-loading"></i> Guias de Transporte</a>
                        <a href="{{ route('vendas.documentos.index', 'notas') }}" class="nav-item"><i class="fas fa-file-invoice"></i> Notas Cr./Débito</a>
                        <a href="{{ route('vendas.saft') }}" class="nav-item"><i class="fas fa-file-code"></i> Exportar SAFT-AO</a>
                    </div>
                </div>
                @endcan

                <!-- Salários e RH -->
                @can('hr.view')
                <div class="nav-group {{ request()->is('rh*') ? 'open' : '' }}">
                    <div class="nav-group-header">
                        <span><i class="fas fa-users"></i> Salários e RH</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="nav-group-items">
                        <a href="{{ route('rh.funcionarios.index') }}" class="nav-item"><i class="fas fa-user-friends"></i> Colaboradores</a>
                        <a href="{{ route('rh.contratos.index') }}" class="nav-item"><i class="fas fa-file-contract"></i> Contratos</a>
                        <a href="{{ route('rh.assiduidade.index') }}" class="nav-item"><i class="fas fa-clock"></i> Assiduidade</a>
                        <a href="{{ route('rh.ausencias.index') }}" class="nav-item"><i class="fas fa-plane-departure"></i> Férias & Ausências</a>
                        <a href="{{ route('rh.horas-extra.index') }}" class="nav-item"><i class="fas fa-stopwatch"></i> Horas Extras</a>
                        <a href="{{ route('rh.beneficios.index') }}" class="nav-item"><i class="fas fa-gift"></i> Benefícios/Deduções</a>
                        <a href="{{ route('rh.salarios.wizard') }}" class="nav-item"><i class="fas fa-calculator"></i> Processamento Salarial</a>
                        <hr class="dropdown-divider my-2 border-secondary">
                        <a href="{{ route('rh.infotipos.index') }}" class="nav-item small text-muted"><i class="fas fa-list-ul"></i> Infotipos</a>
                        <a href="{{ route('rh.escaloes-irt.index') }}" class="nav-item small text-muted"><i class="fas fa-layer-group"></i> Escalões IRT</a>
                        <a href="{{ route('rh.taxas-salariais.index') }}" class="nav-item small text-muted"><i class="fas fa-percent"></i> Taxas (INSS)</a>
                    </div>
                </div>
                @endcan

                <!-- Entidades -->
                @canany(['sales.view', 'purchases.view', 'settings.view'])
                <a href="{{ route('entidades.index') }}" class="nav-item" style="margin: 0.25rem 1rem; border-radius: 12px;">
                    <i class="fas fa-address-book"></i> Entidades
                </a>
                @endcanany

                <!-- Compras -->
                @can('purchases.view')
                <div class="nav-group {{ request()->is('compras*') ? 'open' : '' }}">
                    <div class="nav-group-header">
                        <span><i class="fas fa-shopping-bag"></i> Compras</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="nav-group-items">
                        <a href="{{ route('compras.pedidos.index') }}" class="nav-item"><i class="fas fa-clipboard-list"></i> Pedidos de Compra</a>
                        <a href="{{ route('compras.encomendas.index') }}" class="nav-item"><i class="fas fa-file-contract"></i> Encomendas (PO)</a>
                        <a href="{{ route('compras.rececoes.index') }}" class="nav-item"><i class="fas fa-box-open"></i> Receção (Guias)</a>
                        <a href="{{ route('compras.faturas.index') }}" class="nav-item"><i class="fas fa-file-invoice-dollar"></i> Faturas Fornecedor</a>
                    </div>
                </div>
                @endcan

                <!-- Tesouraria -->
                @can('treasury.view')
                <div class="nav-group {{ request()->is('tesouraria*') ? 'open' : '' }}">
                    <div class="nav-group-header">
                        <span><i class="fas fa-university"></i> Tesouraria</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="nav-group-items">
                        <a href="{{ route('tesouraria.accounts.index') }}" class="nav-item"><i class="fas fa-landmark"></i> Contas Bancárias / Caixas</a>
                        <a href="{{ route('tesouraria.documentos.index', 'recebimentos') }}" class="nav-item"><i class="fas fa-hand-holding-usd text-success"></i> Recebimentos (Clientes)</a>
                        <a href="{{ route('tesouraria.documentos.index', 'pagamentos') }}" class="nav-item"><i class="fas fa-money-check-alt text-danger"></i> Pagamentos (Fornecedores)</a>
                        <a href="{{ route('tesouraria.bank_statements.index') }}" class="nav-item"><i class="fas fa-file-invoice-dollar"></i> Extratos de Contas</a>
                    </div>
                </div>
                @endcan

                <!-- Ativos (Gestão de Património) -->
                @hasanyrole('Super Admin|Administrador|Gestor')
                <a href="{{ route('ativos.index') }}" class="nav-item {{ request()->routeIs('ativos.*') ? 'active' : '' }}" style="margin: 0.25rem 1rem; border-radius: 12px;">
                    <i class="fas fa-boxes"></i> Ativos Fixos
                </a>
                @endhasanyrole

                <!-- Contabilidade -->
                @hasanyrole('Super Admin|Administrador|Gestor')
                <div class="nav-group {{ request()->is('contabilidade*') ? 'open' : '' }}">
                    <div class="nav-group-header">
                        <span><i class="fas fa-book"></i> Contabilidade</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="nav-group-items">
                        <a href="{{ route('contabilidade.chart_of_accounts.index') }}" class="nav-item"><i class="fas fa-list-ol"></i> Plano de Contas</a>
                        <a href="{{ route('contabilidade.journals.index') }}" class="nav-item"><i class="fas fa-book"></i> Diários / Lançamentos</a>
                        <a href="{{ route('contabilidade.maps.index') }}" class="nav-item"><i class="fas fa-project-diagram"></i> Mapeamentos</a>
                    </div>
                </div>
                @endhasanyrole

                <!-- SGD / Arquivo -->
                @can('documents.view')
                <a href="{{ route('documents.index') }}" class="nav-item {{ request()->routeIs('documents.*') ? 'active' : '' }}" style="margin: 0.25rem 1rem; border-radius: 12px;">
                    <i class="fas fa-archive"></i> Gestão Documental
                </a>
                @endcan

                <!-- Configurações -->
                @can('settings.view')
                <div class="nav-group {{ request()->is('admin*') ? 'open' : '' }}">
                    <div class="nav-group-header">
                        <span><i class="fas fa-cog"></i> Configurações & Adm.</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="nav-group-items">
                        <a href="{{ route('admin.settings.index') }}" class="nav-item"><i class="fas fa-sliders-h"></i> Definições Globais</a>
                        
                        <div class="nav-group {{ request()->is('ai*') ? 'open' : '' }}">
                            <div class="nav-group-header">
                                <span><i class="fas fa-microchip"></i> Recursos Avançados</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="nav-group-items">
                                <a href="{{ route('ai.admin.dashboard') }}" class="nav-item"><i class="fas fa-chart-line"></i> Dashboard</a>
                                <a href="{{ route('ai.admin.agents') }}" class="nav-item"><i class="fas fa-network-wired"></i> Agentes</a>
                                <a href="{{ route('ai.admin.providers') }}" class="nav-item"><i class="fas fa-server"></i> Providers</a>
                                <a href="{{ route('ai.admin.models') }}" class="nav-item"><i class="fas fa-cubes"></i> Modelos</a>
                                <a href="{{ route('ai.admin.tools') }}" class="nav-item"><i class="fas fa-tools"></i> Ferramentas</a>
                                <a href="{{ route('ai.admin.conversations') }}" class="nav-item"><i class="fas fa-comment-dots"></i> Conversas</a>
                            </div>
                        </div>

                        <a href="{{ route('admin.users.index') }}" class="nav-item"><i class="fas fa-users"></i> Utilizadores</a>
                        <a href="{{ route('admin.roles.index') }}" class="nav-item"><i class="fas fa-user-shield"></i> Perfis e Permissões</a>
                        <a href="{{ route('admin.companies.index') }}" class="nav-item"><i class="fas fa-building"></i> Empresas</a>
                        <a href="{{ route('admin.logs.index') }}" class="nav-item"><i class="fas fa-history"></i> Auditoria</a>
                    </div>
                </div>
                @endcan

            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="top-header">
                <div style="display:flex; align-items:center; gap:1rem;">
                    <button id="sidebar-toggle" class="btn btn-outline" style="padding:0.5rem; width:40px; display: inline-flex; justify-content: center; align-items: center;" onclick="if(window.innerWidth <= 1024) { document.body.classList.toggle('sidebar-active'); } else { document.body.classList.toggle('sidebar-collapsed'); }">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div style="display:flex; align-items:center; gap:0.75rem;">
                        <div class="header-info">
                            <h1 id="active-company-name">Empresa Padrão</h1>
                        </div>
                    </div>
                </div>
                <div class="header-actions" style="display:flex; align-items:center; gap:0.75rem;">
                    <div class="user-profile" style="display: flex; align-items: center; gap: 0.75rem; padding-left: 0.75rem; border-left: 1px solid #e2e8f0;">
                        <div style="text-align: right;">
                            <p id="current-user-name" style="margin: 0; font-size: 0.875rem; font-weight: 600; color: #1e293b;">@auth {{ auth()->user()->name }} @else Convidado @endauth</p>
                            <p id="current-user-role" style="margin: 0; font-size: 0.7rem; color: #64748b;">Role</p>
                        </div>
                        @auth
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline" title="Sair do Sistema" style="padding: 0.5rem; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; color: #ef4444; border-color: #fee2e2;">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                        @endauth
                    </div>
                </div>
            </header>

            <!-- Dynamic Views -->
            <div id="view-container" class="view-container">
                @yield('content')
            </div>
        </main>
    </div>



    @stack('scripts')
    <script>
        document.addEventListener('click', function(event) {
            if(window.innerWidth <= 1024) {
                const sidebar = document.querySelector('.sidebar');
                const toggle = document.getElementById('sidebar-toggle');
                if(sidebar && toggle && !sidebar.contains(event.target) && !toggle.contains(event.target) && document.body.classList.contains('sidebar-active')) {
                    document.body.classList.remove('sidebar-active');
                }
            }
        });

        $(document).ready(function() {
            // Inicializa: esconde submenus que não estão abertos
            $('.nav-group:not(.open) > .nav-group-items').hide();
            
            // Lógica do Accordion
            $('.nav-group-header').on('click', function(e) {
                e.preventDefault();
                const $parentGroup = $(this).parent('.nav-group');
                const $items = $(this).next('.nav-group-items');
                
                // Se já está aberto, apenas fecha ele mesmo
                if ($parentGroup.hasClass('open')) {
                    $parentGroup.removeClass('open');
                    $items.slideUp(300);
                } else {
                    // Fecha todos os grupos no mesmo nível
                    const $siblings = $parentGroup.siblings('.nav-group');
                    $siblings.removeClass('open');
                    $siblings.children('.nav-group-items').slideUp(300);
                    
                    // Abre este grupo
                    $parentGroup.addClass('open');
                    $items.slideDown(300);
                }
            });
        });
    </script>
</body>
</html>

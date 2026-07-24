<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo Integração PowerBI Direct — Consulvolt Soluções</title>
    <meta name="description" content="Integração Direta do ERP Consulvolt com o Microsoft PowerBI. Conector nativo OData/REST API para relatórios executivos em tempo real em Angola.">

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-blue: #0058E6;
            --primary-blue-hover: #0047b3;
            --dark-navy: #0f172a;
            --dark-surface: #1e293b;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --bg-light: #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
            background-color: #ffffff;
            overflow-x: hidden;
        }

        .top-bar {
            background-color: #0047b3;
            color: #ffffff;
            font-size: 0.825rem;
            padding: 0.5rem 0;
            font-weight: 500;
        }

        .top-bar a { color: #ffffff; text-decoration: none; }
        .top-bar a:hover { text-decoration: underline; }

        .navbar-main {
            background: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand .brand-title {
            font-weight: 800;
            font-size: 1.4rem;
            color: var(--dark-navy);
            letter-spacing: -0.5px;
        }

        .nav-link {
            font-weight: 600;
            color: #334155 !important;
            font-size: 0.95rem;
            margin: 0 0.5rem;
            transition: all 0.2s;
        }

        .nav-link:hover, .nav-link.active { color: var(--primary-blue) !important; }

        .btn-blue {
            background-color: var(--primary-blue);
            color: #ffffff;
            font-weight: 700;
            border-radius: 8px;
            padding: 0.6rem 1.4rem;
            border: none;
            transition: all 0.2s;
        }

        .btn-blue:hover {
            background-color: var(--primary-blue-hover);
            color: #ffffff;
            transform: translateY(-2px);
        }

        .btn-outline-dark-custom {
            border: 2px solid var(--dark-navy);
            color: var(--dark-navy);
            font-weight: 700;
            border-radius: 8px;
            padding: 0.55rem 1.3rem;
            transition: all 0.2s;
        }

        .btn-outline-dark-custom:hover {
            background-color: var(--dark-navy);
            color: #ffffff;
        }

        .hero-banner-page {
            background: linear-gradient(135deg, #090d16 0%, #0f172a 100%);
            color: #ffffff;
            padding: 5rem 0 4rem;
        }

        .module-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            padding: 2rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .module-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 30px rgba(0, 88, 230, 0.08);
            border-color: var(--primary-blue);
        }

        .module-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--primary-blue);
        }

        .module-icon {
            width: 60px;
            height: 60px;
            border-radius: 14px;
            background: #eff6ff;
            color: var(--primary-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 1.5rem;
        }

        .footer-main {
            background: #090d16;
            color: #94a3b8;
            padding: 5rem 0 2rem;
            font-size: 0.9rem;
        }

        .footer-main h5 { color: #ffffff; font-weight: 700; margin-bottom: 1.5rem; }
        .footer-main a { color: #94a3b8; text-decoration: none; transition: all 0.2s; }
        .footer-main a:hover { color: #ffffff; padding-left: 4px; }

        .social-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.08);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>

    <!-- 1. TOP BAR -->
    <div class="top-bar">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-4">
                <span><i class="fas fa-id-card me-1"></i> NIF: <strong>5417213969</strong></span>
                <span><i class="fas fa-phone-alt me-2"></i> (244) 923 692 943 / (244) 923 012 143</span>
                <span class="d-none d-md-inline"><i class="fas fa-envelope me-2"></i> hdsgeral@gmail.com</span>
                <span class="d-none d-lg-inline"><i class="fas fa-map-marker-alt me-2"></i> Lar Patriota, Luanda</span>
            </div>
            <div>
                <a href="https://wa.me/244923692943" target="_blank" class="text-white fw-bold"><i class="fab fa-whatsapp me-1"></i> WhatsApp Comercial</a>
            </div>
        </div>
    </div>

    <!-- 2. NAVBAR MAIN -->
    <nav class="navbar navbar-expand-lg navbar-main">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #0058E6; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; font-weight: 800;">
                    C
                </div>
                <div>
                    <span class="brand-title">Consulvolt <span style="color: #0058E6;">Soluções</span></span>
                    <span class="d-block text-muted" style="font-size: 0.65rem; text-transform: uppercase; font-weight: 700;">10 Anos de Experiência</span>
                </div>
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMainContent">
                <i class="fas fa-bars text-dark fs-3"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarMainContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Início</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('website.about') }}">Sobre Nós</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('website.services') }}">Serviços</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('website.terms') }}">Termos & AGT</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('website.contact') }}">Contactos</a></li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('login') }}" class="btn btn-outline-dark-custom">
                        <i class="fas fa-sign-in-alt me-1"></i> Entrar no ERP
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-blue">
                        <i class="fas fa-user-plus me-1"></i> Criar Conta
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- 3. HERO BANNER -->
    <section class="hero-banner-page">
        <div class="container">
            <span class="badge bg-primary px-3 py-2 rounded-pill mb-3" style="background-color: #0058E6 !important; font-size: 0.85rem;"><i class="fas fa-chart-pie me-1"></i> Business Intelligence Executivo</span>
            <h1 class="fw-extrabold display-4 mb-3 text-white">Integração PowerBI Direct</h1>
            <p class="lead text-slate-300 fs-5 mb-0" style="max-width: 750px;">
                Conexão nativa do ERP Consulvolt com o Microsoft PowerBI para visualização gráfica em tempo real das métricas financeiras, vendas e salários da sua empresa.
            </p>
        </div>
    </section>

    <!-- 4. DETALHE DO MÓDULO -->
    <section class="py-5" style="background: #ffffff;">
        <div class="container py-4">
            <div class="row g-5 align-items-center">
                <div class="col-lg-7">
                    <span class="text-primary fw-bold text-uppercase fs-8" style="color: #0058E6 !important;">Tomada de Decisão Baseada em Dados</span>
                    <h2 class="fw-extrabold text-dark fs-2 mb-4 mt-2">Relatórios Executivos e Dashboards Dinâmicos</h2>
                    <p class="text-secondary fs-6 mb-4" style="line-height: 1.7;">
                        O módulo de <strong>Integração PowerBI Direct</strong> conecta os dados do ERP Consulvolt aos painéis do Microsoft PowerBI através de uma API OData/REST segura.
                    </p>

                    <div class="row g-4 mb-4">
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <h6 class="fw-bold text-dark mb-2"><i class="fas fa-plug text-primary me-2" style="color: #0058E6 !important;"></i> Feed em Tempo Real</h6>
                                <p class="text-muted fs-7 mb-0">Atualização instantânea sem necessidade de exportações manuais.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <h6 class="fw-bold text-dark mb-2"><i class="fas fa-chart-line text-primary me-2" style="color: #0058E6 !important;"></i> Indicadores (KPIs)</h6>
                                <p class="text-muted fs-7 mb-0">Volume de vendas, margem bruta, custos salariais e saldos bancários.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <h6 class="fw-bold text-dark mb-2"><i class="fas fa-layer-group text-primary me-2" style="color: #0058E6 !important;"></i> Consolidação Multi-Empresa</h6>
                                <p class="text-muted fs-7 mb-0">Visão unificada das subsidiárias e filiais num único relatório.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <h6 class="fw-bold text-dark mb-2"><i class="fas fa-mobile-alt text-primary me-2" style="color: #0058E6 !important;"></i> Acesso Mobile</h6>
                                <p class="text-muted fs-7 mb-0">Acompanhe a sua empresa no telemóvel através da app PowerBI.</p>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('register') }}" class="btn btn-blue btn-lg px-4 py-3">
                        <i class="fas fa-rocket me-2"></i> Conectar com o PowerBI
                    </a>
                </div>

                <div class="col-lg-5">
                    <div class="card border-0 shadow-lg overflow-hidden" style="border-radius: 20px;">
                        <div class="card-body p-4 text-white" style="background: #090d16;">
                            <h5 class="fw-bold mb-3">Dashboards Pré-Configurados</h5>
                            <ul class="list-unstyled fs-7 text-slate-300 mb-0">
                                <li class="mb-3"><i class="fas fa-check text-primary me-2" style="color: #60a5fa !important;"></i> Dashboard de Vendas e Faturação por Cliente</li>
                                <li class="mb-3"><i class="fas fa-check text-primary me-2" style="color: #60a5fa !important;"></i> Análise de Despesas de Pessoal e IRT/INSS</li>
                                <li class="mb-3"><i class="fas fa-check text-primary me-2" style="color: #60a5fa !important;"></i> Fluxo de Caixa Projetado e Idade de Saldos</li>
                                <li class="mb-3"><i class="fas fa-check text-primary me-2" style="color: #60a5fa !important;"></i> Rotação de Stock e Produtos Mais Vendidos</li>
                                <li class="mb-0"><i class="fas fa-check text-primary me-2" style="color: #60a5fa !important;"></i> Token de segurança encriptado para acesso à API</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. FOOTER MAIN -->
    <footer class="footer-main">
        <div class="container">
            <div class="row g-4 mb-5">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div style="width: 36px; height: 36px; border-radius: 8px; background: #0058E6; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; font-weight: 800;">
                            C
                        </div>
                        <span class="text-white fw-bold fs-5">Consulvolt <span style="color: #60a5fa;">Soluções</span></span>
                    </div>
                    <p class="text-slate-400 fs-7 mb-4">
                        Consulvolt Soluções: 10 anos de experiência em Materiais Elétricos Pesados, Equipamentos Informáticos, Consultoria Organizacional, Gestão de Projetos e ERP Certificado pela AGT em Angola.
                    </p>
                    <div>
                        <a href="https://wa.me/244923692943" target="_blank" class="social-icon"><i class="fab fa-whatsapp"></i></a>
                        <a href="mailto:hdsgeral@gmail.com" class="social-icon"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4">
                    <h5>Navegação</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('home') }}">Início</a></li>
                        <li class="mb-2"><a href="{{ route('website.about') }}">Sobre Nós</a></li>
                        <li class="mb-2"><a href="{{ route('website.services') }}">Serviços</a></li>
                        <li class="mb-2"><a href="{{ route('website.terms') }}">Termos & AGT</a></li>
                        <li class="mb-2"><a href="{{ route('website.contact') }}">Contactos</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-4">
                    <h5>Módulos ERP</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('website.modules.vendas-pos') }}">Vendas & POS Certificado</a></li>
                        <li class="mb-2"><a href="{{ route('website.modules.recursos-humanos') }}">Recursos Humanos & IRT</a></li>
                        <li class="mb-2"><a href="{{ route('website.modules.contabilidade-pgc') }}">Contabilidade PGC</a></li>
                        <li class="mb-2"><a href="{{ route('website.modules.tesouraria-bancos') }}">Tesouraria & Bancos</a></li>
                        <li class="mb-2"><a href="{{ route('website.modules.powerbi-direct') }}">Integração PowerBI Direct</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-4">
                    <h5>Contactos & Sede</h5>
                    <p class="text-slate-400 fs-7 mb-2"><i class="fas fa-id-card text-primary me-2" style="color: #60a5fa !important;"></i> NIF: 5417213969</p>
                    <p class="text-slate-400 fs-7 mb-2"><i class="fas fa-map-marker-alt text-primary me-2" style="color: #60a5fa !important;"></i> Lar Patriota, Rua Ginásio Wanaka, Luanda</p>
                    <p class="text-slate-400 fs-7 mb-2"><i class="fas fa-phone-alt text-primary me-2" style="color: #60a5fa !important;"></i> (244) 923 692 943 / (244) 923 012 143</p>
                    <p class="text-slate-400 fs-7 mb-0"><i class="fas fa-envelope text-primary me-2" style="color: #60a5fa !important;"></i> hdsgeral@gmail.com</p>
                </div>
            </div>

            <hr style="border-color: rgba(255, 255, 255, 0.1);">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center pt-3 fs-8 text-slate-400">
                <p class="mb-0">&copy; {{ date('Y') }} Consulvolt Soluções. NIF: 5417213969. Todos os direitos reservados. Software Certificado AGT n.º 142/AGT/2019.</p>
                <div class="d-flex gap-3 mt-2 mt-md-0">
                    <a href="{{ route('login') }}" class="text-slate-400">Área de Cliente</a>
                    <a href="{{ route('register') }}" class="text-slate-400">Registar Empresa</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

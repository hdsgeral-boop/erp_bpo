<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP Consulvolt — Sistema Integrado de Gestão Empresarial | Angola</title>
    <meta name="description" content="Plataforma ERP completa para empresas em Angola. Faturação certificada AGT, Processamento Salarial IRT/INSS, Contabilidade PGC, POS, Tesouraria e Integração PowerBI.">

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap 5.3 Framework -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-red: #dc2626;
            --primary-red-hover: #b91c1c;
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

        /* Top bar styling */
        .top-bar {
            background-color: #be123c;
            color: #ffffff;
            font-size: 0.825rem;
            padding: 0.5rem 0;
            font-weight: 500;
        }

        .top-bar a {
            color: #ffffff;
            text-decoration: none;
        }

        .top-bar a:hover {
            text-decoration: underline;
        }

        /* Navbar styling */
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

        .nav-link:hover, .nav-link.active {
            color: var(--primary-red) !important;
        }

        .btn-red {
            background-color: var(--primary-red);
            color: #ffffff;
            font-weight: 700;
            border-radius: 8px;
            padding: 0.6rem 1.4rem;
            border: none;
            transition: all 0.2s;
        }

        .btn-red:hover {
            background-color: var(--primary-red-hover);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(220, 38, 38, 0.25);
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

        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.9)), url('https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            color: #ffffff;
            padding: 6rem 0 7rem;
            position: relative;
        }

        .hero-badge {
            display: inline-block;
            background: rgba(220, 38, 38, 0.2);
            border: 1px solid rgba(220, 38, 38, 0.4);
            color: #fca5a5;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 0.4rem 1rem;
            border-radius: 50px;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            letter-spacing: -1px;
        }

        .hero-description {
            font-size: 1.15rem;
            color: #cbd5e1;
            max-width: 680px;
            margin-bottom: 2.5rem;
            line-height: 1.6;
        }

        /* Highlight Callout Banner */
        .callout-banner {
            background: linear-gradient(135deg, #e11d48, #be123c);
            color: #ffffff;
            padding: 2rem 0;
            box-shadow: 0 10px 25px rgba(225, 29, 72, 0.25);
        }

        /* Feature Split Section */
        .feature-split {
            padding: 6rem 0;
            background: #ffffff;
        }

        .quote-badge {
            background: var(--primary-red);
            color: #ffffff;
            padding: 1.5rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            box-shadow: 0 10px 25px rgba(220, 38, 38, 0.2);
        }

        /* Module Cards */
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
            box-shadow: 0 20px 30px rgba(0, 0, 0, 0.08);
            border-color: var(--primary-red);
        }

        .module-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--primary-red);
        }

        .module-icon {
            width: 60px;
            height: 60px;
            border-radius: 14px;
            background: #fef2f2;
            color: var(--primary-red);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 1.5rem;
        }

        /* High Impact Dark Banner */
        .dark-impact-banner {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            padding: 6rem 0;
            position: relative;
            overflow: hidden;
        }

        .dark-impact-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(220, 38, 38, 0.2) 0%, rgba(0,0,0,0) 70%);
            border-radius: 50%;
        }

        /* Pricing Section */
        .pricing-section {
            padding: 6rem 0;
            background: #f8fafc;
        }

        .pricing-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            padding: 2.5rem 2rem;
            transition: all 0.3s;
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .pricing-card.featured {
            border: 2px solid var(--primary-red);
            box-shadow: 0 15px 35px rgba(220, 38, 38, 0.15);
            transform: scale(1.03);
        }

        .pricing-badge {
            position: absolute;
            top: -15px;
            right: 25px;
            background: var(--primary-red);
            color: #ffffff;
            font-weight: 800;
            font-size: 0.75rem;
            padding: 0.3rem 0.9rem;
            border-radius: 20px;
            text-transform: uppercase;
        }

        .price-val {
            font-size: 2.4rem;
            font-weight: 800;
            color: var(--dark-navy);
        }

        /* Footer */
        .footer-main {
            background: #090d16;
            color: #94a3b8;
            padding: 5rem 0 2rem;
            font-size: 0.9rem;
        }

        .footer-main h5 {
            color: #ffffff;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .footer-main a {
            color: #94a3b8;
            text-decoration: none;
            transition: all 0.2s;
        }

        .footer-main a:hover {
            color: #ffffff;
            padding-left: 4px;
        }

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
            transition: all 0.2s;
        }

        .social-icon:hover {
            background: var(--primary-red);
            color: #ffffff;
        }

        @media (max-width: 991px) {
            .hero-title { font-size: 2.2rem; }
            .pricing-card.featured { transform: scale(1); }
        }
    </style>
</head>
<body>

    <!-- 1. TOP BAR -->
    <div class="top-bar">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-4">
                <span><i class="fas fa-phone-alt me-2"></i> +244 923 000 000 / 934 000 000</span>
                <span class="d-none d-md-inline"><i class="fas fa-envelope me-2"></i> comercial@consulvolt.com</span>
                <span class="d-none d-lg-inline"><i class="fas fa-map-marker-alt me-2"></i> Luanda, Angola</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="https://wa.me/244923000000" target="_blank" class="text-success fw-bold"><i class="fab fa-whatsapp me-1"></i> WhatsApp Suporte</a>
            </div>
        </div>
    </div>

    <!-- 2. NAVBAR MAIN -->
    <nav class="navbar navbar-expand-lg navbar-main">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #dc2626; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; font-weight: 800;">
                    E
                </div>
                <div>
                    <span class="brand-title">ERP_CONSULT</span>
                    <span class="d-block text-muted" style="font-size: 0.65rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Consulvolt Angola</span>
                </div>
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMainContent">
                <i class="fas fa-bars text-dark fs-3"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarMainContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="#inicio">Início</a></li>
                    <li class="nav-item"><a class="nav-link" href="#sobre">Sobre</a></li>
                    <li class="nav-item"><a class="nav-link" href="#modulos">Módulos ERP</a></li>
                    <li class="nav-item"><a class="nav-link" href="#agt">Faturação AGT</a></li>
                    <li class="nav-item"><a class="nav-link" href="#precos">Planos & Preços</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contacto">Contactos</a></li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('login') }}" class="btn btn-outline-dark-custom">
                        <i class="fas fa-sign-in-alt me-1"></i> Entrar no ERP
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-red">
                        <i class="fas fa-user-plus me-1"></i> Criar Conta
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- 3. HERO SECTION -->
    <section id="inicio" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="hero-badge"><i class="fas fa-shield-alt me-1"></i> Software Certificado AGT n.º 142/AGT/2019</span>
                    <h1 class="hero-title">
                        A solução mágica e revolucionária para a gestão da sua empresa a um preço inacreditável.
                    </h1>
                    <p class="hero-description">
                        Gerencie vendas, faturação fiscal AGT, recursos humanos com IRT/INSS, contabilidade PGC, inventário e tesouraria num único sistema inteligente integrado com PowerBI.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('register') }}" class="btn btn-red btn-lg px-4 py-3">
                            <i class="fas fa-rocket me-2"></i> Começar Agora Gratuitamente
                        </a>
                        <a href="#contacto" class="btn btn-outline-light btn-lg px-4 py-3 fw-bold" style="border-radius: 8px;">
                            <i class="fas fa-calendar-alt me-2"></i> Agendar Demonstração
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. HIGHLIGHT CALLOUT BANNER -->
    <section class="callout-banner">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div>
                <h4 class="fw-bold mb-1"><i class="fas fa-lightbulb me-2"></i> Precisa de uma solução à medida para a sua empresa ou holding?</h4>
                <p class="mb-0 text-white-50 fs-7">O ERP Consulvolt oferece suporte multi-empresa nativo para consolidar subsidiárias num único painel.</p>
            </div>
            <a href="#contacto" class="btn btn-light text-danger fw-bold px-4 py-2 text-nowrap" style="border-radius: 8px;">
                Falar com Especialista
            </a>
        </div>
    </section>

    <!-- 5. FEATURE SPLIT SECTION -->
    <section id="sobre" class="feature-split">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="text-danger fw-bold text-uppercase fs-8 tracking-wider">Tem Perguntas? Nós Temos as Respostas.</span>
                    <h2 class="fw-extrabold text-dark fs-1 mb-4 mt-2" style="letter-spacing: -0.5px;">
                        Gestão simplificada e 100% conforme com a legislação angolana.
                    </h2>
                    <p class="text-secondary fs-6 mb-4">
                        Desenvolvido especificamente para o mercado de Angola, o <strong>ERP Consulvolt</strong> elimina a complexidade dos processos fiscais, cálculo de impostos (IRT, INSS, Imposto de Selo, IVA) e gestão de stock em múltiplos armazéns.
                    </p>
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2 text-dark fw-bold">
                                <i class="fas fa-check-circle text-danger fs-5"></i> Faturação com Hash SAF-T
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2 text-dark fw-bold">
                                <i class="fas fa-check-circle text-danger fs-5"></i> Processamento Salarial IRT
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2 text-dark fw-bold">
                                <i class="fas fa-check-circle text-danger fs-5"></i> Conector Direto PowerBI
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2 text-dark fw-bold">
                                <i class="fas fa-check-circle text-danger fs-5"></i> Gestão Multi-Empresa
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 position-relative">
                    <div class="rounded-4 overflow-hidden shadow-lg border">
                        <img src="https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&w=800&q=80" alt="Gestão ERP Consulvolt" class="img-fluid w-100">
                    </div>
                    <div class="quote-badge position-absolute bottom-0 left-0 m-4" style="max-width: 320px;">
                        <i class="fas fa-quote-left fs-3 mb-2 d-block text-white-50"></i>
                        Líder em automação de processos financeiros e RH para empresas em expansão.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. MODULE CARDS SECTION -->
    <section id="modulos" class="py-6" style="padding: 5rem 0; background: #f8fafc;">
        <div class="container">
            <div class="text-center max-w-2xl mx-auto mb-5">
                <span class="text-danger fw-bold text-uppercase fs-8">Comprometidos com as Pessoas, Focados no Futuro</span>
                <h2 class="fw-extrabold text-dark fs-1 mt-2">Módulos Integrados do ERP Consulvolt</h2>
                <p class="text-muted">Tudo o que a sua empresa necessita num único ecossistema modular.</p>
            </div>

            <div class="row g-4">
                <!-- Card 1 -->
                <div class="col-lg-4 col-md-6">
                    <div class="module-card">
                        <div class="module-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                        <h4 class="fw-bold text-dark mb-3">Vendas, POS & Faturação AGT</h4>
                        <p class="text-secondary fs-7 mb-4">
                            Emissão de Faturas (FT, FR, OR, PP, NC, ND, GT) com numeração sequencial travada, Séries Documentais e geração automática do ficheiro SAF-T AO.
                        </p>
                        <a href="{{ route('login') }}" class="text-danger fw-bold text-decoration-none fs-7">Explorar Vendas &rarr;</a>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="col-lg-4 col-md-6">
                    <div class="module-card">
                        <div class="module-icon"><i class="fas fa-users-cog"></i></div>
                        <h4 class="fw-bold text-dark mb-3">Recursos Humanos & Salários</h4>
                        <p class="text-secondary fs-7 mb-4">
                            Cálculo exato de IRT (tabela progressiva 2026), INSS (3% empregado + 8% empresa), emissão de recibos PDF, mapa de transferências bancárias PS2 e ausências.
                        </p>
                        <a href="{{ route('login') }}" class="text-danger fw-bold text-decoration-none fs-7">Explorar RH & Salários &rarr;</a>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="col-lg-4 col-md-6">
                    <div class="module-card">
                        <div class="module-icon"><i class="fas fa-book"></i></div>
                        <h4 class="fw-bold text-dark mb-3">Contabilidade PGC & Tesouraria</h4>
                        <p class="text-secondary fs-7 mb-4">
                            Plano de Contas PGC completo, lançamentos nos diários, Balancete de Verificação, Reconciliação Bancária com extratos e mapa de Ativos Imobilizados.
                        </p>
                        <a href="{{ route('login') }}" class="text-danger fw-bold text-decoration-none fs-7">Explorar Contabilidade &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. HIGH IMPACT DARK BANNER -->
    <section class="dark-impact-banner">
        <div class="container text-center py-4">
            <span class="text-danger fw-bold text-uppercase fs-8 tracking-wider">Gestão Sem Limites</span>
            <h2 class="fw-extrabold fs-1 text-white mt-2 mb-3">
                O seu limite é apenas a sua imaginação.
            </h2>
            <p class="text-slate-300 max-w-2xl mx-auto fs-6 mb-4" style="max-width: 700px;">
                Controle múltiplas empresas e sucursais a partir de uma única conta com permissões de acesso granulares (RBAC) e audit trail completo.
            </p>
            <a href="{{ route('register') }}" class="btn btn-red btn-lg px-5 py-3">
                <i class="fas fa-building me-2"></i> Registar a Sua Empresa Agora
            </a>
        </div>
    </section>

    <!-- 8. DARK FEATURE GRID (BENEFÍCIOS) -->
    <section id="agt" class="py-6" style="padding: 5rem 0; background: #0b1329; color: #fff;">
        <div class="container">
            <div class="text-center mb-5">
                <span class="text-danger fw-bold text-uppercase fs-8">Grandes Conquistas Não Vêm da Zona de Conforto</span>
                <h2 class="fw-extrabold fs-1 text-white mt-2">Segurança e Desempenho Garantidos</h2>
            </div>

            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="p-4 rounded-4 border border-slate-800" style="background: #111c3a;">
                        <i class="fas fa-certificate text-danger fs-2 mb-3"></i>
                        <h5 class="fw-bold text-white mb-2">Faturação Certificada</h5>
                        <p class="text-slate-400 fs-7 mb-0">Assinatura digital RSA, Hash de validação e SAF-T exportável para a AGT.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="p-4 rounded-4 border border-slate-800" style="background: #111c3a;">
                        <i class="fas fa-plug text-primary fs-2 mb-3"></i>
                        <h5 class="fw-bold text-white mb-2">PowerBI Direct Feed</h5>
                        <p class="text-slate-400 fs-7 mb-0">Conexão nativa OData/JSON para dashboards financeiros atualizados em tempo real.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="p-4 rounded-4 border border-slate-800" style="background: #111c3a;">
                        <i class="fas fa-brain text-warning fs-2 mb-3"></i>
                        <h5 class="fw-bold text-white mb-2">Agente de IA Integrado</h5>
                        <p class="text-slate-400 fs-7 mb-0">Assistente inteligente para consultas rápidas de stock, salários e balanços.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="p-4 rounded-4 border border-slate-800" style="background: #111c3a;">
                        <i class="fas fa-user-shield text-success fs-2 mb-3"></i>
                        <h5 class="fw-bold text-white mb-2">Conformidade LGPD</h5>
                        <p class="text-slate-400 fs-7 mb-0">Criptografia de dados sensíveis (NIF, IBAN) e proteção estrita de privacidade.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 9. PRICING SECTION -->
    <section id="precos" class="pricing-section">
        <div class="container">
            <div class="text-center max-w-2xl mx-auto mb-5">
                <span class="text-danger fw-bold text-uppercase fs-8">Escolha o Plano Ideal Para o Seu Negócio</span>
                <h2 class="fw-extrabold text-dark fs-1 mt-2">O sucesso não acontece por acaso.</h2>
                <p class="text-muted">Preços transparentes em Kwanzas (AOA) sem custos ocultos.</p>
            </div>

            <div class="row g-4 align-items-center">
                <!-- Plan 1 -->
                <div class="col-lg-4">
                    <div class="pricing-card">
                        <h4 class="fw-bold text-dark mb-2">Plano Start</h4>
                        <p class="text-muted fs-7 mb-4">Ideal para Micro e Pequenas Empresas (PME).</p>
                        <div class="mb-4">
                            <span class="price-val">5.000</span> <span class="fs-6 text-muted fw-bold">Kz / mês</span>
                        </div>
                        <ul class="list-unstyled fs-7 text-secondary mb-4 grow">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Até 3 Utilizadores</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Módulo Vendas & POS</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Emissão SAF-T AGT</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Gestão de Stock Básico</li>
                            <li class="text-muted opacity-50"><i class="fas fa-times me-2"></i> Módulo de Salários</li>
                        </ul>
                        <a href="{{ route('register') }}" class="btn btn-outline-dark-custom w-100 py-3">Adquirir Plano Start</a>
                    </div>
                </div>

                <!-- Plan 2 (Featured) -->
                <div class="col-lg-4">
                    <div class="pricing-card featured">
                        <span class="pricing-badge">Mais Popular</span>
                        <h4 class="fw-bold text-dark mb-2">Plano Pro / Growth</h4>
                        <p class="text-muted fs-7 mb-4">Para empresas em rápida expansão.</p>
                        <div class="mb-4">
                            <span class="price-val text-danger">8.500</span> <span class="fs-6 text-muted fw-bold">Kz / mês</span>
                        </div>
                        <ul class="list-unstyled fs-7 text-secondary mb-4 grow">
                            <li class="mb-2"><i class="fas fa-check text-danger me-2"></i> Até 10 Utilizadores</li>
                            <li class="mb-2"><i class="fas fa-check text-danger me-2"></i> Vendas, POS & Faturação AGT</li>
                            <li class="mb-2"><i class="fas fa-check text-danger me-2"></i> RH & Processamento Salarial</li>
                            <li class="mb-2"><i class="fas fa-check text-danger me-2"></i> Contabilidade & Tesouraria</li>
                            <li class="mb-2"><i class="fas fa-check text-danger me-2"></i> Suporte Prioritário</li>
                        </ul>
                        <a href="{{ route('register') }}" class="btn btn-red w-100 py-3">Adquirir Plano Pro</a>
                    </div>
                </div>

                <!-- Plan 3 -->
                <div class="col-lg-4">
                    <div class="pricing-card">
                        <h4 class="fw-bold text-dark mb-2">Plano Enterprise</h4>
                        <p class="text-muted fs-7 mb-4">Para Grupos Empresariais e Holdings.</p>
                        <div class="mb-4">
                            <span class="price-val">12.799</span> <span class="fs-6 text-muted fw-bold">Kz / mês</span>
                        </div>
                        <ul class="list-unstyled fs-7 text-secondary mb-4 grow">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Utilizadores Ilimitados</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Gestão Multi-Empresa Nativa</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Conector PowerBI Direct</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Agente de IA Dedicado</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Suporte 24/7 Dedicado</li>
                        </ul>
                        <a href="{{ route('register') }}" class="btn btn-outline-dark-custom w-100 py-3">Adquirir Plano Enterprise</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 10. CONTACT FORM SECTION -->
    <section id="contacto" class="py-6" style="padding: 5rem 0; background: #ffffff;">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <span class="text-danger fw-bold text-uppercase fs-8">Fale Com a Nossa Equipa</span>
                    <h2 class="fw-extrabold text-dark fs-1 mb-3 mt-2">Pronto para transformar a gestão da sua empresa?</h2>
                    <p class="text-secondary mb-4">
                        Preencha o formulário para agendar uma demonstração guiada por um dos nossos consultores seniores em Luanda.
                    </p>

                    @if(session('success'))
                        <div class="alert alert-success border-0 shadow-sm rounded-3">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        </div>
                    @endif

                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="p-3 bg-light text-danger rounded-3 fs-4"><i class="fas fa-phone-alt"></i></div>
                        <div>
                            <div class="fw-bold text-dark">Telefone / Apoio Comercial</div>
                            <div class="text-muted">+244 923 000 000 / +244 934 000 000</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 bg-light text-danger rounded-3 fs-4"><i class="fas fa-envelope"></i></div>
                        <div>
                            <div class="fw-bold text-dark">Email Institucional</div>
                            <div class="text-muted">comercial@consulvolt.com</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card border-0 shadow-lg p-4" style="border-radius: 20px; background: #ffffff;">
                        <h4 class="fw-bold text-dark mb-4">Pedir Demonstração Gratuita</h4>
                        <form action="{{ route('contact.submit') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark fs-8">Nome Completo <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control py-2" placeholder="Seu nome" required>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark fs-8">Email Profissional <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control py-2" placeholder="nome@empresa.co.ao" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark fs-8">Telefone / WhatsApp</label>
                                    <input type="text" name="phone" class="form-control py-2" placeholder="+244 9..." required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark fs-8">Nome da Empresa</label>
                                <input type="text" name="company_name" class="form-control py-2" placeholder="Empresa Lda">
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark fs-8">Mensagem ou Necessidades Específicas</label>
                                <textarea name="message" rows="3" class="form-control" placeholder="Descreva brevemente como podemos ajudar..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-red w-100 py-3 fs-7">
                                <i class="fas fa-paper-plane me-2"></i> Enviar Pedido de Demonstração
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 11. FOOTER MAIN -->
    <footer class="footer-main">
        <div class="container">
            <div class="row g-4 mb-5">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div style="width: 36px; height: 36px; border-radius: 8px; background: #dc2626; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; font-weight: 800;">
                            E
                        </div>
                        <span class="text-white fw-bold fs-5">ERP_CONSULT</span>
                    </div>
                    <p class="text-slate-400 fs-7 mb-4">
                        Sistema integrado de gestão empresarial projetado para automatizar vendas, impostos AGT, folha de pagamento e contabilidade PGC em Angola.
                    </p>
                    <div>
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4">
                    <h5>Navegação</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#inicio">Início</a></li>
                        <li class="mb-2"><a href="#sobre">Sobre Nós</a></li>
                        <li class="mb-2"><a href="#modulos">Módulos ERP</a></li>
                        <li class="mb-2"><a href="#agt">Faturação AGT</a></li>
                        <li class="mb-2"><a href="#precos">Preços</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-4">
                    <h5>Módulos ERP</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#">Vendas & POS Certificado</a></li>
                        <li class="mb-2"><a href="#">Recursos Humanos & IRT</a></li>
                        <li class="mb-2"><a href="#">Contabilidade PGC</a></li>
                        <li class="mb-2"><a href="#">Tesouraria & Bancos</a></li>
                        <li class="mb-2"><a href="#">Integração PowerBI Direct</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-4">
                    <h5>Contactos & Sede</h5>
                    <p class="text-slate-400 fs-7 mb-2"><i class="fas fa-map-marker-alt text-danger me-2"></i> Talatona & Maianga, Luanda — Angola</p>
                    <p class="text-slate-400 fs-7 mb-2"><i class="fas fa-phone-alt text-danger me-2"></i> +244 923 000 000</p>
                    <p class="text-slate-400 fs-7 mb-0"><i class="fas fa-envelope text-danger me-2"></i> comercial@consulvolt.com</p>
                </div>
            </div>

            <hr style="border-color: rgba(255, 255, 255, 0.1);">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center pt-3 fs-8 text-slate-400">
                <p class="mb-0">&copy; {{ date('Y') }} ERP Consulvolt. Todos os direitos reservados. Software Certificado AGT n.º 142/AGT/2019.</p>
                <div class="d-flex gap-3 mt-2 mt-md-0">
                    <a href="{{ route('login') }}" class="text-slate-400">Área de Cliente</a>
                    <a href="{{ route('register') }}" class="text-slate-400">Registar Empresa</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

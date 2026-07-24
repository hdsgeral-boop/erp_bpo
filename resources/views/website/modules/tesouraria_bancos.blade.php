<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo Tesouraria & Bancos — Consulvolt Soluções</title>
    <meta name="description" content="Módulo de Tesouraria e Contas Bancárias do ERP Consulvolt em Angola. Controlo de fluxo de caixa, reconciliação bancária, gestão de contas de clientes e fornecedores.">

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



    <!-- 2. NAVBAR MAIN -->
    <nav class="navbar navbar-expand-lg navbar-main">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <img src="{{ asset('img/logo_erp.png') }}" alt="Consulvolt Soluções" style="height: 48px; width: auto; object-fit: contain;">
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
            <span class="badge bg-primary px-3 py-2 rounded-pill mb-3" style="background-color: #0058E6 !important; font-size: 0.85rem;"><i class="fas fa-wallet me-1"></i> Gestão de Caixas & Liquidez</span>
            <h1 class="fw-extrabold display-4 mb-3 text-white">Tesouraria & Bancos</h1>
            <p class="lead text-slate-300 fs-5 mb-0" style="max-width: 750px;">
                Controlo rigoroso do fluxo de caixa, gestão de contas correntes bancárias, reconciliação de extratos e apuramento de saldos de terceiros.
            </p>
        </div>
    </section>

    <!-- 4. DETALHE DO MÓDULO -->
    <section class="py-5" style="background: #ffffff;">
        <div class="container py-4">
            <div class="row g-5 align-items-center">
                <div class="col-lg-7">
                    <span class="text-primary fw-bold text-uppercase fs-8" style="color: #0058E6 !important;">Liquidez & Controlo de Caixa</span>
                    <h2 class="fw-extrabold text-dark fs-2 mb-4 mt-2">Gestão Financeira e Bancária em Tempo Real</h2>
                    <p class="text-secondary fs-6 mb-4" style="line-height: 1.7;">
                        O módulo de <strong>Tesouraria & Bancos</strong> do ERP Consulvolt permite acompanhar cada movimento de entrada e saída de fundos, assegurando visibilidade sobre a disponibilidade financeira da empresa.
                    </p>

                    <div class="row g-4 mb-4">
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <h6 class="fw-bold text-dark mb-2"><i class="fas fa-university text-primary me-2" style="color: #0058E6 !important;"></i> Contas Bancárias Ilimitadas</h6>
                                <p class="text-muted fs-7 mb-0">Registo de contas em Kwanzas (AOA) e moedas estrangeiras.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <h6 class="fw-bold text-dark mb-2"><i class="fas fa-exchange-alt text-primary me-2" style="color: #0058E6 !important;"></i> Reconciliação Bancária</h6>
                                <p class="text-muted fs-7 mb-0">Cruzamento de extratos com recibos de clientes e pagamentos.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <h6 class="fw-bold text-dark mb-2"><i class="fas fa-money-bill-wave text-primary me-2" style="color: #0058E6 !important;"></i> Caixas Físicos</h6>
                                <p class="text-muted fs-7 mb-0">Abertura, fecho, sangrias e suprimentos de caixas com recibo.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <h6 class="fw-bold text-dark mb-2"><i class="fas fa-history text-primary me-2" style="color: #0058E6 !important;"></i> Idade de Saldos (Aging)</h6>
                                <p class="text-muted fs-7 mb-0">Relatórios de faturas a receber e a pagar por antiguidade.</p>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('register') }}" class="btn btn-blue btn-lg px-4 py-3">
                        <i class="fas fa-rocket me-2"></i> Experimentar Módulo de Tesouraria
                    </a>
                </div>

                <div class="col-lg-5">
                    <div class="card border-0 shadow-lg overflow-hidden" style="border-radius: 20px;">
                        <div class="card-body p-4 text-white" style="background: #090d16;">
                            <h5 class="fw-bold mb-3">Recursos de Controlo de Tesouraria</h5>
                            <ul class="list-unstyled fs-7 text-slate-300 mb-0">
                                <li class="mb-3"><i class="fas fa-check text-primary me-2" style="color: #60a5fa !important;"></i> Emissão de recibos de liquidação e avisos de lançamento</li>
                                <li class="mb-3"><i class="fas fa-check text-primary me-2" style="color: #60a5fa !important;"></i> Previsão diária e mensal de Cash Flow</li>
                                <li class="mb-3"><i class="fas fa-check text-primary me-2" style="color: #60a5fa !important;"></i> Gestão de adiantamentos a fornecedores e de clientes</li>
                                <li class="mb-3"><i class="fas fa-check text-primary me-2" style="color: #60a5fa !important;"></i> Transferências entre caixas e contas bancárias</li>
                                <li class="mb-0"><i class="fas fa-check text-primary me-2" style="color: #60a5fa !important;"></i> Registo com auditoria de quem movimentou cada fundo</li>
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
                        <img src="{{ asset('img/logo_erp.png') }}" alt="Consulvolt Soluções" style="height: 48px; width: auto; object-fit: contain; background: transparent;">
                    </div>
                    <p class="text-slate-400 fs-7 mb-4">
                        Consulvolt Soluções — 10 Anos de Excelência e Inovação Tecnológica em Angola. ERP Certificado pela AGT.
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

    <!-- Floating WhatsApp Button -->
    <a href="https://wa.me/244923692943" target="_blank" class="whatsapp-float-btn" title="Falar no WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <style>
        .whatsapp-float-btn {
            position: fixed;
            bottom: 25px;
            right: 25px;
            width: 60px;
            height: 60px;
            background-color: #25d366;
            color: #ffffff;
            border-radius: 50px;
            text-align: center;
            font-size: 32px;
            box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .whatsapp-float-btn:hover {
            background-color: #128c7e;
            color: #ffffff;
            transform: scale(1.1);
            box-shadow: 0 6px 25px rgba(37, 211, 102, 0.6);
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

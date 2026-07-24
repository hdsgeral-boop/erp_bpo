<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nós — Consulvolt Soluções (10 Anos de Experiência)</title>
    <meta name="description" content="Consulvolt Soluções: 10 anos de experiência em Venda de Materiais Elétricos Pesados, Equipamentos Informáticos, Consultoria Organizacional, Desenvolvimento de Aplicações Tecnológicas e Gestão de Projetos em Angola.">

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
                    <li class="nav-item"><a class="nav-link active" href="{{ route('website.about') }}">Sobre Nós</a></li>
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

    <!-- 3. PAGE HERO BANNER -->
    <section class="hero-banner-page">
        <div class="container">
            <span class="badge bg-primary px-3 py-2 rounded-pill mb-3" style="background-color: #0058E6 !important; font-size: 0.85rem;"><i class="fas fa-award me-1"></i> 10 Anos no Mercado Angolano</span>
            <h1 class="fw-extrabold display-4 mb-3 text-white">Sobre a Consulvolt Soluções</h1>
            <p class="lead text-slate-300 fs-5 mb-0" style="max-width: 750px;">
                Eficiência, inovação e desenvolvimento de tecnologia para o crescimento das empresas em Angola.
            </p>
        </div>
    </section>

    <!-- 4. CARTA DE APRESENTAÇÃO -->
    <section class="py-5" style="background: #ffffff;">
        <div class="container py-4">
            <div class="row g-5 align-items-center">
                <div class="col-lg-7">
                    <span class="text-primary fw-bold text-uppercase fs-8" style="color: #0058E6 !important;">Carta de Apresentação</span>
                    <h2 class="fw-extrabold text-dark fs-2 mb-4 mt-2">10 Anos de Inovação e Qualidade em Angola</h2>
                    
                    <div class="p-4 rounded-4 mb-4" style="background: #f8fafc; border-left: 4px solid #0058E6;">
                        <p class="text-dark fs-6 fw-semibold mb-0" style="line-height: 1.7;">
                            "É com grande satisfação que apresentamos a <strong>Consulvolt Soluções</strong>, uma empresa com 10 anos de experiência no mercado angolano. Nossa atuação se concentra na venda de materiais elétricos pesados, equipamentos informáticos e consumíveis, consultoria organizacional, desenvolvimento de aplicações tecnológicas para melhoria de processos e controlo interno, além de gestão de projetos."
                        </p>
                    </div>

                    <p class="text-secondary fs-6 mb-4" style="line-height: 1.7;">
                        Temos nos dedicado a oferecer soluções que não apenas atendem às necessidades de nossos clientes, mas que também promovem a eficiência e a inovação em suas operações. Nossa equipe é composta por profissionais altamente qualificados, prontos para oferecer soluções personalizadas e desenvolver estratégias adaptadas a cada desafio.
                    </p>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <strong class="text-dark d-block">Contribuinte / NIF</strong>
                                <span class="text-primary fw-bold" style="color: #0058E6 !important;">5417213969</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <strong class="text-dark d-block">Sede Institucional</strong>
                                <span class="text-muted">Lar Patriota, Rua Ginásio Wanaka, Luanda</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card border-0 shadow-lg overflow-hidden" style="border-radius: 20px;">
                        <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=800&q=80" alt="Consulvolt Soluções" class="img-fluid">
                        <div class="card-body p-4 text-white" style="background: #090d16;">
                            <h5 class="fw-bold mb-3">Nossos 4 Pilares de Serviço</h5>
                            <ul class="list-unstyled fs-7 text-slate-300 mb-0">
                                <li class="mb-2"><i class="fas fa-check-circle me-2 text-primary" style="color: #60a5fa !important;"></i> Materiais Elétricos Pesados e Informática</li>
                                <li class="mb-2"><i class="fas fa-check-circle me-2 text-primary" style="color: #60a5fa !important;"></i> Consultoria Organizacional & Processos</li>
                                <li class="mb-2"><i class="fas fa-check-circle me-2 text-primary" style="color: #60a5fa !important;"></i> Desenvolvimento de Aplicações Tecnológicas & ERP</li>
                                <li class="mb-0"><i class="fas fa-check-circle me-2 text-primary" style="color: #60a5fa !important;"></i> Gestão de Projetos Empresariais</li>
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
                        <li class="mb-2"><a href="{{ route('login') }}">Vendas & POS Certificado</a></li>
                        <li class="mb-2"><a href="{{ route('login') }}">Recursos Humanos & IRT</a></li>
                        <li class="mb-2"><a href="{{ route('login') }}">Contabilidade PGC</a></li>
                        <li class="mb-2"><a href="{{ route('login') }}">Tesouraria & Bancos</a></li>
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

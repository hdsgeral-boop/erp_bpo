<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termos & AGT - Consulvolt Soluções</title>
    <meta name="description" content="Certificação AGT n.º 142/AGT/2019, termos de serviço e privacidade da Consulvolt Soluções em Angola.">

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
                    <li class="nav-item"><a class="nav-link active" href="{{ route('website.terms') }}">Termos & AGT</a></li>
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
            <span class="badge bg-primary px-3 py-2 rounded-pill mb-3" style="background-color: #0058E6 !important; font-size: 0.85rem;"><i class="fas fa-shield-alt me-1"></i> Conformidade Fiscal</span>
            <h1 class="fw-extrabold display-4 mb-3 text-white">Termos & Certificação AGT</h1>
            <p class="lead text-slate-300 fs-5 mb-0" style="max-width: 750px;">
                Software de faturação certificado sob o n.º 142/AGT/2019 com rigor de privacidade e segurança de dados.
            </p>
        </div>
    </section>

    <!-- 4. TERMOS & PRIVACIDADE -->
    <section class="py-5" style="background: #ffffff;">
        <div class="container py-4">
            <div class="row g-5">
                <div class="col-lg-8">
                    <h3 class="fw-bold text-dark mb-3">1. Certificação de Faturação pela AGT</h3>
                    <p class="text-secondary fs-6 mb-4" style="line-height: 1.7;">
                        O ERP da Consulvolt Soluções é homologado pela <strong>Administração Geral Tributária (AGT)</strong> em Angola sob o número de certificado <strong>142/AGT/2019</strong>. Todos os documentos fiscais emitidos cumprem as especificações de assinatura digital RSA 1024-bit e exportação SAF-T AO.
                    </p>

                    <h3 class="fw-bold text-dark mb-3">2. Privacidade e Proteção de Dados Empresariais</h3>
                    <p class="text-secondary fs-6 mb-4" style="line-height: 1.7;">
                        Garantimos a total privacidade dos dados das empresas clientes. Todas as informações financeiras, tributárias, de inventário e de pessoal são mantidas sob ambiente seguro e encriptado.
                    </p>

                    <h3 class="fw-bold text-dark mb-3">3. Dados da Empresa</h3>
                    <p class="text-secondary fs-6 mb-0" style="line-height: 1.7;">
                        <strong>Razão Social:</strong> Consulvolt Soluções<br>
                        <strong>NIF:</strong> 5417213969<br>
                        <strong>Sede:</strong> Angola - Luanda, Lar Patriota, Rua Ginásio Wanaka<br>
                        <strong>Email:</strong> hdsgeral@gmail.com<br>
                        <strong>Telefones:</strong> (244) 923 692 943 / (244) 923 012 143
                    </p>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm p-4 bg-light" style="border-radius: 20px; border-top: 4px solid #0058E6 !important;">
                        <h5 class="fw-bold text-dark mb-3">Resumo da Empresa</h5>
                        <ul class="list-unstyled text-slate-700 fs-7 mb-0">
                            <li class="mb-2"><strong>Empresa:</strong> Consulvolt Soluções</li>
                            <li class="mb-2"><strong>NIF:</strong> 5417213969</li>
                            <li class="mb-2"><strong>Experiência:</strong> 10 Anos em Angola</li>
                            <li class="mb-0"><strong>Certificação:</strong> 142/AGT/2019</li>
                        </ul>
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
                        Consulvolt Soluções - 10 Anos de Excelência e Inovação Tecnológica em Angola. ERP Certificado pela AGT.
                    </p>
                    <div>
                        <a href="https://wa.me/244923012143" target="_blank" class="social-icon"><i class="fab fa-whatsapp"></i></a>
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
    <a href="https://wa.me/244923012143" target="_blank" class="whatsapp-float-btn" title="Falar no WhatsApp">
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

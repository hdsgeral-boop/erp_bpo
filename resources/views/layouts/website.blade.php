<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Consulvolt Soluções — Gestão Empresarial & Tecnologia | Angola')</title>
    <meta name="description" content="@yield('meta_description', 'Consulvolt Soluções - 10 Anos de Experiência em Venda de Materiais Elétricos e Informáticos, Consultoria Organizacional, Desenvolvimento de Aplicações e Gestão de Projetos em Angola.')">

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap 5.3 Framework -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-blue: #0058E6;
            --primary-blue-hover: #0047b3;
            --primary-blue-light: #eff6ff;
            --dark-navy: #090d16;
            --dark-surface: #0f172a;
            --card-navy: #1e293b;
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

        /* Navbar Main */
        .navbar-main {
            background: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            padding: 0.9rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand .brand-logo-img {
            height: 48px;
            width: auto;
            object-fit: contain;
            background: transparent;
        }

        .nav-link {
            font-weight: 600;
            color: #334155 !important;
            font-size: 0.95rem;
            margin: 0 0.4rem;
            transition: all 0.2s;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--primary-blue) !important;
        }

        .btn-blue-primary {
            background: linear-gradient(135deg, #0058E6, #0047b3);
            color: #ffffff;
            font-weight: 700;
            border-radius: 10px;
            padding: 0.65rem 1.4rem;
            border: none;
            box-shadow: 0 8px 20px rgba(0, 88, 230, 0.3);
            transition: all 0.2s;
        }

        .btn-blue-primary:hover {
            background: linear-gradient(135deg, #0047b3, #00388f);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(0, 88, 230, 0.4);
        }

        .btn-outline-blue {
            border: 2px solid var(--primary-blue);
            color: var(--primary-blue);
            font-weight: 700;
            border-radius: 10px;
            padding: 0.55rem 1.3rem;
            transition: all 0.2s;
        }

        .btn-outline-blue:hover {
            background-color: var(--primary-blue);
            color: #ffffff;
        }

        /* Footer Main */
        .footer-main {
            background: var(--dark-navy);
            color: #94a3b8;
            padding: 4rem 0 2rem;
            font-size: 0.9rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
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
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.06);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            margin-right: 0.5rem;
            transition: all 0.2s;
        }

        .social-icon:hover {
            background: var(--primary-blue);
            color: #ffffff;
            transform: translateY(-2px);
        }

        .badge-agt-cert {
            background: rgba(0, 88, 230, 0.15);
            color: #60a5fa;
            border: 1px solid rgba(0, 88, 230, 0.3);
            border-radius: 30px;
            padding: 0.4rem 1rem;
            font-size: 0.8rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        @yield('styles')
    </style>
</head>
<body>

    <!-- NAVBAR MAIN (Top Bar Eliminada conforme solicitado) -->
    <nav class="navbar navbar-expand-lg navbar-main">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <img src="{{ asset('img/logo_erp.png') }}" alt="Consulvolt Soluções" class="brand-logo-img">
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMainContent">
                <i class="fas fa-bars text-dark fs-3"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarMainContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Início</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('website.about') ? 'active' : '' }}" href="{{ route('website.about') }}">Sobre Nós</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('website.services') ? 'active' : '' }}" href="{{ route('website.services') }}">Serviços & ERP</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('website.contact') ? 'active' : '' }}" href="{{ route('website.contact') }}">Contactos</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('website.terms') ? 'active' : '' }}" href="{{ route('website.terms') }}">Termos & AGT</a></li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('login') }}" class="btn btn-outline-blue">
                        <i class="fas fa-sign-in-alt me-1"></i> Entrar no ERP
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-blue-primary">
                        <i class="fas fa-user-plus me-1"></i> Criar Conta
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- CONTENT -->
    <main>
        @yield('content')
    </main>

    <!-- FOOTER MAIN -->
    <footer class="footer-main">
        <div class="container">
            <div class="row g-4 mb-5">
                <div class="col-lg-4">
                    <div class="mb-3">
                        <img src="{{ asset('img/logo_erp.png') }}" alt="Consulvolt Soluções" style="height: 48px; width: auto; object-fit: contain; background: transparent;">
                    </div>
                    <!-- Frase curta e profissional no footer -->
                    <p class="text-slate-400 fs-7 mb-3">
                        Consulvolt Soluções — 10 Anos de Excelência e Inovação Tecnológica em Angola. ERP Certificado pela AGT.
                    </p>
                    <div class="badge-agt-cert mb-3">
                        <i class="fas fa-shield-alt"></i> Software Certificado AGT n.º 142/AGT/2019
                    </div>
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
                        <li class="mb-2"><a href="{{ route('website.services') }}">Serviços & Módulos ERP</a></li>
                        <li class="mb-2"><a href="{{ route('website.contact') }}">Contactos</a></li>
                        <li class="mb-2"><a href="{{ route('website.terms') }}">Termos & AGT</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-4">
                    <h5>Serviços & Soluções</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('website.services') }}">Materiais Elétricos Pesados</a></li>
                        <li class="mb-2"><a href="{{ route('website.services') }}">Equipamentos Informáticos</a></li>
                        <li class="mb-2"><a href="{{ route('website.services') }}">Consultoria Organizacional</a></li>
                        <li class="mb-2"><a href="{{ route('website.services') }}">Desenvolvimento de Aplicações</a></li>
                        <li class="mb-2"><a href="{{ route('website.services') }}">Gestão de Projetos</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-4">
                    <h5>Contactos & Sede</h5>
                    <p class="text-slate-400 fs-7 mb-2"><i class="fas fa-id-card text-primary me-2"></i> NIF: 5417213969</p>
                    <p class="text-slate-400 fs-7 mb-2"><i class="fas fa-map-marker-alt text-primary me-2"></i> Angola - Luanda, Lar Patriota, Rua Ginásio Wanaka</p>
                    <p class="text-slate-400 fs-7 mb-2"><i class="fas fa-phone-alt text-primary me-2"></i> (244) 923 692 943 / 923 012 143</p>
                    <p class="text-slate-400 fs-7 mb-0"><i class="fas fa-envelope text-primary me-2"></i> hdsgeral@gmail.com</p>
                </div>
            </div>

            <hr style="border-color: rgba(255, 255, 255, 0.1);">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center pt-3 fs-8 text-slate-400">
                <p class="mb-0">&copy; {{ date('Y') }} Consulvolt Soluções. Todos os direitos reservados. NIF: 5417213969.</p>
                <div class="d-flex gap-3 mt-2 mt-md-0">
                    <a href="{{ route('login') }}" class="text-slate-400">Área de Cliente ERP</a>
                    <a href="{{ route('register') }}" class="text-slate-400">Registar Empresa</a>
                    <a href="{{ route('website.terms') }}" class="text-slate-400">Termos & Privacidade</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

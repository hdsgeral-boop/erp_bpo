<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactos — Consulvolt Soluções | NIF: 5417213969</title>
    <meta name="description" content="Contactos da Consulvolt Soluções: Lar Patriota, Rua Ginásio Wanaka, Luanda. Telefones: (244) 923 692 943 / (244) 923 012 143. Email: hdsgeral@gmail.com. NIF: 5417213969.">

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
                    <li class="nav-item"><a class="nav-link active" href="{{ route('website.contact') }}">Contactos</a></li>
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
            <span class="badge bg-primary px-3 py-2 rounded-pill mb-3" style="background-color: #0058E6 !important; font-size: 0.85rem;"><i class="fas fa-headset me-1"></i> Atendimento Comercial</span>
            <h1 class="fw-extrabold display-4 mb-3 text-white">Contactos Institucionais</h1>
            <p class="lead text-slate-300 fs-5 mb-0" style="max-width: 750px;">
                Fale com a Consulvolt Soluções. Estamos à sua disposição para propostas, suporte e consultas técnicas.
            </p>
        </div>
    </section>

    <!-- 4. CONTACT DETAILS & FORM -->
    <section class="py-5" style="background: #ffffff;">
        <div class="container py-4">
            <div class="row g-5">
                <div class="col-lg-5">
                    <span class="text-primary fw-bold text-uppercase fs-8" style="color: #0058E6 !important;">Informações da Empresa</span>
                    <h2 class="fw-extrabold text-dark fs-2 mb-4 mt-2">Nossos Contactos</h2>

                    <div class="p-4 rounded-4 bg-light mb-4 border" style="border-left: 4px solid #0058E6 !important;">
                        <h6 class="fw-bold text-dark mb-1"><i class="fas fa-id-card me-2 text-primary" style="color: #0058E6 !important;"></i> Contribuinte / NIF</h6>
                        <p class="text-primary fw-bold fs-5 mb-0" style="color: #0058E6 !important;">5417213969</p>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-1"><i class="fas fa-map-marker-alt me-2 text-primary" style="color: #0058E6 !important;"></i> Morada</h6>
                        <p class="text-secondary fs-6 mb-0">Angola - Luanda, Lar Patriota, Rua Ginásio Wanaka</p>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-1"><i class="fas fa-envelope me-2 text-primary" style="color: #0058E6 !important;"></i> Email Institucional</h6>
                        <a href="mailto:hdsgeral@gmail.com" class="text-primary fw-bold fs-6 text-decoration-none" style="color: #0058E6 !important;">hdsgeral@gmail.com</a>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-1"><i class="fas fa-phone-alt me-2 text-primary" style="color: #0058E6 !important;"></i> Telefones</h6>
                        <p class="text-secondary fs-6 mb-0">
                            (244) 923 692 943<br>
                            (244) 923 012 143
                        </p>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card border-0 shadow-lg p-4" style="border-radius: 20px;">
                        <h4 class="fw-bold text-dark mb-4">Pedir Demonstração ou Informação</h4>
                        
                        @if(session('success'))
                            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
                                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('contact.submit') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark fs-8">Nome Completo <span class="text-primary" style="color: #0058E6 !important;">*</span></label>
                                <input type="text" name="name" class="form-control py-2" placeholder="Seu nome" required>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark fs-8">Email Profissional <span class="text-primary" style="color: #0058E6 !important;">*</span></label>
                                    <input type="email" name="email" class="form-control py-2" placeholder="nome@empresa.co.ao" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark fs-8">Telefone / WhatsApp</label>
                                    <input type="text" name="phone" class="form-control py-2" placeholder="(244) 9..." required>
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
                            <button type="submit" class="btn btn-blue w-100 py-3 fs-7">
                                <i class="fas fa-paper-plane me-2"></i> Enviar Mensagem
                            </button>
                        </form>
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
                        <img src="{{ asset('img/logo_erp.png') }}" alt="Consulvolt Soluções" style="height: 42px; width: auto; object-fit: contain; background: #ffffff; padding: 4px 10px; border-radius: 8px;">
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

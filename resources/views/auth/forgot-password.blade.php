<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Palavra-passe - ERP Consulvolt</title>
    <!-- FontAwesome 6 & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #0058E6;
            --primary-hover: #0047b3;
            --dark-navy: #090d16;
            --navy-card: #0f172a;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--dark-navy);
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
        }

        .auth-container {
            min-height: 100vh;
            display: flex;
        }

        /* Lado Esquerdo (Institucional / Banner) - Fundo Branco (Desktop apenas) */
        .auth-banner {
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 4rem;
            position: relative;
            overflow: hidden;
        }

        .banner-feature-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.25rem;
            transition: all 0.3s ease;
        }

        .banner-feature-card:hover {
            background: #f1f5f9;
            border-color: #0058E6;
            transform: translateY(-2px);
        }

        /* Lado Direito (Formulário) - Fundo Escuro */
        .auth-form-wrapper {
            background: #090d16;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
            min-height: 100vh;
        }

        .auth-card {
            width: 100%;
            max-width: 440px;
        }

        .form-control {
            border-radius: 12px;
            padding: 0.85rem 1.1rem;
            background-color: #1e293b;
            border: 1px solid #334155;
            color: #ffffff;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .form-control::placeholder {
            color: #64748b;
        }

        .form-control:focus {
            background-color: #1e293b;
            color: #ffffff;
            border-color: #0058E6;
            box-shadow: 0 0 0 4px rgba(0, 88, 230, 0.25);
        }

        .input-group-text {
            border-radius: 12px 0 0 12px;
            background-color: #1e293b;
            border-color: #334155;
            color: #94a3b8;
        }

        .input-group .form-control {
            border-radius: 0 12px 12px 0;
        }

        .btn-primary-custom {
            background-color: #0058E6;
            border: none;
            border-radius: 12px;
            padding: 0.9rem;
            font-weight: 700;
            font-size: 1rem;
            color: #ffffff;
            box-shadow: 0 10px 20px -5px rgba(0, 88, 230, 0.4);
            transition: all 0.2s ease;
        }

        .btn-primary-custom:hover {
            background-color: #0047b3;
            transform: translateY(-2px);
            box-shadow: 0 14px 24px -5px rgba(0, 88, 230, 0.5);
            color: #fff;
        }

        .badge-agt {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.25);
            border-radius: 30px;
            padding: 0.4rem 1rem;
            font-size: 0.8rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Melhores práticas de Responsividade e Touch UI/UX */
        @media (max-width: 991px) {
            .auth-form-wrapper {
                padding: 2.5rem 1.25rem;
            }
            .form-control, .input-group-text {
                font-size: 1rem; /* Previne auto-zoom no iOS Safari */
                padding: 0.9rem 1rem;
            }
            .btn-primary-custom {
                padding: 1rem;
            }
        }

        @media (max-width: 576px) {
            .auth-form-wrapper {
                padding: 2rem 1rem;
            }
            .auth-card {
                padding: 0 0.5rem;
            }
        }
    </style>
</head>
<body>

<div class="auth-container row g-0">
    <!-- LADO ESQUERDO: Fundo Branco com o Logótipo Oficial (Visível apenas em Desktop >= 992px) -->
    <div class="col-lg-6 d-none d-lg-flex flex-column justify-content-between auth-banner">
        <div>
            <!-- Logótipo Oficial no lado esquerdo -->
            <a href="{{ route('home') }}" class="d-inline-block mb-5" title="Voltar ao Website">
                <img src="{{ asset('img/logo_erp.png') }}" alt="Consulvolt Soluções" style="height: 60px; width: auto; object-fit: contain;">
            </a>

            <div class="mt-4">
                <span class="badge-agt mb-3"><i class="fas fa-shield-alt"></i> Certificado AGT Angola n.º 142/AGT/2019</span>
                <h1 class="text-dark fw-bold display-5 mb-3" style="line-height: 1.2;">Segurança e Recuperação de Conta</h1>
                <p class="text-secondary lead fs-6 mb-5">Caso tenha esquecido a sua palavra-passe, enviaremos um endereço seguro de redefinição para o seu e-mail corporativo.</p>
            </div>
        </div>

        <div>
            <div class="banner-feature-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="text-primary fs-3" style="color: #0058E6 !important;"><i class="fas fa-shield-alt"></i></div>
                    <div>
                        <h6 class="text-dark fw-bold mb-1">Proteção de Dados Empresariais</h6>
                        <small class="text-muted">Conexão encriptada de alta segurança com registo de auditoria de acessos.</small>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center text-muted fs-7 mt-4 pt-3 border-top">
                <span>&copy; {{ date('Y') }} Consulvolt Soluções. NIF: 5417213969.</span>
                <span><a href="{{ route('home') }}" class="text-primary fw-bold text-decoration-none"><i class="fas fa-globe me-1"></i> Voltar ao Website</a></span>
            </div>
        </div>
    </div>

    <!-- LADO DIREITO: Formulário com Fundo Escuro -->
    <div class="col-lg-6 col-12 auth-form-wrapper">
        <div class="auth-card">
            <!-- Header Móvel: Apresenta o logótipo oficial em dispositivos móveis (< 992px) -->
            <div class="d-lg-none text-center mb-4">
                <a href="{{ route('home') }}" class="d-inline-block mb-2" title="Voltar ao Website">
                    <img src="{{ asset('img/logo_erp.png') }}" alt="Consulvolt Soluções" style="height: 52px; width: auto; object-fit: contain; background: transparent;">
                </a>
                <span class="d-block text-slate-400 fs-8" style="color: #94a3b8;"><a href="{{ route('home') }}" class="text-decoration-none" style="color: #60a5fa;"><i class="fas fa-arrow-left me-1"></i> Voltar ao Website</a></span>
            </div>

            <div class="mb-4 text-center text-lg-start">
                <h3 class="fw-bold text-white mb-1">Recuperar Palavra-passe</h3>
                <p class="text-slate-400" style="color: #94a3b8;">Introduza o e-mail associado à sua conta corporativa.</p>
            </div>

            @if(session('status'))
                <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4" style="background-color: #064e3b; color: #6ee7b7; border: 1px solid #047857;">
                    <i class="fas fa-check-circle me-2"></i> {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4" style="background-color: #450a0a; color: #fca5a5; border: 1px solid #7f1d1d;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle me-2 fs-5"></i>
                        <div>{{ $errors->first() }}</div>
                    </div>
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="email" class="form-label fw-semibold text-white">E-mail Corporativo <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="seu.email@empresa.com">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary-custom w-100 mb-4">
                    Enviar Instruções <i class="fas fa-paper-plane ms-2"></i>
                </button>
            </form>

            <div class="text-center pt-3 border-top" style="border-color: #1e293b !important;">
                <a href="{{ route('login') }}" class="fw-bold text-decoration-none fs-7" style="color: #60a5fa;"><i class="fas fa-arrow-left me-1"></i> Voltar ao Início de Sessão</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autenticação Corporativa — ERP Consulvolt</title>
    <!-- FontAwesome 6 & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #2563eb;
            --primary-hover: #1d4ed8;
            --dark-navy: #090d16;
            --navy-card: #0f172a;
            --accent-gold: #f59e0b;
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

        /* Left Side Banner */
        .auth-banner {
            background: radial-gradient(circle at 10% 20%, rgba(37, 99, 235, 0.25) 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, rgba(245, 158, 11, 0.15) 0%, transparent 40%),
                        linear-gradient(135deg, #090d16 0%, #0f172a 100%);
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 4rem;
            position: relative;
            overflow: hidden;
        }

        .auth-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 24px 24px;
            pointer-events: none;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .brand-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.5rem;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.4);
        }

        .brand-name {
            font-size: 1.6rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.5px;
        }

        .brand-name span {
            color: #3b82f6;
        }

        .banner-feature-card {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.25rem;
            transition: all 0.3s ease;
        }

        .banner-feature-card:hover {
            background: rgba(255, 255, 255, 0.07);
            border-color: rgba(59, 130, 246, 0.4);
            transform: translateY(-2px);
        }

        /* Right Side Form */
        .auth-form-wrapper {
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
        }

        .auth-card {
            width: 100%;
            max-width: 440px;
        }

        .form-control {
            border-radius: 12px;
            padding: 0.85rem 1.1rem;
            border: 1px solid #cbd5e1;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        .input-group-text {
            border-radius: 12px 0 0 12px;
            background-color: #f8fafc;
            border-color: #cbd5e1;
            color: #64748b;
        }

        .input-group .form-control {
            border-radius: 0 12px 12px 0;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: none;
            border-radius: 12px;
            padding: 0.9rem;
            font-weight: 700;
            font-size: 1rem;
            color: #ffffff;
            box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4);
            transition: all 0.2s ease;
        }

        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            transform: translateY(-2px);
            box-shadow: 0 14px 24px -5px rgba(37, 99, 235, 0.5);
            color: #fff;
        }

        .badge-agt {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 30px;
            padding: 0.4rem 1rem;
            font-size: 0.8rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
    </style>
</head>
<body>

<div class="auth-container row g-0">
    <!-- Left Hero Column -->
    <div class="col-lg-6 d-none d-lg-flex auth-banner">
        <div>
            <a href="{{ route('home') }}" class="brand-logo mb-5" title="Voltar ao Website">
                <img src="{{ asset('img/logo_erp.png') }}" alt="Consulvolt Soluções" style="height: 55px; width: auto; object-fit: contain; background: #ffffff; padding: 6px 14px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
            </a>

            <div class="mt-4">
                <span class="badge-agt mb-3"><i class="fas fa-shield-alt"></i> Certificado AGT Angola</span>
                <h1 class="text-white fw-bold display-5 mb-3" style="line-height: 1.2;">Gestão Empresarial Inteligente e Integrada</h1>
                <p class="text-white-50 lead fs-6 mb-5">Plataforma ERP multi-empresa com faturação homologada, tesouraria, salários com retenções IRT/INSS e contabilidade PGC.</p>
            </div>
        </div>

        <div>
            <div class="banner-feature-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="text-warning fs-3"><i class="fas fa-file-invoice-dollar"></i></div>
                    <div>
                        <h6 class="text-white fw-bold mb-1">Faturação Certificada AGT</h6>
                        <small class="text-white-50">Assinatura digital RSA 1024-bit e geração de ficheiro SAF-T AO XML.</small>
                    </div>
                </div>
            </div>

            <div class="banner-feature-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="text-primary fs-3"><i class="fas fa-calculator"></i></div>
                    <div>
                        <h6 class="text-white fw-bold mb-1">Recursos Humanos & Salários</h6>
                        <small class="text-white-50">Cálculo automatizado de IRT, INSS (3%/8%) e emissão de recibos.</small>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center text-white-50 fs-7 mt-4 pt-3 border-top border-white-10">
                <span>&copy; {{ date('Y') }} Consulvolt Soluções. NIF: 5417213969.</span>
                <span><a href="{{ route('home') }}" class="text-white-50 text-decoration-underline">Voltar ao Website</a></span>
            </div>
        </div>
    </div>

    <!-- Right Form Column -->
    <div class="col-lg-6 auth-form-wrapper">
        <div class="auth-card">
            <!-- Form Brand Logo Header -->
            <div class="text-center mb-4 pb-2">
                <a href="{{ route('home') }}" class="d-inline-block" title="Voltar ao Website">
                    <img src="{{ asset('img/logo_erp.png') }}" alt="Consulvolt Soluções" style="height: 65px; width: auto; object-fit: contain;">
                </a>
            </div>

            <div class="mb-4">
                <h3 class="fw-bold text-dark mb-1">Aceder à Plataforma</h3>
                <p class="text-muted">Introduza as suas credenciais para iniciar sessão.</p>
            </div>

            @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4" style="background-color: #fef2f2; color: #991b1b;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle me-2 fs-5"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4" style="background-color: #fef2f2; color: #991b1b;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle me-2 fs-5"></i>
                        <div>{{ $errors->first() }}</div>
                    </div>
                </div>
            @endif

            @if(session('status'))
                <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4" style="background-color: #ecfdf5; color: #065f46;">
                    <i class="fas fa-check-circle me-2"></i> {{ session('status') }}
                </div>
            @endif

            <!-- Botão Google OAuth 2.0 -->
            <a href="{{ route('auth.google.redirect') }}" class="btn btn-outline-dark w-100 mb-3 py-2 d-flex align-items-center justify-content-center gap-2" style="border-radius: 12px; border-color: #cbd5e1; font-weight: 600; font-size: 0.95rem; background: #ffffff; color: #1e293b; transition: all 0.2s;">
                <svg width="20" height="20" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                <span>Continuar com o Google</span>
            </a>

            <div class="d-flex align-items-center my-3">
                <hr class="border-secondary opacity-25 m-0" style="flex-grow: 1;">
                <span class="px-3 text-muted small fw-semibold">ou com palavra-passe</span>
                <hr class="border-secondary opacity-25 m-0" style="flex-grow: 1;">
            </div>

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold text-dark">Endereço de E-mail <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="utilizador@empresa.com">
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="password" class="form-label fw-semibold text-dark mb-0">Palavra-passe <span class="text-danger">*</span></label>
                        <a href="{{ route('password.request') }}" class="text-primary text-decoration-none fw-semibold" style="font-size: 0.85rem;">Esqueceu a palavra-passe?</a>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
                        <button type="button" class="btn btn-outline-secondary border-start-0 border-top border-bottom border-end" id="togglePassword" style="border-radius: 0 12px 12px 0;">
                            <i class="fas fa-eye text-muted"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label text-secondary fs-7" for="remember">
                            Manter sessão iniciada
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary-custom w-100 mb-4">
                    Entrar no Sistema <i class="fas fa-arrow-right ms-2"></i>
                </button>
            </form>

            <div class="text-center pt-3 border-top">
                <span class="text-muted fs-7">Ainda não tem conta de empresa?</span>
                <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none ms-1 fs-7">Registar Nova Empresa / Gestor</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('togglePassword')?.addEventListener('click', function() {
        const passInput = document.getElementById('password');
        const icon = this.querySelector('i');
        if (passInput.type === 'password') {
            passInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
</script>
</body>
</html>

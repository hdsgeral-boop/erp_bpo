<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperação de Palavra-passe — ERP Consulvolt</title>
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
            color: #fff;
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
                <h1 class="text-white fw-bold display-5 mb-3" style="line-height: 1.2;">Segurança e Recuperação de Conta</h1>
                <p class="text-white-50 lead fs-6 mb-5">Caso tenha esquecido a sua palavra-passe, enviaremos um endereço seguro de redefinição para o seu e-mail corporativo.</p>
            </div>
        </div>

        <div>
            <div class="banner-feature-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="text-info fs-3"><i class="fas fa-shield-alt"></i></div>
                    <div>
                        <h6 class="text-white fw-bold mb-1">Proteção de Dados Empresariais</h6>
                        <small class="text-white-50">Conexão encriptada de alta segurança com registo de auditoria de acessos.</small>
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
            <!-- Mobile Brand Header -->
            <div class="d-lg-none text-center mb-4">
                <a href="{{ route('home') }}" class="text-decoration-none d-inline-block" title="Voltar ao Website">
                    <img src="{{ asset('img/logo_erp.png') }}" alt="Consulvolt Soluções" style="height: 50px; width: auto; object-fit: contain;">
                </a>
            </div>

            <div class="mb-4">
                <h3 class="fw-bold text-dark mb-1">Recuperar Palavra-passe</h3>
                <p class="text-muted">Introduza o e-mail associado à sua conta corporativa.</p>
            </div>

            @if(session('status'))
                <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4" style="background-color: #ecfdf5; color: #065f46;">
                    <i class="fas fa-check-circle me-2"></i> {{ session('status') }}
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

            <form action="{{ route('password.email') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="email" class="form-label fw-semibold text-dark">E-mail Corporativo <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="seu.email@empresa.com">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary-custom w-100 mb-4">
                    Enviar Instruções <i class="fas fa-paper-plane ms-2"></i>
                </button>
            </form>

            <div class="text-center pt-3 border-top">
                <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none fs-7"><i class="fas fa-arrow-left me-1"></i> Voltar ao Inicio de Sessão</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>

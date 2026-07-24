<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registo de Gestor — ERP Consulvolt</title>
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
                        radial-gradient(circle at 90% 80%, rgba(16, 185, 129, 0.15) 0%, transparent 40%),
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
            max-width: 480px;
        }

        .form-control, .form-select {
            border-radius: 12px;
            padding: 0.8rem 1rem;
            border: 1px solid #cbd5e1;
            font-size: 0.925rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        .input-group-text {
            border-radius: 12px 0 0 12px;
            background-color: #f8fafc;
            border-color: #cbd5e1;
            color: #64748b;
        }

        .input-group .form-control, .input-group .form-select {
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
                <div class="brand-icon" style="background: linear-gradient(135deg, #0058E6, #2563eb);">
                    <i class="fas fa-bolt"></i>
                </div>
                <div class="brand-name">Consulvolt <span style="color: #60a5fa;">Soluções</span></div>
            </a>

            <div class="mt-4">
                <h1 class="text-white fw-bold display-5 mb-3" style="line-height: 1.2;">Registo de Gestor de Empresa</h1>
                <p class="text-white-50 lead fs-6 mb-5">Crie a sua conta de administrador e associe a sua organização ao ecossistema de gestão empresarial líder em Angola.</p>
            </div>
        </div>

        <div>
            <div class="banner-feature-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="text-success fs-3"><i class="fas fa-users-cog"></i></div>
                    <div>
                        <h6 class="text-white fw-bold mb-1">Controlo de Acessos & Perfis Spatie</h6>
                        <small class="text-white-50">Defina permissões por utilizador e departamento com registo completo de auditoria.</small>
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
                <a href="{{ route('home') }}" class="text-decoration-none">
                    <div class="brand-icon mx-auto mb-2" style="background: linear-gradient(135deg, #0058E6, #2563eb);">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-0">Consulvolt <span style="color: #0058E6;">Soluções</span></h3>
                </a>
            </div>

            <div class="mb-4">
                <h3 class="fw-bold text-dark mb-1">Registar Novo Gestor</h3>
                <p class="text-muted">Preencha os campos para criar a sua credencial de acesso.</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4" style="background-color: #fef2f2; color: #991b1b;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle me-2 fs-5"></i>
                        <div>{{ $errors->first() }}</div>
                    </div>
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
                <span>Registar com o Google</span>
            </a>

            <div class="d-flex align-items-center my-3">
                <hr class="border-secondary opacity-25 m-0" style="flex-grow: 1;">
                <span class="px-3 text-muted small fw-semibold">ou criar conta manual</span>
                <hr class="border-secondary opacity-25 m-0" style="flex-grow: 1;">
            </div>

            <form action="{{ route('register.post') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold text-dark">Nome Completo <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Ex: Pascoal Paulo">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold text-dark">Endereço de E-mail <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="gestor@empresa.com">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="company_id" class="form-label fw-semibold text-dark">Empresa a Associar <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                        <select id="company_id" name="company_id" class="form-select" required>
                            <option value="">Selecione a Empresa...</option>
                            @foreach($companies as $c)
                                <option value="{{ $c->id }}" {{ old('company_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} (NIF: {{ $c->nif ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-4">
                    <div class="col-md-6">
                        <label for="password" class="form-label fw-semibold text-dark">Palavra-passe <span class="text-danger">*</span></label>
                        <input type="password" id="password" name="password" class="form-control" required placeholder="Mín 8 caracteres">
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label fw-semibold text-dark">Confirmar <span class="text-danger">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required placeholder="Repita a palavra-passe">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary-custom w-100 mb-4">
                    Concluir Registo <i class="fas fa-check-circle ms-2"></i>
                </button>
            </form>

            <div class="text-center pt-3 border-top">
                <span class="text-muted fs-7">Já possui uma conta ativa?</span>
                <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none ms-1 fs-7">Iniciar Sessão</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>

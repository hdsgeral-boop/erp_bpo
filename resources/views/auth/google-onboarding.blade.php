<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concluir Registo Google - ERP Consulvolt</title>
    <!-- FontAwesome 6 & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #0058E6;
            --dark-navy: #090d16;
            --navy-card: #0f172a;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--dark-navy);
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .onboarding-card {
            width: 100%;
            max-width: 560px;
            background: #0f172a;
            border: 1px solid #1e293b;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
        }

        .form-control {
            border-radius: 12px;
            padding: 0.85rem 1.1rem;
            background-color: #1e293b;
            border: 1px solid #334155;
            color: #ffffff;
            font-size: 0.95rem;
        }

        .form-control:focus {
            background-color: #1e293b;
            color: #ffffff;
            border-color: #0058E6;
            box-shadow: 0 0 0 4px rgba(0, 88, 230, 0.25);
        }

        .form-control[readonly] {
            background-color: #090d16;
            color: #94a3b8;
            border-color: #1e293b;
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
            color: #fff;
        }

        .section-badge {
            background: rgba(0, 88, 230, 0.15);
            color: #60a5fa;
            border: 1px solid rgba(0, 88, 230, 0.3);
            border-radius: 8px;
            padding: 0.35rem 0.85rem;
            font-size: 0.85rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 1rem;
        }

        .user-google-pill {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 1.5rem;
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>

<div class="onboarding-card">
    <div class="text-center mb-4">
        <a href="{{ route('home') }}" class="d-inline-block mb-3">
            <img src="{{ asset('img/logo_erp.png') }}" alt="Consulvolt Soluções" style="height: 52px; width: auto; object-fit: contain;">
        </a>
        <h3 class="fw-bold text-white mb-1">Concluir Registo da Empresa</h3>
        <p class="text-slate-400" style="color: #94a3b8;">Autenticado via Google. Complete os dados obrigatórios da sua organização.</p>
    </div>

    <!-- Perfil Google Verificado -->
    <div class="user-google-pill">
        @if(!empty($googleData['avatar']))
            <img src="{{ $googleData['avatar'] }}" alt="Avatar" class="user-avatar">
        @else
            <div class="user-avatar bg-primary d-flex align-items-center justify-content-center text-white fw-bold">
                <i class="fab fa-google"></i>
            </div>
        @endif
        <div>
            <div class="text-white fw-bold fs-6">{{ $googleData['name'] }}</div>
            <div class="text-secondary small">{{ $googleData['email'] }} <i class="fas fa-check-circle text-success ms-1" title="Verificado pelo Google"></i></div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4" style="background-color: #450a0a; color: #fca5a5; border: 1px solid #7f1d1d;">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2 fs-5"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        </div>
    @endif

    <form action="{{ route('auth.google.onboarding.submit') }}" method="POST">
        @csrf

        <!-- SECÇÃO 1: EMPRESA -->
        <div class="section-badge"><i class="fas fa-building me-1"></i> DADOS DA EMPRESA</div>

        <div class="row">
            <div class="col-md-7 mb-3">
                <label for="company_name" class="form-label fw-semibold text-white">Nome da Empresa <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                    <input type="text" id="company_name" name="company_name" class="form-control" value="{{ old('company_name') }}" required placeholder="Ex: Consulvolt Soluções Lda">
                </div>
            </div>

            <div class="col-md-5 mb-3">
                <label for="company_nif" class="form-label fw-semibold text-white">NIF da Empresa <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                    <input type="text" id="company_nif" name="company_nif" class="form-control" value="{{ old('company_nif') }}" required placeholder="5417XXXXXX">
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6 mb-3 mb-md-0">
                <label for="company_email" class="form-label fw-semibold text-white">E-mail da Empresa</label>
                <input type="email" id="company_email" name="company_email" class="form-control" value="{{ old('company_email', $googleData['email']) }}" placeholder="geral@empresa.co.ao">
            </div>
            <div class="col-md-6">
                <label for="company_phone" class="form-label fw-semibold text-white">Telefone da Empresa</label>
                <input type="text" id="company_phone" name="company_phone" class="form-control" value="{{ old('company_phone') }}" placeholder="+244 923 000 000">
            </div>
        </div>

        <!-- SECÇÃO 2: UTILIZADOR -->
        <div class="section-badge"><i class="fas fa-user-edit me-1"></i> DADOS DO UTILIZADOR (ADMINISTRADOR)</div>

        <div class="mb-3">
            <label for="name" class="form-label fw-semibold text-white">Nome Completo <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $googleData['name']) }}" required>
            </div>
            <small class="text-secondary" style="font-size: 0.8rem;">Pode editar o seu nome obtido da conta Google.</small>
        </div>

        <div class="mb-3">
            <label for="google_email_display" class="form-label fw-semibold text-white">Endereço de E-mail Google</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" id="google_email_display" class="form-control" value="{{ $googleData['email'] }}" readonly>
            </div>
        </div>

        <div class="mb-4">
            <label for="phone" class="form-label fw-semibold text-white">Telefone Pessoal <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone') }}" required placeholder="+244 9XX XXX XXX">
            </div>
        </div>

        <button type="submit" class="btn btn-primary-custom w-100 mb-3">
            Finalizar Registo e Aceder ao ERP <i class="fas fa-arrow-right ms-2"></i>
        </button>
    </form>
</div>

</body>
</html>

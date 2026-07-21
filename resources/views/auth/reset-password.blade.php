<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Palavra-passe - ERP_CONSULT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .login-card { background: white; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); padding: 2.5rem; width: 100%; max-width: 400px; text-align: center; border-top: 4px solid var(--primary-color, #4f46e5); }
        .login-logo { width: 64px; height: 64px; margin-bottom: 1rem; object-fit: contain; }
        .login-title { font-size: 1.5rem; font-weight: 700; color: #1e293b; margin: 0 0 0.5rem; }
        .login-subtitle { font-size: 0.875rem; color: #64748b; margin-bottom: 2rem; }
        .form-group { text-align: left; margin-bottom: 1rem; }
        .form-group label { display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem; }
        .form-control { width: 100%; padding: 0.75rem 1rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 1rem; outline: none; transition: border-color 0.2s; box-sizing: border-box; }
        .form-control:focus { border-color: #4f46e5; }
        .btn-primary { width: 100%; padding: 0.75rem; background: #4f46e5; color: white; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: background 0.2s; margin-top: 1rem; }
        .btn-primary:hover { background: #4338ca; }
        .alert-danger { background: #fef2f2; border-left: 4px solid #ef4444; color: #b91c1c; padding: 1rem; border-radius: 4px; text-align: left; font-size: 0.875rem; margin-bottom: 1.5rem; }
    </style>
</head>
<body>
    <div class="login-card">
        <img src="{{ asset('img/logo_erp.png') }}" class="login-logo" alt="Logo">
        <h1 class="login-title">Criar Nova Palavra-passe</h1>
        <p class="login-subtitle">Por favor, defina a sua nova credencial de acesso.</p>

        @if($errors->any())
            <div class="alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label>E-mail Corporativo</label>
                <input type="email" name="email" class="form-control" value="{{ $email ?? old('email') }}" required autofocus readonly>
            </div>
            
            <div class="form-group">
                <label>Nova Palavra-passe</label>
                <input type="password" name="password" class="form-control" required placeholder="••••••••">
            </div>

            <div class="form-group">
                <label>Confirmar Nova Palavra-passe</label>
                <input type="password" name="password_confirmation" class="form-control" required placeholder="••••••••">
            </div>
            
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Guardar Nova Palavra-passe
            </button>
        </form>
    </div>
</body>
</html>

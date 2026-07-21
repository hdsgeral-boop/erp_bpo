<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP_CONSULT - Acesso Reservado</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            margin: 0; 
            padding: 0; 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            background: #0f172a; /* Fallback color */
            overflow: hidden;
        }

        /* --- VIDEO BACKGROUND RULES --- */
        .video-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover; /* Key for responsiveness! Cuts the edges smoothly */
            z-index: 0;
        }

        /* Overlay escuro para garantir que o formulário é legível */
        .video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.75); /* Azul escuro com 75% opacidade */
            z-index: 1;
        }

        /* --- LOGIN CARD RULES --- */
        .login-card { 
            background: rgba(255, 255, 255, 0.95); /* Leve transparência (Glassmorphism) */
            backdrop-filter: blur(10px);
            border-radius: 16px; 
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); 
            padding: 3rem 2.5rem; 
            width: 100%; 
            max-width: 420px; 
            text-align: center; 
            border-top: 4px solid var(--primary-color, #4f46e5); 
            z-index: 2; /* Garante que fica por cima do vídeo e do overlay */
            position: relative;
            margin: 1rem;
        }
        
        .login-logo { width: 72px; height: 72px; margin-bottom: 1.5rem; object-fit: contain; }
        .login-title { font-size: 1.75rem; font-weight: 700; color: #0f172a; margin: 0 0 0.5rem; letter-spacing: -0.025em; }
        .login-subtitle { font-size: 0.95rem; color: #64748b; margin-bottom: 2.5rem; }
        
        .form-group { text-align: left; margin-bottom: 1.25rem; }
        .form-group label { display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.5rem; }
        
        .form-control { 
            width: 100%; 
            padding: 0.85rem 1rem; 
            border: 1px solid #cbd5e1; 
            border-radius: 10px; 
            font-size: 1rem; 
            outline: none; 
            transition: all 0.2s; 
            box-sizing: border-box; 
            background: #f8fafc;
        }
        .form-control:focus { border-color: #4f46e5; background: #ffffff; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
        
        .btn-primary { 
            width: 100%; 
            padding: 0.85rem; 
            background: #4f46e5; 
            color: white; 
            border: none; 
            border-radius: 10px; 
            font-size: 1rem; 
            font-weight: 600; 
            cursor: pointer; 
            transition: all 0.2s; 
            margin-top: 1.5rem; 
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
        }
        .btn-primary:hover { background: #4338ca; transform: translateY(-1px); box-shadow: 0 6px 8px -1px rgba(79, 70, 229, 0.3); }
        
        .alert-danger { background: #fef2f2; border-left: 4px solid #ef4444; color: #b91c1c; padding: 1rem; border-radius: 6px; text-align: left; font-size: 0.875rem; margin-bottom: 1.5rem; font-weight: 500; }
        
        /* Checkbox personalizada */
        .checkbox-container { display: flex; align-items: center; gap: 0.5rem; }
        .checkbox-container input[type="checkbox"] { width: 16px; height: 16px; cursor: pointer; accent-color: #4f46e5; }
    </style>
</head>
<body>

    <!-- VÍDEO DE FUNDO -->
    <!-- 
        Nota: playsinline é crítico para iOS (iPhones). 
        autoplay arranca sozinho, loop repete, muted retira o som.
    -->
    <video class="video-bg" autoplay loop muted playsinline poster="https://images.pexels.com/photos/3183150/pexels-photo-3183150.jpeg?auto=compress&cs=tinysrgb&w=1920">
        <!-- Substitua o vídeo "login-bg.mp4" na pasta public/videos pelo seu ficheiro descarregado -->
        <source src="{{ asset('videos/login-bg.mp4') }}" type="video/mp4">
    </video>
    
    <!-- CAMADA ESCURA POR CIMA DO VÍDEO -->
    <div class="video-overlay"></div>

    <!-- FORMULÁRIO DE LOGIN (Fica por cima devido ao z-index: 2) -->
    <div class="login-card">
        <img src="{{ asset('img/logo_erp.png') }}" class="login-logo" alt="Logo">
        <h1 class="login-title">ERP Consulvolt</h1>
        <p class="login-subtitle">Acesso Reservado à Plataforma</p>

        @if($errors->any())
            <div class="alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="form-group">
                <label>E-mail Corporativo</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="nome@consulvolt.com">
            </div>
            <div class="form-group">
                <label>Palavra-passe</label>
                <input type="password" name="password" class="form-control" required placeholder="••••••••">
            </div>
            <div class="form-group" style="display: flex; align-items: center; justify-content: space-between;">
                <div class="checkbox-container">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember" style="margin: 0; font-weight: 500; cursor: pointer;">Lembrar-me</label>
                </div>
                <a href="{{ route('password.request') }}" style="font-size: 0.875rem; color: #4f46e5; text-decoration: none; font-weight: 600; transition: color 0.2s;">Esqueceu a senha?</a>
            </div>
            <button type="submit" class="btn-primary">
                <i class="fas fa-sign-in-alt me-2"></i> Entrar no Sistema
            </button>
        </form>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selecionar Empresa - ERP Consulvolt</title>
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
            padding: 2.5rem 1rem;
        }

        .workspace-card {
            width: 100%;
            max-width: 650px;
            background: #0f172a;
            border: 1px solid #1e293b;
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
        }

        .user-header-pill {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 14px;
            padding: 0.85rem 1.2rem;
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 1.75rem;
        }

        .user-avatar-initials {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, #0058E6 0%, #3b82f6 100%);
            color: #ffffff;
            font-weight: 800;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .company-card-button {
            width: 100%;
            background: #1e293b;
            border: 2px solid #334155;
            border-radius: 18px;
            padding: 1.35rem 1.5rem;
            margin-bottom: 1.1rem;
            text-align: left;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .company-card-button:hover {
            border-color: #0058E6;
            background: #172554;
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -6px rgba(0, 88, 230, 0.35);
        }

        .company-card-button:hover .arrow-indicator {
            transform: translateX(4px);
            color: #60a5fa !important;
        }

        .company-icon-box {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: rgba(0, 88, 230, 0.2);
            color: #60a5fa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            font-weight: 700;
            flex-shrink: 0;
            border: 1px solid rgba(0, 88, 230, 0.3);
        }

        .info-pill {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            padding: 0.3rem 0.75rem;
            font-size: 0.78rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .info-pill.role-admin {
            background: rgba(16, 185, 129, 0.12);
            color: #6ee7b7;
            border-color: rgba(16, 185, 129, 0.3);
        }

        .info-pill.role-gestor {
            background: rgba(59, 130, 246, 0.12);
            color: #93c5fd;
            border-color: rgba(59, 130, 246, 0.3);
        }

        .info-pill.role-user {
            background: rgba(148, 163, 184, 0.12);
            color: #cbd5e1;
            border-color: rgba(148, 163, 184, 0.3);
        }

        .info-pill.plan-pill {
            background: rgba(168, 85, 247, 0.12);
            color: #c084fc;
            border-color: rgba(168, 85, 247, 0.3);
        }

        .badge-status-active {
            background: rgba(34, 197, 94, 0.15);
            color: #4ade80;
            border: 1px solid rgba(34, 197, 94, 0.3);
            border-radius: 20px;
            padding: 0.25rem 0.65rem;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .badge-status-warning {
            background: rgba(234, 179, 8, 0.15);
            color: #fde047;
            border: 1px solid rgba(234, 179, 8, 0.3);
            border-radius: 20px;
            padding: 0.25rem 0.65rem;
            font-size: 0.75rem;
            font-weight: 700;
        }
    </style>
</head>
<body>

<div class="workspace-card">
    <div class="text-center mb-4">
        <a href="{{ route('home') }}" class="d-inline-block mb-3">
            <img src="{{ asset('img/logo_erp.png') }}" alt="Consulvolt Soluções" style="height: 52px; width: auto; object-fit: contain;">
        </a>
        <h3 class="fw-bold text-white mb-1">Selecione a Empresa</h3>
        <p class="text-slate-400" style="color: #94a3b8;">Escolha a organização onde pretende trabalhar neste momento.</p>
    </div>

    <!-- Pílula do Utilizador Autenticado -->
    <div class="user-header-pill">
        <div class="user-avatar-initials">
            {{ strtoupper(substr($user->name ?? 'U', 0, 2)) }}
        </div>
        <div class="grow">
            <div class="text-white fw-bold fs-6">{{ $user->name }}</div>
            <div class="text-secondary small">{{ $user->email }}</div>
        </div>
        <span class="badge bg-secondary opacity-75 fw-normal fs-8">Sessão Ativa</span>
    </div>

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4" style="background-color: #450a0a; color: #fca5a5; border: 1px solid #7f1d1d;">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="mb-4">
        @foreach($companyCards as $comp)
            <form action="{{ route('company.select.post') }}" method="POST" class="m-0">
                @csrf
                <input type="hidden" name="company_id" value="{{ $comp['id'] }}">
                <button type="submit" class="company-card-button">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="company-icon-box">
                                @if($comp['logo'])
                                    <img src="{{ asset('storage/' . $comp['logo']) }}" alt="{{ $comp['name'] }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">
                                @else
                                    <i class="fas fa-building"></i>
                                @endif
                            </div>
                            <div>
                                <h5 class="text-white fw-bold mb-1" style="font-size: 1.1rem;">{{ $comp['name'] }}</h5>
                                <div class="text-secondary small"><i class="fas fa-hashtag me-1 opacity-50"></i> NIF: {{ $comp['nif'] }}</div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <span class="{{ $comp['is_active'] ? 'badge-status-active' : 'badge-status-warning' }}">
                                <i class="fas fa-circle me-1" style="font-size: 0.5rem; vertical-align: middle;"></i> {{ $comp['status'] }}
                            </span>
                            <i class="fas fa-arrow-right text-muted fs-5 ms-2 arrow-indicator" style="transition: transform 0.2s;"></i>
                        </div>
                    </div>

                    <!-- Metadados Informativos (Cargo & Plano) -->
                    <div class="d-flex align-items-center gap-2 pt-2 border-top border-secondary border-opacity-25 w-100">
                        <span class="info-pill {{ $comp['role'] === 'Administrador' ? 'role-admin' : ($comp['role'] === 'Gestor' ? 'role-gestor' : 'role-user') }}">
                            <i class="fas fa-user-shield"></i> Cargo: {{ $comp['role'] }}
                        </span>
                        <span class="info-pill plan-pill">
                            <i class="fas fa-crown"></i> {{ $comp['plan'] }}
                        </span>
                        @if($comp['days_remaining'] <= 5)
                            <span class="info-pill text-warning border-warning">
                                <i class="fas fa-clock"></i> {{ $comp['days_remaining'] }} dias restantes
                            </span>
                        @endif
                    </div>
                </button>
            </form>
        @endforeach
    </div>

    <div class="text-center pt-3 border-top" style="border-color: #1e293b !important;">
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-secondary text-decoration-none small hover-white">
            <i class="fas fa-sign-out-alt me-1"></i> Terminar Sessão / Alternar Utilizador
        </a>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</div>

</body>
</html>

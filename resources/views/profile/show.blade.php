@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
            <i class="fas fa-exclamation-triangle me-2"></i> <strong>Atenção:</strong> Por favor verifique os erros no formulário:
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

    <!-- Profile Header Banner Card -->
    <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="border-radius: 16px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
        <div class="card-body p-4 text-white">
            <div class="d-flex flex-column flex-md-row align-items-center gap-4">
                <div class="user-avatar-lg shadow-lg d-flex align-items-center justify-content-center fw-bold" style="width: 90px; height: 90px; border-radius: 20px; background: linear-gradient(135deg, #2563eb, #3b82f6); font-size: 2.2rem; border: 3px solid rgba(255,255,255,0.2);">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div class="text-center text-md-start flex-grow-1">
                    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-2 mb-1">
                        <h2 class="fw-bold mb-0 text-white" style="font-size: 1.6rem;">{{ $user->name }}</h2>
                        <span class="badge bg-primary px-3 py-2 fs-8 fw-semibold" style="border-radius: 8px;">
                            <i class="fas fa-shield-alt me-1"></i> {{ $user->roles->first()?->name ?? 'Utilizador do Sistema' }}
                        </span>
                    </div>
                    <p class="text-light opacity-75 mb-2" style="font-size: 0.95rem;">
                        <i class="fas fa-envelope me-1"></i> {{ $user->email }}
                        @if($user->phone)
                            <span class="mx-2">•</span> <i class="fas fa-phone me-1"></i> {{ $user->phone }}
                        @endif
                    </p>
                    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-3 fs-8 text-light opacity-75">
                        <span>{{ $user->companies->first()?->name ?? 'Sem Empresa Fixa' }}</span>
                        <span><i class="fas fa-briefcase me-1 text-warning"></i> {{ $user->job_title ?? 'Colaborador ERP' }}</span>
                        <span><i class="fas fa-calendar-alt me-1 text-success"></i> Membro desde {{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sidebar Navigation Tabs -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm p-2" style="border-radius: 16px;">
                <div class="nav flex-column nav-pills" id="profile-tabs" role="tablist" aria-orientation="vertical">
                    <button class="nav-link active text-start py-3 px-3 fw-bold rounded-3 mb-1" id="tab-info-btn" data-bs-toggle="pill" data-bs-target="#tab-info" type="button" role="tab">
                        <i class="fas fa-id-card text-primary me-2 width-20"></i> Dados Pessoais
                    </button>
                    <button class="nav-link text-start py-3 px-3 fw-bold rounded-3 mb-1" id="tab-security-btn" data-bs-toggle="pill" data-bs-target="#tab-security" type="button" role="tab">
                        <i class="fas fa-lock text-warning me-2 width-20"></i> Segurança & Senha
                    </button>
                    <button class="nav-link text-start py-3 px-3 fw-bold rounded-3 mb-1" id="tab-company-btn" data-bs-toggle="pill" data-bs-target="#tab-company" type="button" role="tab">
                        <i class="fas fa-building text-info me-2 width-20"></i> Empresa & Permissões
                    </button>
                    <button class="nav-link text-start py-3 px-3 fw-bold rounded-3 mb-1" id="tab-prefs-btn" data-bs-toggle="pill" data-bs-target="#tab-prefs" type="button" role="tab">
                        <i class="fas fa-sliders-h text-secondary me-2 width-20"></i> Preferências de Conta
                    </button>
                </div>
            </div>
        </div>

        <!-- Tab Contents -->
        <div class="col-lg-9">
            <div class="tab-content" id="profile-tabContent">

                <!-- TAB 1: Dados Pessoais -->
                <div class="tab-pane fade show active" id="tab-info" role="tabpanel">
                    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                        <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                            <h5 class="fw-bold mb-1"><i class="fas fa-user-edit text-primary me-2"></i>Informações Pessoais</h5>
                            <p class="text-muted fs-8 mb-0">Gerencie as suas informações de contacto e identificação no ERP.</p>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Nome Completo <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Endereço de E-mail <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-dark">Telefone / Telemóvel</label>
                                        <input type="text" name="phone" class="form-control" placeholder="+244 9XX XXX XXX" value="{{ old('phone', $user->phone) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-dark">Cargo / Função</label>
                                        <input type="text" name="job_title" class="form-control" placeholder="Ex.: Gestor Comercial" value="{{ old('job_title', $user->job_title) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-dark">Departamento</label>
                                        <input type="text" name="department" class="form-control" placeholder="Ex.: Financeiro" value="{{ old('department', $user->department) }}">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-semibold text-dark">Biografia / Observações</label>
                                        <textarea name="bio" class="form-control" rows="3" placeholder="Escreva uma breve nota sobre as suas responsabilidades no sistema...">{{ old('bio', $user->bio) }}</textarea>
                                    </div>
                                </div>

                                <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary fw-bold px-4" style="border-radius: 10px;">
                                        <i class="fas fa-save me-1"></i> Guardar Alterações
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: Segurança & Senha -->
                <div class="tab-pane fade" id="tab-security" role="tabpanel">
                    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                        <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                            <h5 class="fw-bold mb-1"><i class="fas fa-key text-warning me-2"></i>Alterar Palavra-passe</h5>
                            <p class="text-muted fs-8 mb-0">Mantenha a sua conta segura atualizando a sua senha periodicamente.</p>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('profile.password.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row g-3" style="max-width: 600px;">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold text-dark">Palavra-passe Atual <span class="text-danger">*</span></label>
                                        <input type="password" name="current_password" class="form-control" placeholder="••••••••" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Nova Palavra-passe <span class="text-danger">*</span></label>
                                        <input type="password" name="password" class="form-control" placeholder="Mínimo 8 caracteres" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Confirmar Nova Palavra-passe <span class="text-danger">*</span></label>
                                        <input type="password" name="password_confirmation" class="form-control" placeholder="Repita a nova senha" required>
                                    </div>
                                </div>

                                <div class="mt-4 pt-3 border-top d-flex justify-content-start">
                                    <button type="submit" class="btn btn-warning fw-bold px-4 text-dark" style="border-radius: 10px;">
                                        <i class="fas fa-lock me-1"></i> Atualizar Palavra-passe
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: Empresa & Permissões -->
                <div class="tab-pane fade" id="tab-company" role="tabpanel">
                    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                        <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                            <h5 class="fw-bold mb-1"><i class="fas fa-building text-info me-2"></i>Empresa & Permissões de Acesso</h5>
                            <p class="text-muted fs-8 mb-0">Consulte as empresas associadas à sua conta e as respetivas permissões de sistema.</p>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-3 bg-light">
                                        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-building text-primary me-2"></i>Empresas Autorizadas</h6>
                                        @if($user->companies->count() > 0)
                                            <ul class="list-group list-group-flush border-0">
                                                @foreach($user->companies as $comp)
                                                    <li class="list-group-bg border-0 px-0 py-2 d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <strong class="text-dark">{{ $comp->name }}</strong>
                                                            <div class="text-muted fs-8">NIF: {{ $comp->nif ?? 'N/A' }}</div>
                                                        </div>
                                                        <span class="badge bg-success">Ativa</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-muted fs-8 mb-0">Acesso Global a Todas as Empresas (Modo Administrador Geral)</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="p-3 border rounded-3 bg-light">
                                        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-user-shield text-warning me-2"></i>Perfil e Função</h6>
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <span class="badge bg-dark px-3 py-2 fs-7">
                                                {{ $user->roles->first()?->name ?? 'Super Admin' }}
                                            </span>
                                        </div>
                                        <p class="text-muted fs-8 mb-0">O seu perfil define as capacidades de leitura, criação e edição nos módulos de Vendas, Logística, RH, Tesouraria e Contabilidade.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 4: Preferências -->
                <div class="tab-pane fade" id="tab-prefs" role="tabpanel">
                    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                        <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                            <h5 class="fw-bold mb-1"><i class="fas fa-sliders-h text-secondary me-2"></i>Preferências Globais</h5>
                            <p class="text-muted fs-8 mb-0">Personalize a sua experiência de utilização no ERP.</p>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3" style="max-width: 600px;">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Idioma do Sistema</label>
                                    <select class="form-select border-1" disabled>
                                        <option selected>Português (Angola / PGC)</option>
                                        <option>English (US)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Notificações por E-mail</label>
                                    <select class="form-select border-1">
                                        <option selected>Todas as Notificações Relevantes</option>
                                        <option>Apenas Alertas Críticos e Faturação</option>
                                        <option>Desativado</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

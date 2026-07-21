@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    .form-control, .form-select {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border: 1px solid #cbd5e1;
    }
    .form-control:focus, .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .form-label {
        font-weight: 600;
        color: #475569;
        font-size: 0.9rem;
    }
    .btn-save {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border-radius: 10px;
        padding: 0.6rem 2rem;
        font-weight: 600;
        border: none;
        box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
        transition: all 0.2s;
    }
    .btn-save:hover { color: white; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.4); }
    .btn-cancel {
        background: #f1f5f9;
        color: #475569;
        border-radius: 10px;
        padding: 0.6rem 2rem;
        font-weight: 600;
        border: none;
        transition: all 0.2s;
    }
    .btn-cancel:hover { background: #e2e8f0; color: #1e293b; }
    .check-list-container {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 1rem;
        max-height: 250px;
        overflow-y: auto;
        background: #f8fafc;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('admin.users.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="fas fa-arrow-left me-1"></i> Voltar à Listagem
        </a>
        <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-user-edit text-primary me-2"></i>Editar Utilizador</h2>
        <p class="text-muted mb-0">Modificar credenciais e acessos de {{ $user->name }}.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm" style="border-radius: 10px; border-left: 4px solid #ef4444;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-premium p-4 p-md-5">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row g-5">
                <div class="col-md-7">
                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-4">Dados de Autenticação</h5>
                    
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label">Nome Completo <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-user text-muted"></i></span>
                                <input type="text" name="name" class="form-control border-start-0" value="{{ old('name', $user->name) }}" required>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label">Endereço de Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" name="email" class="form-control border-start-0" value="{{ old('email', $user->email) }}" required>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label">Nova Palavra-passe</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-lock text-muted"></i></span>
                                <input type="password" name="password" class="form-control border-start-0" placeholder="Deixe em branco para manter a atual">
                            </div>
                            <div class="form-text">Preencha apenas se desejar redefinir a palavra-passe atual.</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-4">Segurança e Acessos</h5>

                    <div class="mb-4">
                        <label class="form-label">Perfil de Acesso (Role) <span class="text-danger">*</span></label>
                        <select name="role_id" class="form-select" required>
                            <option value="">Selecione um perfil...</option>
                            @php $userRoleId = $user->roles->first()->id ?? null; @endphp
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ (old('role_id') ?? $userRoleId) == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">O perfil determina as permissões globais deste utilizador no sistema.</div>
                    </div>

                    <div>
                        <label class="form-label">Empresas Autorizadas</label>
                        <div class="check-list-container">
                            @php $userCompanies = $user->companies->pluck('id')->toArray(); @endphp
                            @foreach($companies as $comp)
                            <div class="form-check mb-2 pb-2 border-bottom">
                                <input class="form-check-input" type="checkbox" name="companies[]" value="{{ $comp->id }}" id="comp_{{ $comp->id }}" 
                                    {{ (is_array(old('companies')) && in_array($comp->id, old('companies'))) || in_array($comp->id, $userCompanies) ? 'checked' : '' }}>
                                <label class="form-check-label ms-2 text-dark" for="comp_{{ $comp->id }}">
                                    {{ $comp->name }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                        <div class="form-text mt-2">Selecione as empresas às quais o utilizador pode aceder.</div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3 mt-5 border-top pt-4">
                <a href="{{ route('admin.users.index') }}" class="btn btn-cancel">Cancelar</a>
                <button type="submit" class="btn btn-save"><i class="fas fa-save me-2"></i> Guardar Alterações</button>
            </div>
        </form>
    </div>
</div>
@endsection

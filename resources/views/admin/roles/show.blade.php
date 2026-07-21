@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    .info-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    .info-value {
        font-size: 1.05rem;
        color: #1e293b;
        font-weight: 500;
        margin-bottom: 1.5rem;
    }
    .btn-edit {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border-radius: 10px;
        padding: 0.6rem 2rem;
        font-weight: 600;
        border: none;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
    }
    .btn-edit:hover { color: white; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4); }
    
    .module-badge {
        display: inline-block;
        background: #f1f5f9;
        color: #334155;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        margin: 0.2rem;
        font-size: 0.85rem;
        border: 1px solid #e2e8f0;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.roles.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
                <i class="fas fa-arrow-left me-1"></i> Voltar à Listagem
            </a>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-shield-alt text-primary me-2"></i>Detalhes do Perfil</h2>
        </div>
        <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-edit">
            <i class="fas fa-edit me-2"></i> Editar Perfil
        </a>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card-premium p-4 h-100">
                <div class="text-center mb-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                        @if($role->id === 1)
                            <i class="fas fa-crown text-warning fa-2x"></i>
                        @else
                            <i class="fas fa-shield-alt text-primary fa-2x"></i>
                        @endif
                    </div>
                    <h4 class="fw-bold text-dark">{{ $role->name }}</h4>
                    <span class="badge bg-info text-dark mt-2">{{ $role->users()->count() }} Utilizadores</span>
                </div>
                
                <div class="border-top pt-4">
                    <div class="info-label">Data de Criação</div>
                    <div class="info-value">{{ $role->created_at ? $role->created_at->format('d/m/Y H:i') : 'N/D' }}</div>
                    
                    <div class="info-label">Última Atualização</div>
                    <div class="info-value">{{ $role->updated_at ? $role->updated_at->format('d/m/Y H:i') : 'N/D' }}</div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card-premium p-4 h-100">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-4">Permissões Concedidas</h5>
                
                @if($role->id === 1)
                    <div class="alert alert-success d-flex align-items-center">
                        <i class="fas fa-check-circle fa-2x me-3"></i>
                        <div>
                            <strong>Acesso Total</strong><br>
                            Este perfil possui direitos administrativos completos sobre todo o sistema. Nenhuma restrição é aplicada.
                        </div>
                    </div>
                @else
                    @if($permissions->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-lock fa-2x mb-3 d-block"></i>
                            Este perfil não tem permissões atribuídas.
                        </div>
                    @else
                        @foreach($permissions as $module => $modulePermissions)
                        <div class="mb-4">
                            <h6 class="fw-bold text-muted text-capitalize mb-2"><i class="fas fa-cube me-2"></i>{{ $module }}</h6>
                            <div class="d-flex flex-wrap">
                                @foreach($modulePermissions as $perm)
                                    <span class="module-badge">
                                        <i class="fas fa-check text-success me-1"></i> {{ str_replace($module.'.', '', $perm->name) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

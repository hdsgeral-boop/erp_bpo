@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    .table-custom { margin-bottom: 0; }
    .table-custom thead th {
        background-color: #ffffff;
        color: #475569;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        padding: 1rem 1.5rem;
        border-bottom: 2px solid #e2e8f0;
    }
    .table-custom tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-custom tbody tr:hover { background-color: #f8fafc; }
    .btn-action {
        border-radius: 8px;
        padding: 0.4rem 0.8rem;
        transition: all 0.2s;
    }
    .btn-action:hover {
        background: #f1f5f9;
        transform: translateY(-2px);
    }
    .btn-add-new {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border-radius: 10px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
    }
    .btn-add-new:hover { color: white; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4); }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-users-cog text-primary me-2"></i>Gestão de Utilizadores</h2>
            <p class="text-muted mb-0">Controlo de acessos, contas e perfis de segurança do sistema.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-add-new">
            <i class="fas fa-plus me-2"></i> Novo Utilizador
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm" style="border-radius: 10px; border-left: 4px solid #10b981;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger shadow-sm" style="border-radius: 10px; border-left: 4px solid #ef4444;">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card-premium">
        <div class="p-4 border-bottom">
            <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Pesquisar por nome ou email..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100" style="border-radius: 8px;">Filtrar</button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th width="60"></th>
                        <th>Nome Completo</th>
                        <th>Email</th>
                        <th>Perfil Principal</th>
                        <th>Empresas Autorizadas</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="text-center">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center fw-bold text-primary" style="width: 40px; height: 40px; border: 2px solid #e2e8f0;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        </td>
                        <td class="fw-bold text-dark">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @php $role = $user->roles->first(); @endphp
                            @if($role)
                                <span class="badge" style="background-color: #e0f2fe; color: #0284c7; padding: 0.5em 0.8em; border-radius: 6px;">
                                    <i class="fas fa-shield-alt me-1"></i> {{ $role->name }}
                                </span>
                            @else
                                <span class="badge bg-secondary" style="padding: 0.5em 0.8em; border-radius: 6px;">Sem Perfil</span>
                            @endif
                        </td>
                        <td>
                            @forelse($user->companies as $comp)
                                <span class="badge" style="background-color: #f1f5f9; color: #475569; padding: 0.4em 0.7em; border-radius: 4px; margin-right: 4px; border: 1px solid #cbd5e1;">
                                    <i class="fas fa-building text-muted me-1"></i> {{ $comp->name }}
                                </span>
                            @empty
                                <span class="text-muted fst-italic" style="font-size: 0.85rem;">Nenhuma empresa</span>
                            @endforelse
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-action text-primary" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if(auth()->id() !== $user->id)
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-action text-danger" title="Eliminar" onclick="return confirm('Tem certeza que deseja eliminar este utilizador?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-users fa-2x mb-3 d-block"></i>
                            Nenhum utilizador encontrado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection

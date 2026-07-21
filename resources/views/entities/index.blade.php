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
    .btn-action:hover { background: #f1f5f9; transform: translateY(-2px); }
    .btn-add-new {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border-radius: 10px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
    }
    .btn-add-new:hover { color: white; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4); }
    .nav-tabs-premium .nav-link {
        color: #64748b;
        border: none;
        border-bottom: 3px solid transparent;
        font-weight: 600;
        padding: 1rem 1.5rem;
    }
    .nav-tabs-premium .nav-link:hover { color: #3b82f6; }
    .nav-tabs-premium .nav-link.active {
        color: #3b82f6;
        background: transparent;
        border-bottom: 3px solid #3b82f6;
    }
    .role-badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
        border-radius: 6px;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-users text-primary me-2"></i>Entidades</h2>
            <p class="text-muted mb-0">Gestão centralizada de clientes, fornecedores e terceiros.</p>
        </div>
        <a href="{{ route('entidades.create') }}" class="btn btn-add-new">
            <i class="fas fa-plus me-2"></i> Nova Entidade
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
        <!-- Tabs -->
        <ul class="nav nav-tabs nav-tabs-premium border-bottom" id="entityTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ request('type') == null ? 'active' : '' }}" href="{{ route('entidades.index') }}">
                    <i class="fas fa-list me-2"></i>Todas
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ request('type') == 'customer' ? 'active' : '' }}" href="{{ route('entidades.index', ['type' => 'customer', 'search' => request('search')]) }}">
                    <i class="fas fa-user-tag me-2"></i>Clientes
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ request('type') == 'supplier' ? 'active' : '' }}" href="{{ route('entidades.index', ['type' => 'supplier', 'search' => request('search')]) }}">
                    <i class="fas fa-truck me-2"></i>Fornecedores
                </a>
            </li>
        </ul>

        <div class="p-4 border-bottom bg-light">
            <form action="{{ route('entidades.index') }}" method="GET" class="row g-3 align-items-center">
                @if(request('type'))
                    <input type="hidden" name="type" value="{{ request('type') }}">
                @endif
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Pesquisar por Nome, NIF ou Conta..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100" style="border-radius: 8px;">Pesquisar</button>
                </div>
                @if(request('search') || request('type'))
                <div class="col-md-2">
                    <a href="{{ route('entidades.index') }}" class="btn btn-outline-secondary w-100" style="border-radius: 8px;">Limpar</a>
                </div>
                @endif
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>NIF</th>
                        <th>Nome da Entidade</th>
                        <th>Papéis</th>
                        <th>Conta SNC</th>
                        <th>Estado</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($thirdParties as $entidade)
                    <tr>
                        <td class="text-muted fw-bold">{{ $entidade->nif ?: 'N/A' }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $entidade->name }}</div>
                            @if($entidade->city)
                                <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>{{ $entidade->city }}</small>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                @if($entidade->is_customer)
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary role-badge">Cliente</span>
                                @endif
                                @if($entidade->is_supplier)
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info role-badge">Fornecedor</span>
                                @endif
                                @if(!$entidade->is_customer && !$entidade->is_supplier)
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary role-badge">Outro</span>
                                @endif
                            </div>
                        </td>
                        <td><span class="badge bg-light text-dark border font-monospace">{{ $entidade->account_code ?: 'N/A' }}</span></td>
                        <td>
                            @if($entidade->is_active)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="fas fa-check-circle me-1"></i>Ativa</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary"><i class="fas fa-times-circle me-1"></i>Inativa</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('entidades.show', $entidade->id) }}" class="btn btn-sm btn-action text-info" title="Ver Detalhes">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('entidades.edit', $entidade->id) }}" class="btn btn-sm btn-action text-primary" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('entidades.destroy', $entidade->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-action text-danger" title="Eliminar" onclick="return confirm('Tem certeza que deseja eliminar esta entidade?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-users fa-2x mb-3 d-block"></i>
                            Nenhuma entidade encontrada.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($thirdParties->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $thirdParties->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection

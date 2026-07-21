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
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-id-badge text-primary me-2"></i>Cargos (Funções)</h2>
            <p class="text-muted mb-0">Gestão de cargos organizacionais.</p>
        </div>
        <a href="{{ route('config.positions.create') }}" class="btn btn-add-new">
            <i class="fas fa-plus me-2"></i> Novo Cargo
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
            <form action="{{ route('config.positions.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Pesquisar por título ou código..." value="{{ request('search') }}">
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
                        <th>Código</th>
                        <th>Título do Cargo</th>
                        <th>Departamento</th>
                        <th>Nível Diretivo</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($positions as $position)
                    <tr>
                        <td class="fw-bold text-muted">{{ $position->code }}</td>
                        <td class="fw-bold text-dark">{{ $position->title }}</td>
                        <td>
                            @if($position->department)
                                <span class="badge" style="background-color: #f1f5f9; color: #475569; padding: 0.5em 0.8em; border-radius: 6px;">
                                    <i class="fas fa-sitemap me-1"></i> {{ $position->department->name }}
                                </span>
                            @else
                                <span class="text-muted fst-italic">Transversal</span>
                            @endif
                        </td>
                        <td>
                            @if($position->is_management)
                                <span class="badge bg-warning text-dark"><i class="fas fa-star me-1"></i> Direção/Chefia</span>
                            @else
                                <span class="badge bg-secondary">Operacional</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('config.positions.show', $position->id) }}" class="btn btn-sm btn-action text-info" title="Ver Detalhes">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('config.positions.edit', $position->id) }}" class="btn btn-sm btn-action text-primary" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('config.positions.destroy', $position->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-action text-danger" title="Eliminar" onclick="return confirm('Tem certeza que deseja eliminar este cargo?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-id-badge fa-2x mb-3 d-block"></i>
                            Nenhum cargo encontrado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($positions->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $positions->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection

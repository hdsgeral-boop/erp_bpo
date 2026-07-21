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
    .status-active { color: #10b981; background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; }
    .status-sold { color: #f59e0b; background: rgba(245, 158, 11, 0.1); border: 1px solid #f59e0b; }
    .status-written-off { color: #ef4444; background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-boxes text-primary me-2"></i>Gestão de Ativos</h2>
            <p class="text-muted mb-0">Imobilizado, equipamentos e alocações.</p>
        </div>
        <a href="{{ route('ativos.create') }}" class="btn btn-add-new">
            <i class="fas fa-plus me-2"></i> Novo Ativo
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
        <div class="p-4 border-bottom bg-light">
            <form action="{{ route('ativos.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold mb-1">Pesquisa</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Código ou Nome..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold mb-1">Categoria</label>
                    <select name="category_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold mb-1">Departamento</label>
                    <select name="department_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold mb-1">Funcionário</label>
                    <select name="employee_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1" style="border-radius: 8px;">Filtrar</button>
                    @if(request('search') || request('category_id') || request('department_id') || request('employee_id'))
                        <a href="{{ route('ativos.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">Limpar</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Ativo</th>
                        <th>Alocação Atual</th>
                        <th>Aquisição</th>
                        <th>Valor Contabilístico</th>
                        <th>Estado</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 rounded p-2 me-3 text-primary">
                                    <i class="fas fa-desktop fa-lg"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $asset->name }}</div>
                                    <small class="text-muted font-monospace">COD: {{ $asset->code }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($asset->employee)
                                <div class="text-dark small"><i class="fas fa-user text-muted me-1"></i> {{ $asset->employee->name }}</div>
                            @endif
                            @if($asset->department)
                                <div class="text-muted small"><i class="fas fa-building text-muted me-1"></i> {{ $asset->department->name }}</div>
                            @endif
                            @if(!$asset->employee && !$asset->department)
                                <span class="text-muted small fst-italic">Não alocado</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-dark small">{{ $asset->purchase_date ? $asset->purchase_date->format('d/m/Y') : '-' }}</div>
                            <div class="text-muted small">{{ number_format($asset->purchase_value, 2, ',', '.') }} AOA</div>
                        </td>
                        <td>
                            <div class="text-dark small">{{ number_format($asset->residual_value, 2, ',', '.') }} AOA</div>
                        </td>
                        <td>
                            @if($asset->status === 'active')
                                <span class="badge status-active px-2 py-1"><i class="fas fa-check-circle me-1"></i>Ativo</span>
                            @elseif($asset->status === 'sold')
                                <span class="badge status-sold px-2 py-1"><i class="fas fa-handshake me-1"></i>Vendido</span>
                            @elseif($asset->status === 'written_off')
                                <span class="badge status-written-off px-2 py-1"><i class="fas fa-times-circle me-1"></i>Abatido</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('ativos.show', $asset->id) }}" class="btn btn-sm btn-action text-info" title="Ver Perfil">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('ativos.edit', $asset->id) }}" class="btn btn-sm btn-action text-primary" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($asset->status === 'active')
                            <form action="{{ route('ativos.destroy', $asset->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-action text-danger" title="Abater Ativo" onclick="return confirm('Tem certeza que deseja abater este ativo do imobilizado?')">
                                    <i class="fas fa-arrow-down"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-box-open fa-2x mb-3 d-block"></i>
                            Nenhum ativo encontrado com estes filtros.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($assets->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $assets->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection

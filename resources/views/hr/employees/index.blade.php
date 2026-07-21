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
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #e2e8f0;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.1rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-user-tie text-primary me-2"></i>Recursos Humanos</h2>
            <p class="text-muted mb-0">Gestão de colaboradores, perfis e alocações.</p>
        </div>
        <a href="{{ route('rh.funcionarios.create') }}" class="btn btn-add-new">
            <i class="fas fa-plus me-2"></i> Novo Colaborador
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
            <form action="{{ route('rh.funcionarios.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold mb-1">Pesquisa</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Nome, NIF, Email..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold mb-1">Departamento</label>
                    <select name="department_id" class="form-select">
                        <option value="">Todos os Departamentos</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold mb-1">Estado</label>
                    <select name="is_active" class="form-select">
                        <option value="">Todos</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Ativos</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inativos</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1" style="border-radius: 8px;">Filtrar</button>
                    @if(request('search') || request('department_id') || request('is_active') !== null)
                        <a href="{{ route('rh.funcionarios.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">Limpar</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Colaborador</th>
                        <th>Contacto</th>
                        <th>Enquadramento</th>
                        <th>Admissão</th>
                        <th>Estado</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-3">
                                    {{ strtoupper(substr($employee->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $employee->name }}</div>
                                    <small class="text-muted font-monospace">NIF: {{ $employee->nif ?: '-' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($employee->email)
                                <div class="text-dark small"><i class="fas fa-envelope text-muted me-1"></i> {{ $employee->email }}</div>
                            @endif
                            @if($employee->phone)
                                <div class="text-muted small"><i class="fas fa-phone text-muted me-1"></i> {{ $employee->phone }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-primary small">{{ $employee->position ? $employee->position->title : 'Cargo não definido' }}</div>
                            <div class="text-muted small"><i class="fas fa-building me-1"></i> {{ $employee->department ? $employee->department->name : 'Sem Departamento' }}</div>
                        </td>
                        <td>
                            <div class="text-dark">{{ $employee->admission_date ? $employee->admission_date->format('d/m/Y') : '-' }}</div>
                            @if($employee->admission_date)
                                <small class="text-muted">{{ $employee->admission_date->diffForHumans() }}</small>
                            @endif
                        </td>
                        <td>
                            @if($employee->is_active)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="fas fa-check me-1"></i>Ativo</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary"><i class="fas fa-ban me-1"></i>Inativo</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('rh.funcionarios.show', $employee->id) }}" class="btn btn-sm btn-action text-info" title="Ver Detalhes">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('rh.funcionarios.edit', $employee->id) }}" class="btn btn-sm btn-action text-primary" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('rh.funcionarios.destroy', $employee->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-action text-danger" title="Eliminar" onclick="return confirm('Tem certeza que deseja eliminar este colaborador?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-user-slash fa-2x mb-3 d-block"></i>
                            Nenhum colaborador encontrado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($employees->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $employees->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection

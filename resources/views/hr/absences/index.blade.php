@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        background: #ffffff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-plane-departure text-primary me-2"></i>Férias e Ausências</h2>
            <p class="text-muted mt-1">Gestão de pedidos de férias, baixas médicas e faltas.</p>
        </div>
        <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="fas fa-plus me-1"></i> Novo Registo
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card card-premium mb-4">
        <div class="card-body">
            <form action="{{ route('rh.ausencias.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">Pesquisar Funcionário</label>
                    <input type="text" name="search" class="form-control" value="{{ $search }}" placeholder="Nome do colaborador...">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">Estado</label>
                    <select name="status" class="form-select">
                        <option value="">Todos os Estados</option>
                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pendente</option>
                        <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Aprovado</option>
                        <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Rejeitado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100 fw-bold"><i class="fas fa-filter me-2"></i>Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card-premium">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Funcionário</th>
                        <th>Tipo</th>
                        <th>Data Início</th>
                        <th>Data Fim</th>
                        <th>Dias</th>
                        <th>Estado</th>
                        <th>Aprovador</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absences as $abs)
                    @php
                        $days = \Carbon\Carbon::parse($abs->start_date)->diffInDays(\Carbon\Carbon::parse($abs->end_date)) + 1;
                    @endphp
                    <tr>
                        <td class="ps-4 fw-bold text-dark">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold;">
                                    {{ substr($abs->employee->first_name, 0, 1) }}{{ substr($abs->employee->last_name, 0, 1) }}
                                </div>
                                {{ $abs->employee->first_name }} {{ $abs->employee->last_name }}
                            </div>
                        </td>
                        <td>
                            @if($abs->type == 'vacation')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25"><i class="fas fa-umbrella-beach me-1"></i> Férias</span>
                            @elseif($abs->type == 'sick')
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25"><i class="fas fa-stethoscope me-1"></i> Baixa Médica</span>
                            @elseif($abs->type == 'justified')
                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25"><i class="fas fa-check me-1"></i> Falta Justificada</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25"><i class="fas fa-times me-1"></i> Falta Injustificada</span>
                            @endif
                        </td>
                        <td class="fw-medium">{{ \Carbon\Carbon::parse($abs->start_date)->format('d/m/Y') }}</td>
                        <td class="fw-medium">{{ \Carbon\Carbon::parse($abs->end_date)->format('d/m/Y') }}</td>
                        <td class="fw-bold text-primary">{{ $days }} d</td>
                        <td>
                            @if($abs->status == 'approved')
                                <span class="badge bg-success">Aprovado</span>
                            @elseif($abs->status == 'rejected')
                                <span class="badge bg-danger">Rejeitado</span>
                            @else
                                <span class="badge bg-warning text-dark">Pendente</span>
                            @endif
                        </td>
                        <td class="text-muted small">
                            {{ $abs->approver ? $abs->approver->name : '-' }}
                        </td>
                        <td class="text-end pe-4">
                            <button type="button" class="btn btn-sm btn-light border text-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $abs->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('rh.ausencias.destroy', $abs->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem a certeza que deseja eliminar?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light border text-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal{{ $abs->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="{{ route('rh.ausencias.update', $abs->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header bg-light border-bottom-0">
                                        <h5 class="modal-title fw-bold"><i class="fas fa-edit text-primary me-2"></i> Editar Registo / Aprovar</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Funcionário</label>
                                            <input type="text" class="form-control" value="{{ $abs->employee->first_name }} {{ $abs->employee->last_name }}" disabled>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label class="form-label fw-bold small">Tipo de Ausência</label>
                                                <select name="type" class="form-select" required>
                                                    <option value="vacation" {{ $abs->type == 'vacation' ? 'selected' : '' }}>Férias</option>
                                                    <option value="sick" {{ $abs->type == 'sick' ? 'selected' : '' }}>Baixa Médica</option>
                                                    <option value="justified" {{ $abs->type == 'justified' ? 'selected' : '' }}>Falta Justificada</option>
                                                    <option value="unjustified" {{ $abs->type == 'unjustified' ? 'selected' : '' }}>Falta Injustificada</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small">Data de Início</label>
                                                <input type="date" name="start_date" class="form-control" value="{{ \Carbon\Carbon::parse($abs->start_date)->format('Y-m-d') }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small">Data de Fim</label>
                                                <input type="date" name="end_date" class="form-control" value="{{ \Carbon\Carbon::parse($abs->end_date)->format('Y-m-d') }}" required>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label fw-bold small">Estado de Aprovação</label>
                                                <select name="status" class="form-select" required>
                                                    <option value="pending" {{ $abs->status == 'pending' ? 'selected' : '' }}>Pendente</option>
                                                    <option value="approved" {{ $abs->status == 'approved' ? 'selected' : '' }}>Aprovado</option>
                                                    <option value="rejected" {{ $abs->status == 'rejected' ? 'selected' : '' }}>Rejeitado</option>
                                                </select>
                                                <small class="text-muted">A aprovação regista o utilizador logado como aprovador.</small>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label fw-bold small">Motivo / Notas</label>
                                                <textarea name="reason" class="form-control" rows="2">{{ $abs->reason }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light border-top-0">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-primary fw-bold">Guardar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-plane-slash fs-4 mb-2 d-block opacity-50"></i>
                            Nenhum registo de ausência ou férias encontrado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">
            {{ $absences->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('rh.ausencias.store') }}" method="POST">
            @csrf
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white border-bottom-0">
                    <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i> Novo Registo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Funcionário</label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">Selecione o colaborador...</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Tipo de Ausência</label>
                            <select name="type" class="form-select" required>
                                <option value="vacation">Férias</option>
                                <option value="sick">Baixa Médica</option>
                                <option value="justified">Falta Justificada</option>
                                <option value="unjustified">Falta Injustificada</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Data de Início</label>
                            <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Data de Fim</label>
                            <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Motivo / Notas</label>
                            <textarea name="reason" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold">Registar Pedido</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

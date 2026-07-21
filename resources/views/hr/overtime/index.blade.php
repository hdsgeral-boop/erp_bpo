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
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-clock text-primary me-2"></i>Horas Extras</h2>
            <p class="text-muted mt-1">Registo e aprovação de horas extraordinárias para processamento salarial.</p>
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
            <form action="{{ route('rh.horas-extra.index') }}" method="GET" class="row g-3 align-items-end">
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
                        <th>Data</th>
                        <th>Horas</th>
                        <th>Multiplicador</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                        <th>Aprovador</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($overtimes as $ot)
                    <tr>
                        <td class="ps-4 fw-bold text-dark">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold;">
                                    {{ substr($ot->employee->first_name, 0, 1) }}{{ substr($ot->employee->last_name, 0, 1) }}
                                </div>
                                {{ $ot->employee->first_name }} {{ $ot->employee->last_name }}
                            </div>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($ot->date)->format('d/m/Y') }}</td>
                        <td class="fw-bold text-primary">{{ number_format($ot->hours, 1, ',', '.') }} h</td>
                        <td><span class="badge bg-secondary">{{ number_format($ot->multiplier, 1, ',', '.') }}x</span></td>
                        <td class="text-muted small">{{ \Illuminate\Support\Str::limit($ot->reason, 30) }}</td>
                        <td>
                            @if($ot->status == 'approved')
                                <span class="badge bg-success">Aprovado</span>
                            @elseif($ot->status == 'rejected')
                                <span class="badge bg-danger">Rejeitado</span>
                            @else
                                <span class="badge bg-warning text-dark">Pendente</span>
                            @endif
                        </td>
                        <td class="text-muted small">
                            {{ $ot->approver ? $ot->approver->name : '-' }}
                        </td>
                        <td class="text-end pe-4">
                            <button type="button" class="btn btn-sm btn-light border text-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $ot->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('rh.horas-extra.destroy', $ot->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem a certeza que deseja eliminar?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light border text-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal{{ $ot->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="{{ route('rh.horas-extra.update', $ot->id) }}" method="POST">
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
                                            <input type="text" class="form-control" value="{{ $ot->employee->first_name }} {{ $ot->employee->last_name }}" disabled>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label class="form-label fw-bold small">Data</label>
                                                <input type="date" name="date" class="form-control" value="{{ \Carbon\Carbon::parse($ot->date)->format('Y-m-d') }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small">Quantidade (Horas)</label>
                                                <input type="number" step="0.5" name="hours" class="form-control" value="{{ $ot->hours }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small">Multiplicador</label>
                                                <select name="multiplier" class="form-select" required>
                                                    <option value="1.5" {{ $ot->multiplier == 1.5 ? 'selected' : '' }}>1.5x (Dias Úteis)</option>
                                                    <option value="2.0" {{ $ot->multiplier == 2.0 ? 'selected' : '' }}>2.0x (Fim de Semana/Feriado)</option>
                                                </select>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label fw-bold small">Estado de Aprovação</label>
                                                <select name="status" class="form-select" required>
                                                    <option value="pending" {{ $ot->status == 'pending' ? 'selected' : '' }}>Pendente</option>
                                                    <option value="approved" {{ $ot->status == 'approved' ? 'selected' : '' }}>Aprovado</option>
                                                    <option value="rejected" {{ $ot->status == 'rejected' ? 'selected' : '' }}>Rejeitado</option>
                                                </select>
                                                <small class="text-muted">A aprovação regista o utilizador logado como aprovador.</small>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label fw-bold small">Motivo</label>
                                                <textarea name="reason" class="form-control" rows="2">{{ $ot->reason }}</textarea>
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
                            <i class="fas fa-clock fs-4 mb-2 d-block opacity-50"></i>
                            Nenhum registo de horas extras.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">
            {{ $overtimes->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('rh.horas-extra.store') }}" method="POST">
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
                            <label class="form-label fw-bold small">Data</label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Quantidade (Horas)</label>
                            <input type="number" step="0.5" name="hours" class="form-control" value="1.0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Multiplicador</label>
                            <select name="multiplier" class="form-select" required>
                                <option value="1.5" selected>1.5x (Dias Úteis)</option>
                                <option value="2.0">2.0x (Fim de Semana/Feriado)</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Motivo</label>
                            <textarea name="reason" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold">Registar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

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
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-percent text-primary me-2"></i>Taxas Salariais (INSS, Segurança Social)</h2>
            <p class="text-muted mt-1">Parametrização dinâmica das taxas e deduções aplicáveis ao processamento.</p>
        </div>
        <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="fas fa-plus me-1"></i> Nova Taxa
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card-premium">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Nome / Descrição</th>
                        <th>Tipo</th>
                        <th>Taxa Trabalhador (%)</th>
                        <th>Taxa Entidade Patronal (%)</th>
                        <th>Período Validade</th>
                        <th>Estado</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrollTaxes as $tax)
                    <tr>
                        <td class="ps-4 fw-bold text-dark">{{ $tax->name }}</td>
                        <td><span class="badge bg-secondary">{{ $tax->type }}</span></td>
                        <td class="fw-bold text-danger">{{ number_format($tax->employee_rate, 2, ',', '.') }}%</td>
                        <td class="fw-bold text-primary">{{ number_format($tax->employer_rate, 2, ',', '.') }}%</td>
                        <td class="small text-muted">
                            {{ $tax->valid_from->format('d/m/Y') }} 
                            @if($tax->valid_to)
                                - {{ $tax->valid_to->format('d/m/Y') }}
                            @else
                                (Sem fim)
                            @endif
                        </td>
                        <td>
                            @if($tax->is_active)
                                <span class="badge bg-success">Ativa</span>
                            @else
                                <span class="badge bg-secondary">Inativa</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <button type="button" class="btn btn-sm btn-light border text-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $tax->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('rh.taxas-salariais.destroy', $tax->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem a certeza que deseja eliminar?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light border text-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal{{ $tax->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="{{ route('rh.taxas-salariais.update', $tax->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header bg-light border-bottom-0">
                                        <h5 class="modal-title fw-bold"><i class="fas fa-edit text-primary me-2"></i> Editar Taxa</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label class="form-label fw-bold small">Nome</label>
                                                <input type="text" name="name" class="form-control" value="{{ $tax->name }}" required>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label fw-bold small">Tipo (Sigla)</label>
                                                <input type="text" name="type" class="form-control" value="{{ $tax->type }}" placeholder="Ex: INSS" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small">Taxa Trab. (%)</label>
                                                <input type="number" step="0.01" name="employee_rate" class="form-control" value="{{ $tax->employee_rate }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small">Taxa Empr. (%)</label>
                                                <input type="number" step="0.01" name="employer_rate" class="form-control" value="{{ $tax->employer_rate }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small">Válido Desde</label>
                                                <input type="date" name="valid_from" class="form-control" value="{{ $tax->valid_from->format('Y-m-d') }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small">Válido Até</label>
                                                <input type="date" name="valid_to" class="form-control" value="{{ $tax->valid_to ? $tax->valid_to->format('Y-m-d') : '' }}">
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-check form-switch mt-2">
                                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $tax->is_active ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-medium">Taxa Ativa</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light border-top-0">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-primary fw-bold">Guardar Alterações</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-info-circle fs-4 mb-2 d-block"></i>
                            Nenhuma taxa configurada.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">
            {{ $payrollTaxes->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('rh.taxas-salariais.store') }}" method="POST">
            @csrf
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white border-bottom-0">
                    <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i> Nova Taxa Salarial</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Nome</label>
                            <input type="text" name="name" class="form-control" placeholder="Ex: Contribuição Segurança Social" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Tipo (Sigla)</label>
                            <input type="text" name="type" class="form-control" placeholder="Ex: INSS" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Taxa Trabalhador (%)</label>
                            <input type="number" step="0.01" name="employee_rate" class="form-control" value="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Taxa Entidade Patronal (%)</label>
                            <input type="number" step="0.01" name="employer_rate" class="form-control" value="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Válido Desde</label>
                            <input type="date" name="valid_from" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Válido Até</label>
                            <input type="date" name="valid_to" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                                <label class="form-check-label fw-medium">Taxa Ativa</label>
                            </div>
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
@endsection

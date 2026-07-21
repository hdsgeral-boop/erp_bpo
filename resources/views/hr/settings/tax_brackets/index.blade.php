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
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-layer-group text-primary me-2"></i>Escalões de IRT</h2>
            <p class="text-muted mt-1">Parametrização dinâmica das taxas de imposto sobre rendimento do trabalho.</p>
        </div>
        <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="fas fa-plus me-1"></i> Novo Escalão
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
                        <th>Rendimento Mín.</th>
                        <th>Rendimento Máx.</th>
                        <th>Parcela Fixa</th>
                        <th>Taxa (%)</th>
                        <th>Excesso De</th>
                        <th>Estado</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($taxBrackets as $bracket)
                    <tr>
                        <td class="ps-4 fw-bold text-dark">{{ $bracket->name ?? 'Escalão ' . $bracket->id }}</td>
                        <td>{{ number_format($bracket->min_value, 2, ',', '.') }} Kz</td>
                        <td>{{ $bracket->max_value ? number_format($bracket->max_value, 2, ',', '.') . ' Kz' : 'Ilimitado' }}</td>
                        <td>{{ number_format($bracket->fixed_portion, 2, ',', '.') }} Kz</td>
                        <td class="fw-bold text-primary">{{ number_format($bracket->tax_rate, 2, ',', '.') }}%</td>
                        <td>{{ number_format($bracket->excess_of, 2, ',', '.') }} Kz</td>
                        <td>
                            @if($bracket->is_active)
                                <span class="badge bg-success">Ativo</span>
                            @else
                                <span class="badge bg-secondary">Inativo</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <button type="button" class="btn btn-sm btn-light border text-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $bracket->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('rh.escaloes-irt.destroy', $bracket->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem a certeza que deseja eliminar?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light border text-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal{{ $bracket->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <form action="{{ route('rh.escaloes-irt.update', $bracket->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header bg-light border-bottom-0">
                                        <h5 class="modal-title fw-bold"><i class="fas fa-edit text-primary me-2"></i> Editar Escalão</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label class="form-label fw-bold small">Descrição</label>
                                                <input type="text" name="name" class="form-control" value="{{ $bracket->name }}" placeholder="Ex: De 100.001 a 150.000">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small">Rendimento Mínimo (Kz)</label>
                                                <input type="number" step="0.01" name="min_value" class="form-control" value="{{ $bracket->min_value }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small">Rendimento Máximo (Kz)</label>
                                                <input type="number" step="0.01" name="max_value" class="form-control" value="{{ $bracket->max_value }}">
                                                <small class="text-muted">Deixe em branco se ilimitado.</small>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold small">Parcela Fixa (Kz)</label>
                                                <input type="number" step="0.01" name="fixed_portion" class="form-control" value="{{ $bracket->fixed_portion }}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold small">Taxa (%)</label>
                                                <input type="number" step="0.01" name="tax_rate" class="form-control" value="{{ $bracket->tax_rate }}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold small">Excesso De (Kz)</label>
                                                <input type="number" step="0.01" name="excess_of" class="form-control" value="{{ $bracket->excess_of }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small">Válido Desde</label>
                                                <input type="date" name="valid_from" class="form-control" value="{{ $bracket->valid_from->format('Y-m-d') }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small">Válido Até</label>
                                                <input type="date" name="valid_to" class="form-control" value="{{ $bracket->valid_to ? $bracket->valid_to->format('Y-m-d') : '' }}">
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-check form-switch mt-2">
                                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $bracket->is_active ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-medium">Escalão Ativo</label>
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
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-info-circle fs-4 mb-2 d-block"></i>
                            Nenhum escalão configurado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">
            {{ $taxBrackets->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('rh.escaloes-irt.store') }}" method="POST">
            @csrf
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white border-bottom-0">
                    <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i> Novo Escalão</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Descrição</label>
                            <input type="text" name="name" class="form-control" placeholder="Ex: De 100.001 a 150.000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Rendimento Mínimo (Kz)</label>
                            <input type="number" step="0.01" name="min_value" class="form-control" value="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Rendimento Máximo (Kz)</label>
                            <input type="number" step="0.01" name="max_value" class="form-control">
                            <small class="text-muted">Deixe em branco se ilimitado.</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Parcela Fixa (Kz)</label>
                            <input type="number" step="0.01" name="fixed_portion" class="form-control" value="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Taxa (%)</label>
                            <input type="number" step="0.01" name="tax_rate" class="form-control" value="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Excesso De (Kz)</label>
                            <input type="number" step="0.01" name="excess_of" class="form-control" value="0" required>
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
                                <label class="form-check-label fw-medium">Escalão Ativo</label>
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

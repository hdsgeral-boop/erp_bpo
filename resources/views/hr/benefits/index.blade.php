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
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-gift text-primary me-2"></i>Benefícios e Deduções</h2>
            <p class="text-muted mt-1">Gestão de subsídios e descontos específicos por colaborador.</p>
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
            <form action="{{ route('rh.beneficios.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">Pesquisar Funcionário</label>
                    <input type="text" name="search" class="form-control" value="{{ $search }}" placeholder="Nome do colaborador...">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">Tipo</label>
                    <select name="type" class="form-select">
                        <option value="">Todos os Tipos</option>
                        <option value="benefit" {{ $type == 'benefit' ? 'selected' : '' }}>Benefício (Acréscimo)</option>
                        <option value="deduction" {{ $type == 'deduction' ? 'selected' : '' }}>Dedução (Desconto)</option>
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
                        <th>Descrição</th>
                        <th>Valor</th>
                        <th>Tipo</th>
                        <th>Tributável (IRT/INSS)</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($benefits as $ben)
                    <tr>
                        <td class="ps-4 fw-bold text-dark">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold;">
                                    {{ substr($ben->employee->first_name, 0, 1) }}{{ substr($ben->employee->last_name, 0, 1) }}
                                </div>
                                {{ $ben->employee->first_name }} {{ $ben->employee->last_name }}
                            </div>
                        </td>
                        <td>{{ $ben->name }}</td>
                        <td class="fw-bold {{ $ben->type == 'deduction' ? 'text-danger' : 'text-success' }}">
                            {{ number_format($ben->amount, 2, ',', '.') }} {{ $ben->is_percentage ? '%' : 'Kz' }}
                        </td>
                        <td>
                            @if($ben->type == 'deduction')
                                <span class="badge bg-danger"><i class="fas fa-minus me-1"></i> Dedução</span>
                            @else
                                <span class="badge bg-success"><i class="fas fa-plus me-1"></i> Benefício</span>
                            @endif
                        </td>
                        <td>
                            @if($ben->is_taxable)
                                <span class="badge bg-secondary"><i class="fas fa-check text-white me-1"></i> Sim</span>
                            @else
                                <span class="text-muted"><i class="fas fa-times me-1"></i> Não</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <button type="button" class="btn btn-sm btn-light border text-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $ben->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('rh.beneficios.destroy', $ben->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem a certeza que deseja eliminar?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light border text-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal{{ $ben->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="{{ route('rh.beneficios.update', $ben->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header bg-light border-bottom-0">
                                        <h5 class="modal-title fw-bold"><i class="fas fa-edit text-primary me-2"></i> Editar Registo</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Funcionário</label>
                                            <input type="text" class="form-control" value="{{ $ben->employee->first_name }} {{ $ben->employee->last_name }}" disabled>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label class="form-label fw-bold small">Descrição</label>
                                                <input type="text" name="name" class="form-control" value="{{ $ben->name }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small">Natureza</label>
                                                <select name="type" class="form-select" required>
                                                    <option value="benefit" {{ $ben->type == 'benefit' ? 'selected' : '' }}>Acréscimo (+)</option>
                                                    <option value="deduction" {{ $ben->type == 'deduction' ? 'selected' : '' }}>Desconto (-)</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small">Valor</label>
                                                <input type="number" step="0.01" name="amount" class="form-control" value="{{ $ben->amount }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch mt-2">
                                                    <input class="form-check-input" type="checkbox" name="is_percentage" value="1" {{ $ben->is_percentage ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-medium small">É percentagem (%)</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch mt-2">
                                                    <input class="form-check-input" type="checkbox" name="is_taxable" value="1" {{ $ben->is_taxable ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-medium small">Tributável (IRT/INSS)</label>
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
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-gift fs-4 mb-2 d-block opacity-50"></i>
                            Nenhum benefício ou dedução configurado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">
            {{ $benefits->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('rh.beneficios.store') }}" method="POST">
            @csrf
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white border-bottom-0">
                    <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i> Novo Benefício / Dedução</h5>
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
                            <label class="form-label fw-bold small">Descrição</label>
                            <input type="text" name="name" class="form-control" placeholder="Ex: Isenção de Horário, Fundo Pensões" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Natureza</label>
                            <select name="type" class="form-select" required>
                                <option value="benefit" selected>Acréscimo (+)</option>
                                <option value="deduction">Desconto (-)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Valor</label>
                            <input type="number" step="0.01" name="amount" class="form-control" value="0" required>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_percentage" value="1">
                                <label class="form-check-label fw-medium small">É percentagem (%)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_taxable" value="1" checked>
                                <label class="form-check-label fw-medium small">Tributável (IRT/INSS)</label>
                            </div>
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

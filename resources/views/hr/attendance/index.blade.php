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
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-clock text-primary me-2"></i>Ponto Digital e Assiduidade</h2>
            <p class="text-muted mt-1">Registo e acompanhamento de entradas, saídas e estado diário.</p>
        </div>
        <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="fas fa-fingerprint me-1"></i> Registar Ponto Manual
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
            <form action="{{ route('rh.assiduidade.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">Pesquisar Funcionário</label>
                    <input type="text" name="search" class="form-control" value="{{ $search }}" placeholder="Nome do colaborador...">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">Data</label>
                    <input type="date" name="date" class="form-control" value="{{ $date }}">
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
                        <th>Entrada</th>
                        <th>Saída</th>
                        <th>Método</th>
                        <th>Estado</th>
                        <th>Notas</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $att)
                    <tr>
                        <td class="ps-4 fw-bold text-dark">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold;">
                                    {{ substr($att->employee->first_name, 0, 1) }}{{ substr($att->employee->last_name, 0, 1) }}
                                </div>
                                {{ $att->employee->first_name }} {{ $att->employee->last_name }}
                            </div>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($att->date)->format('d/m/Y') }}</td>
                        <td class="fw-bold">{{ $att->clock_in ? \Carbon\Carbon::parse($att->clock_in)->format('H:i') : '--:--' }}</td>
                        <td class="fw-bold">{{ $att->clock_out ? \Carbon\Carbon::parse($att->clock_out)->format('H:i') : '--:--' }}</td>
                        <td>
                            @if($att->type == 'api')
                                <span class="badge bg-info text-dark"><i class="fas fa-microchip me-1"></i>Biométrico</span>
                            @else
                                <span class="badge bg-secondary"><i class="fas fa-desktop me-1"></i>Web</span>
                            @endif
                        </td>
                        <td>
                            @if($att->status == 'present')
                                <span class="badge bg-success">Presente</span>
                            @elseif($att->status == 'absent')
                                <span class="badge bg-danger">Falta</span>
                            @elseif($att->status == 'late')
                                <span class="badge bg-warning text-dark">Atraso</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($att->status) }}</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ \Illuminate\Support\Str::limit($att->notes, 30) }}</td>
                        <td class="text-end pe-4">
                            <button type="button" class="btn btn-sm btn-light border text-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $att->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('rh.assiduidade.destroy', $att->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem a certeza que deseja eliminar este registo?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light border text-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal{{ $att->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="{{ route('rh.assiduidade.update', $att->id) }}" method="POST">
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
                                            <input type="text" class="form-control" value="{{ $att->employee->first_name }} {{ $att->employee->last_name }}" disabled>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small">Hora de Entrada</label>
                                                <input type="time" name="clock_in" class="form-control" value="{{ $att->clock_in ? \Carbon\Carbon::parse($att->clock_in)->format('H:i') : '' }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small">Hora de Saída</label>
                                                <input type="time" name="clock_out" class="form-control" value="{{ $att->clock_out ? \Carbon\Carbon::parse($att->clock_out)->format('H:i') : '' }}">
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label fw-bold small">Estado</label>
                                                <select name="status" class="form-select" required>
                                                    <option value="present" {{ $att->status == 'present' ? 'selected' : '' }}>Presente</option>
                                                    <option value="absent" {{ $att->status == 'absent' ? 'selected' : '' }}>Ausente (Falta)</option>
                                                    <option value="late" {{ $att->status == 'late' ? 'selected' : '' }}>Atraso</option>
                                                    <option value="half-day" {{ $att->status == 'half-day' ? 'selected' : '' }}>Meio-Dia</option>
                                                </select>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label fw-bold small">Notas / Justificação</label>
                                                <textarea name="notes" class="form-control" rows="2">{{ $att->notes }}</textarea>
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
                            <i class="fas fa-fingerprint fs-4 mb-2 d-block opacity-50"></i>
                            Nenhum registo de ponto encontrado para os filtros selecionados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">
            {{ $attendances->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('rh.assiduidade.store') }}" method="POST">
            @csrf
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white border-bottom-0">
                    <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i> Ponto Manual Web</h5>
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
                            <label class="form-label fw-bold small">Hora de Entrada</label>
                            <input type="time" name="clock_in" class="form-control" value="{{ date('H:i') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Hora de Saída</label>
                            <input type="time" name="clock_out" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Estado</label>
                            <select name="status" class="form-select" required>
                                <option value="present" selected>Presente</option>
                                <option value="absent">Ausente (Falta)</option>
                                <option value="late">Atraso</option>
                                <option value="half-day">Meio-Dia</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Notas</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold">Registar Ponto</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

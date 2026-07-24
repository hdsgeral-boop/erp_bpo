@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    .badge-present { background-color: #d1fae5; color: #047857; font-weight: 700; border: 1px solid #a7f3d0; }
    .badge-late { background-color: #fef3c7; color: #b45309; font-weight: 700; border: 1px solid #fde68a; }
    .badge-absent { background-color: #fee2e2; color: #b91c1c; font-weight: 700; border: 1px solid #fca5a5; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-extrabold text-dark mb-1">
                <i class="fas fa-clock text-primary me-2"></i> Controlo de Assiduidade e Ponto
            </h2>
            <p class="text-muted small mb-0">Registo diário de presença, picagens de relógio de ponto e faltas.</p>
        </div>
        <button type="button" class="btn btn-primary fw-bold px-4 py-2" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#createAttendanceModal">
            <i class="fas fa-fingerprint me-2"></i> Registar Ponto Manual
        </button>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Table Card -->
    <div class="card-premium overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">COLABORADOR</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">DATA</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">ENTRADA</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">SAÍDA</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">TOTAL HORAS</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">ESTADO</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold text-end">AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $att)
                    @php
                        $in = $att->clock_in ? \Carbon\Carbon::parse($att->clock_in) : null;
                        $out = $att->clock_out ? \Carbon\Carbon::parse($att->clock_out) : null;
                        $diffText = '-';
                        if ($in && $out) {
                            $mins = $out->diffInMinutes($in);
                            $h = floor($mins / 60);
                            $m = $mins % 60;
                            $diffText = "{$h}h " . str_pad($m, 2, '0', STR_PAD_LEFT) . "m";
                        }
                    @endphp
                    <tr>
                        <td class="py-3 px-4 fw-bold text-dark">{{ $att->employee->name ?? 'Desconhecido' }}</td>
                        <td class="py-3 px-4 text-secondary">{{ \Carbon\Carbon::parse($att->date)->format('d/m/Y') }}</td>
                        <td class="py-3 px-4 fw-bold text-success">{{ $att->clock_in ? \Carbon\Carbon::parse($att->clock_in)->format('H:i') : '-' }}</td>
                        <td class="py-3 px-4 fw-bold text-primary">{{ $att->clock_out ? \Carbon\Carbon::parse($att->clock_out)->format('H:i') : '-' }}</td>
                        <td class="py-3 px-4 fw-bold text-dark">{{ $diffText }}</td>
                        <td class="py-3 px-4">
                            @if(strtolower($att->status) === 'present' || strtolower($att->status) === 'presente')
                                <span class="badge badge-present px-3 py-1 text-uppercase">PRESENTE</span>
                            @elseif(strtolower($att->status) === 'late' || strtolower($att->status) === 'atraso')
                                <span class="badge badge-late px-3 py-1 text-uppercase">ATRASO</span>
                            @else
                                <span class="badge badge-absent px-3 py-1 text-uppercase">AUSENTE</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-end">
                            <form action="{{ route('rh.assiduidade.destroy', $att->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Eliminar este registo de ponto?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger border-0"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-5 text-center text-muted">
                            <i class="fas fa-clock fa-2x mb-3 text-secondary opacity-50 d-block"></i>
                            Nenhum registo de ponto encontrado. Clique em <strong>Registar Ponto Manual</strong> para adicionar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($attendances->hasPages())
        <div class="p-3 border-top">
            {{ $attendances->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Registar Ponto Manual -->
<div class="modal fade" id="createAttendanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-fingerprint text-primary me-2"></i>Registar Ponto Manual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('rh.assiduidade.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Colaborador <span class="text-danger">*</span></label>
                        <select name="employee_id" class="form-select" required style="border-radius: 10px;">
                            <option value="">Selecione o Colaborador...</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->nif ?? 'Sem NIF' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Data <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius: 10px;">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Hora de Entrada</label>
                            <input type="time" name="clock_in" class="form-control" value="08:00" style="border-radius: 10px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Hora de Saída</label>
                            <input type="time" name="clock_out" class="form-control" value="17:00" style="border-radius: 10px;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Estado do Ponto <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required style="border-radius: 10px;">
                            <option value="present" selected>PRESENTE</option>
                            <option value="late">ATRASO</option>
                            <option value="absent">AUSENTE</option>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted">Observações / Nota</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Notas adicionais..." style="border-radius: 10px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal" style="border-radius: 10px;">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4" style="border-radius: 10px;">Salvar Registo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

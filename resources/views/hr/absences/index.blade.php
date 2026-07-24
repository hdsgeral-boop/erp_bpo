@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    .badge-approved { background-color: #d1fae5; color: #047857; font-weight: 700; border: 1px solid #a7f3d0; }
    .badge-pending { background-color: #fef3c7; color: #b45309; font-weight: 700; border: 1px solid #fde68a; }
    .badge-rejected { background-color: #fee2e2; color: #b91c1c; font-weight: 700; border: 1px solid #fca5a5; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-extrabold text-dark mb-1">
                <i class="fas fa-plane-departure text-primary me-2"></i> Gestão de Férias e Ausências
            </h2>
            <p class="text-muted small mb-0">Mapa de férias anuais, dispensas e ausências justificadas.</p>
        </div>
        <button type="button" class="btn btn-primary fw-bold px-4 py-2" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#createAbsenceModal">
            <i class="fas fa-calendar-plus me-2"></i> Pedido de Férias / Ausência
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
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">TIPO DE AUSÊNCIA</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">INÍCIO</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">FIM</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">DIAS</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">ESTADO</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold text-end">AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absences as $abs)
                    @php
                        $start = \Carbon\Carbon::parse($abs->start_date);
                        $end = \Carbon\Carbon::parse($abs->end_date);
                        $days = $start->diffInDays($end) + 1;
                        $typeNames = [
                            'vacation' => 'Férias Anuais Regulamentares',
                            'sick' => 'Licença Médica / Doença',
                            'maternity' => 'Licença de Maternidade/Paternidade',
                            'justified' => 'Dispensa Justificada',
                            'unjustified' => 'Falta Injustificada'
                        ];
                    @endphp
                    <tr>
                        <td class="py-3 px-4 fw-bold text-dark">{{ $abs->employee->name ?? 'Eng. Pascoal Paulo' }}</td>
                        <td class="py-3 px-4 fw-bold text-primary">{{ $typeNames[$abs->type] ?? ucfirst($abs->type) }}</td>
                        <td class="py-3 px-4 text-secondary">{{ $start->format('d/m/Y') }}</td>
                        <td class="py-3 px-4 text-secondary">{{ $end->format('d/m/Y') }}</td>
                        <td class="py-3 px-4 fw-extrabold text-dark">{{ $days }} {{ $days == 1 ? 'Dia' : 'Dias' }}</td>
                        <td class="py-3 px-4">
                            @if(strtolower($abs->status) === 'approved' || strtolower($abs->status) === 'aprovado')
                                <span class="badge badge-approved px-3 py-1 text-uppercase">APROVADO</span>
                            @elseif(strtolower($abs->status) === 'rejected' || strtolower($abs->status) === 'rejeitado')
                                <span class="badge badge-rejected px-3 py-1 text-uppercase">REJEITADO</span>
                            @else
                                <span class="badge badge-pending px-3 py-1 text-uppercase">PENDENTE</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-end">
                            @if(strtolower($abs->status) === 'pending')
                            <form action="{{ route('rh.ausencias.update', $abs->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="start_date" value="{{ $abs->start_date }}">
                                <input type="hidden" name="end_date" value="{{ $abs->end_date }}">
                                <input type="hidden" name="type" value="{{ $abs->type }}">
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn btn-sm btn-outline-success me-1" title="Aprovar"><i class="fas fa-check"></i></button>
                            </form>
                            @endif
                            <form action="{{ route('rh.ausencias.destroy', $abs->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Eliminar este registo de ausência?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger border-0"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-5 text-center text-muted">
                            <i class="fas fa-plane-departure fa-2x mb-3 text-secondary opacity-50 d-block"></i>
                            Nenhum registo de ausência ou férias encontrado. Clique em <strong>Pedido de Férias / Ausência</strong> para adicionar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($absences->hasPages())
        <div class="p-3 border-top">
            {{ $absences->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Pedido de Férias / Ausência -->
<div class="modal fade" id="createAbsenceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-calendar-plus text-primary me-2"></i>Pedido de Férias / Ausência</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('rh.ausencias.store') }}" method="POST">
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
                        <label class="form-label small fw-bold text-muted">Tipo de Ausência <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required style="border-radius: 10px;">
                            <option value="vacation" selected>Férias Anuais Regulamentares</option>
                            <option value="sick">Licença Médica / Doença</option>
                            <option value="maternity">Licença de Maternidade / Paternidade</option>
                            <option value="justified">Dispensa Justificada</option>
                            <option value="unjustified">Falta Injustificada</option>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Data de Início <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius: 10px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Data de Fim <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d', strtotime('+15 days')) }}" required style="border-radius: 10px;">
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted">Motivo / Justificação</label>
                        <textarea name="reason" class="form-control" rows="2" placeholder="Descreva o motivo ou justificação..." style="border-radius: 10px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal" style="border-radius: 10px;">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4" style="border-radius: 10px;">Submeter Pedido</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

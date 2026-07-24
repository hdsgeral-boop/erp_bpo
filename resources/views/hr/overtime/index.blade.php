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
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-extrabold text-dark mb-1">
                <i class="fas fa-stopwatch text-primary me-2"></i> Gestão de Horas Extras
            </h2>
            <p class="text-muted small mb-0">Registo e aprovação de trabalho suplementar (50%, 100% LGT Angola).</p>
        </div>
        <button type="button" class="btn btn-primary fw-bold px-4 py-2" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#createOvertimeModal">
            <i class="fas fa-plus me-2"></i> Registar Horas Extras
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
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">HORAS REALIZADAS</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">TAXA LGT</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">VALOR CALCULADO</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">ESTADO</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold text-end">AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($overtimes as $ot)
                    @php
                        $baseSalary = (float)($ot->employee->base_salary ?? 100000);
                        $hourlyRate = ($baseSalary / 22) / 8;
                        $calcValue = round($hourlyRate * $ot->hours * $ot->multiplier, 2);
                        $rateLabel = $ot->multiplier == 2.0 ? '100% (Descanso/Feriado)' : '50% (Dia Útil)';
                    @endphp
                    <tr>
                        <td class="py-3 px-4 fw-bold text-dark">{{ $ot->employee->name ?? 'Eng. Pascoal Paulo' }}</td>
                        <td class="py-3 px-4 text-secondary">{{ \Carbon\Carbon::parse($ot->date)->format('d/m/Y') }}</td>
                        <td class="py-3 px-4 fw-extrabold text-dark">{{ $ot->hours }} {{ $ot->hours == 1 ? 'Hora' : 'Horas' }}</td>
                        <td class="py-3 px-4 fw-bold text-primary">{{ $rateLabel }}</td>
                        <td class="py-3 px-4 fw-extrabold text-success">{{ number_format($calcValue, 2, ',', '.') }} Kz</td>
                        <td class="py-3 px-4">
                            @if(strtolower($ot->status) === 'approved' || strtolower($ot->status) === 'validado')
                                <span class="badge badge-approved px-3 py-1 text-uppercase">VALIDADO</span>
                            @else
                                <span class="badge badge-pending px-3 py-1 text-uppercase">PENDENTE</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-end">
                            @if(strtolower($ot->status) === 'pending')
                            <form action="{{ route('rh.horas-extra.update', $ot->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="date" value="{{ $ot->date }}">
                                <input type="hidden" name="hours" value="{{ $ot->hours }}">
                                <input type="hidden" name="multiplier" value="{{ $ot->multiplier }}">
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn btn-sm btn-outline-success me-1" title="Validar Horas Extras"><i class="fas fa-check"></i></button>
                            </form>
                            @endif
                            <form action="{{ route('rh.horas-extra.destroy', $ot->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Eliminar este registo de horas extras?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger border-0"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-5 text-center text-muted">
                            <i class="fas fa-stopwatch fa-2x mb-3 text-secondary opacity-50 d-block"></i>
                            Nenhum registo de horas extras encontrado. Clique em <strong>Registar Horas Extras</strong> para adicionar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($overtimes->hasPages())
        <div class="p-3 border-top">
            {{ $overtimes->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Registar Horas Extras -->
<div class="modal fade" id="createOvertimeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-stopwatch text-primary me-2"></i>Registar Horas Extras</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('rh.horas-extra.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Colaborador <span class="text-danger">*</span></label>
                        <select name="employee_id" class="form-select" required style="border-radius: 10px;">
                            <option value="">Selecione o Colaborador...</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->name }} (Vencimento Base: {{ number_format($emp->base_salary ?? 0, 2, ',', '.') }} Kz)</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Data do Trabalho Suplementar <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius: 10px;">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Horas Realizadas <span class="text-danger">*</span></label>
                            <input type="number" step="0.5" name="hours" class="form-control" value="4" required min="0.5" style="border-radius: 10px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Taxa LGT Angola <span class="text-danger">*</span></label>
                            <select name="multiplier" class="form-select" required style="border-radius: 10px;">
                                <option value="1.50" selected>50% (Dia Útil)</option>
                                <option value="2.00">100% (Descanso / Feriado)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted">Motivo / Descrição da Tarefa</label>
                        <textarea name="reason" class="form-control" rows="2" placeholder="Descreva o motivo do trabalho extraordinário..." style="border-radius: 10px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal" style="border-radius: 10px;">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4" style="border-radius: 10px;">Registar Horas</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

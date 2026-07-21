@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        background: #ffffff;
        transition: transform 0.2s ease-in-out;
    }
    .card-premium:hover {
        transform: translateY(-5px);
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-users text-primary me-2"></i>Dashboard de Recursos Humanos</h2>
            <p class="text-muted mt-1">Visão global dos colaboradores, assiduidade e custos salariais.</p>
        </div>
        <div>
            <a href="{{ route('rh.salarios.wizard') }}" class="btn btn-primary fw-bold shadow-sm">
                <i class="fas fa-calculator me-1"></i> Processar Salários
            </a>
        </div>
    </div>

    <!-- Resumo Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card card-premium h-100 p-4 border-bottom border-primary border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1 text-uppercase fw-bold small">Total Colaboradores</p>
                        <h3 class="fw-bold mb-0 text-dark">{{ \App\Models\Employee::where('is_active', true)->count() }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3 text-primary">
                        <i class="fas fa-id-badge fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-premium h-100 p-4 border-bottom border-success border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1 text-uppercase fw-bold small">Presentes Hoje</p>
                        <h3 class="fw-bold mb-0 text-dark">{{ \App\Models\Attendance::where('date', date('Y-m-d'))->where('status', 'present')->count() }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-circle p-3 text-success">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-premium h-100 p-4 border-bottom border-warning border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1 text-uppercase fw-bold small">Férias / Ausências</p>
                        <h3 class="fw-bold mb-0 text-dark">{{ \App\Models\Absence::where('start_date', '<=', date('Y-m-d'))->where('end_date', '>=', date('Y-m-d'))->where('status', 'approved')->count() }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3 text-warning">
                        <i class="fas fa-plane-departure fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-premium h-100 p-4 border-bottom border-info border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1 text-uppercase fw-bold small">Custo Salarial Mês</p>
                        <h3 class="fw-bold mb-0 text-dark">{{ number_format(\App\Models\PayrollReceipt::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->sum('gross_salary'), 2, ',', '.') }} <span class="fs-6">Kz</span></h3>
                    </div>
                    <div class="bg-info bg-opacity-10 rounded-circle p-3 text-info">
                        <i class="fas fa-money-bill-wave fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Atalhos e Tabelas -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card card-premium h-100">
                <div class="card-header bg-transparent border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="fas fa-clock text-primary me-2"></i>Últimos Registos de Ponto</h5>
                    <a href="{{ route('rh.assiduidade.index') }}" class="btn btn-sm btn-light">Ver Todos</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Funcionário</th>
                                    <th>Data</th>
                                    <th>Entrada</th>
                                    <th>Saída</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $recentAttendances = \App\Models\Attendance::with('employee')->orderBy('created_at', 'desc')->take(5)->get();
                                @endphp
                                @forelse($recentAttendances as $att)
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">{{ $att->employee->first_name }} {{ $att->employee->last_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($att->date)->format('d/m/Y') }}</td>
                                    <td>{{ $att->clock_in ? \Carbon\Carbon::parse($att->clock_in)->format('H:i') : '-' }}</td>
                                    <td>{{ $att->clock_out ? \Carbon\Carbon::parse($att->clock_out)->format('H:i') : '-' }}</td>
                                    <td>
                                        @if($att->status == 'present')
                                            <span class="badge bg-success">Presente</span>
                                        @elseif($att->status == 'absent')
                                            <span class="badge bg-danger">Ausente</span>
                                        @else
                                            <span class="badge bg-warning text-dark">{{ ucfirst($att->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Sem registos recentes.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-premium h-100">
                <div class="card-header bg-transparent border-bottom px-4 py-3">
                    <h5 class="fw-bold mb-0"><i class="fas fa-calendar-check text-primary me-2"></i>Ausências Pendentes</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @php
                            $pendingAbsences = \App\Models\Absence::with('employee')->where('status', 'pending')->take(5)->get();
                        @endphp
                        @forelse($pendingAbsences as $abs)
                        <div class="list-group-item px-4 py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold mb-1">{{ $abs->employee->first_name }} {{ $abs->employee->last_name }}</h6>
                                <p class="text-muted mb-0 small"><i class="fas fa-plane text-warning me-1"></i>{{ \Carbon\Carbon::parse($abs->start_date)->format('d/m') }} - {{ \Carbon\Carbon::parse($abs->end_date)->format('d/m') }} ({{ ucfirst($abs->type) }})</p>
                            </div>
                            <a href="{{ route('rh.ausencias.index') }}" class="btn btn-sm btn-light text-primary border"><i class="fas fa-arrow-right"></i></a>
                        </div>
                        @empty
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-check-double fs-3 mb-2 text-success opacity-50 d-block"></i>
                            Não há pedidos pendentes.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

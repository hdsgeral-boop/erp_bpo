@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    .badge-processed { background-color: #d1fae5; color: #047857; font-weight: 700; border: 1px solid #a7f3d0; }
    .badge-reversed { background-color: #fee2e2; color: #b91c1c; font-weight: 700; border: 1px solid #fca5a5; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-extrabold text-dark mb-1">
                <i class="fas fa-calculator text-primary me-2"></i> Histórico de Processamentos
            </h2>
            <p class="text-muted small mb-0">Consulte os processamentos salariais fechados e emita recibos.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('rh.salarios.simulation') }}" class="btn btn-outline-primary fw-bold px-3 py-2" style="border-radius: 10px;">
                <i class="fas fa-play-circle me-1"></i> Simular Folha
            </a>
            <button type="button" class="btn btn-primary fw-bold px-4 py-2" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#createPayrollModal">
                <i class="fas fa-play me-2"></i> Novo Processamento
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Table Card -->
    <div class="card-premium overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">REFERÊNCIA</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">PERÍODO</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">TOTAL ILÍQUIDO</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">TOTAL INSS</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">TOTAL IRT</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">LÍQUIDO A PAGAR</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">ESTADO</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold text-end">AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($runs as $run)
                    @php
                        $monthsPt = [
                            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
                            5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
                            9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
                        ];
                        $monthName = $monthsPt[(int)$run->month] ?? $run->month;
                    @endphp
                    <tr>
                        <td class="py-3 px-4 fw-extrabold text-primary">{{ $run->reference }} <span class="badge bg-light text-muted border">v{{ $run->version }}</span></td>
                        <td class="py-3 px-4 fw-bold text-dark">{{ $monthName }} / {{ $run->year }}</td>
                        <td class="py-3 px-4 fw-bold text-dark">{{ number_format($run->total_additions, 2, ',', '.') }} Kz</td>
                        <td class="py-3 px-4 fw-bold text-warning-emphasis">{{ number_format($run->total_inss, 2, ',', '.') }} Kz</td>
                        <td class="py-3 px-4 fw-bold text-danger">{{ number_format($run->total_irt, 2, ',', '.') }} Kz</td>
                        <td class="py-3 px-4 fw-extrabold text-success fs-6">{{ number_format($run->total_net_paid, 2, ',', '.') }} Kz</td>
                        <td class="py-3 px-4">
                            @if(!$run->is_reversed && (strtolower($run->status) === 'processed' || strtolower($run->status) === 'fechado'))
                                <span class="badge badge-processed px-3 py-1 text-uppercase">FECHADO</span>
                            @else
                                <span class="badge badge-reversed px-3 py-1 text-uppercase">ESTORNADO</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-end">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle fw-bold" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v me-1"></i> Opções
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                    <li><a class="dropdown-item py-2" href="{{ route('rh.salarios.recibo', $run->id) }}" target="_blank"><i class="fas fa-file-pdf text-danger me-2"></i> Recibos PDF</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('rh.reports.inss', ['run_id' => $run->id]) }}"><i class="fas fa-shield-alt text-primary me-2"></i> Mapa de INSS</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('rh.reports.bank', ['run_id' => $run->id]) }}"><i class="fas fa-university text-success me-2"></i> Ficheiro Bancário PS2</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-5 text-center text-muted">
                            <i class="fas fa-receipt fa-2x mb-3 text-secondary opacity-50 d-block"></i>
                            Nenhum processamento salarial efetuado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($runs->hasPages())
        <div class="p-3 border-top">
            {{ $runs->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Novo Processamento Salarial -->
<div class="modal fade" id="createPayrollModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-calculator text-primary me-2"></i>Novo Processamento Salarial</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('rh.salarios.process') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Mês de Processamento <span class="text-danger">*</span></label>
                            <select name="month" class="form-select" required style="border-radius: 10px;">
                                @php
                                    $currMonth = (int)date('m');
                                    $monthsList = [
                                        1 => '01 - Janeiro', 2 => '02 - Fevereiro', 3 => '03 - Março', 4 => '04 - Abril',
                                        5 => '05 - Maio', 6 => '06 - Junho', 7 => '07 - Julho', 8 => '08 - Agosto',
                                        9 => '09 - Setembro', 10 => '10 - Outubro', 11 => '11 - Novembro', 12 => '12 - Dezembro'
                                    ];
                                @endphp
                                @foreach($monthsList as $num => $label)
                                    <option value="{{ $num }}" {{ $num == $currMonth ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Ano <span class="text-danger">*</span></label>
                            <input type="number" name="year" class="form-control" value="{{ date('Y') }}" required min="2020" style="border-radius: 10px;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted d-flex justify-content-between">
                            <span>Colaboradores Abrangidos <span class="text-danger">*</span></span>
                            <span class="text-primary fw-normal" style="cursor: pointer;" onclick="document.querySelectorAll('.emp-check').forEach(c => c.checked = true);">Selecionar Todos</span>
                        </label>
                        <div class="p-3 border rounded bg-light" style="max-height: 200px; overflow-y: auto; border-radius: 10px;">
                            @foreach($employees as $emp)
                                <div class="form-check mb-2">
                                    <input class="form-check-input emp-check" type="checkbox" name="employee_ids[]" value="{{ $emp->id }}" id="emp_{{ $emp->id }}" checked>
                                    <label class="form-check-label small fw-bold text-dark" for="emp_{{ $emp->id }}">
                                        {{ $emp->name }} <span class="text-muted font-monospace font-normal">({{ number_format($emp->base_salary ?? 0, 2, ',', '.') }} Kz)</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="alert alert-info small rounded-3 mb-0">
                        <i class="fas fa-info-circle me-1"></i> Ao confirmar, a folha será calculada com o IRT (2026), INSS (3%/8%), e integrada na Tesouraria e Contabilidade.
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal" style="border-radius: 10px;">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4" style="border-radius: 10px;" onclick="return confirm('Confirmar o fecho da folha de pagamento?');">
                        <i class="fas fa-check-circle me-2"></i> Calcular & Fechar Folha
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

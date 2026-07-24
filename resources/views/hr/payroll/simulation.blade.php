@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        background: #ffffff;
    }
    .wizard-step {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        font-weight: bold;
    }
    .wizard-step.active {
        background-color: #0d6efd;
        color: white;
    }
    .wizard-step.completed {
        background-color: #198754;
        color: white;
    }
    .wizard-step.pending {
        background-color: #e9ecef;
        color: #6c757d;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-play-circle text-primary me-2"></i>Processar Salários</h2>
            <p class="text-muted mt-1">Siga os passos para simular e fechar o processamento mensal.</p>
        </div>
    </div>

    <div class="card card-premium mb-4">
        <div class="card-body p-5">
            
            <div class="d-flex justify-content-center mb-5 position-relative">
                <div class="position-absolute top-50 translate-middle w-50 bg-light" style="left: 50%; height: 4px; z-index: 0;"></div>
                <div class="d-flex justify-content-between position-relative w-50" style="z-index: 1;">
                    <div class="text-center bg-white px-2">
                        <div class="wizard-step completed mx-auto mb-2 shadow-sm"><i class="fas fa-check"></i></div>
                        <span class="fw-bold text-success small">Parâmetros</span>
                    </div>
                    <div class="text-center bg-white px-2">
                        <div class="wizard-step active mx-auto mb-2 shadow-sm">2</div>
                        <span class="fw-bold text-primary small">Simulação</span>
                    </div>
                    <div class="text-center bg-white px-2">
                        <div class="wizard-step pending mx-auto mb-2">3</div>
                        <span class="fw-bold text-muted small">Fecho</span>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <h5 class="fw-bold border-bottom pb-2 mb-3">Resultados da Simulação: <span class="text-primary">{{ $reference }}</span></h5>
                </div>
            </div>

            <!-- Resumo Totais -->
            <div class="row g-3 mb-4">
                <div class="col-md-2">
                    <div class="p-3 border rounded bg-light text-center">
                        <small class="d-block text-muted fw-bold">Total Vencimento Base</small>
                        <span class="fs-5 fw-bold text-dark">{{ number_format($totals['base'], 2, ',', '.') }}</span>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="p-3 border rounded bg-light text-center border-success">
                        <small class="d-block text-muted fw-bold">+ Total Benefícios/Extras</small>
                        <span class="fs-5 fw-bold text-success">{{ number_format($totals['additions'], 2, ',', '.') }}</span>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="p-3 border rounded bg-light text-center border-danger">
                        <small class="d-block text-muted fw-bold">- Total Descontos/Faltas</small>
                        <span class="fs-5 fw-bold text-danger">{{ number_format($totals['deductions'], 2, ',', '.') }}</span>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="p-3 border rounded bg-light text-center border-warning">
                        <small class="d-block text-muted fw-bold">Total INSS (Empresa+Trab)</small>
                        <span class="fs-5 fw-bold text-warning text-dark">{{ number_format($totals['inss_company'] + $totals['inss_employee'], 2, ',', '.') }}</span>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="p-3 border rounded bg-light text-center border-danger">
                        <small class="d-block text-muted fw-bold">Total IRT (Estado)</small>
                        <span class="fs-5 fw-bold text-danger">{{ number_format($totals['irt'], 2, ',', '.') }}</span>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="p-3 border rounded bg-primary text-white text-center shadow-sm">
                        <small class="d-block fw-bold opacity-75">Total Líquido a Pagar</small>
                        <span class="fs-5 fw-bold">{{ number_format($totals['net'], 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered table-hover align-middle" style="font-size: 0.85rem;">
                    <thead class="table-light text-center">
                        <tr>
                            <th class="text-start">Funcionário</th>
                            <th>Vencimento Base</th>
                            <th>Benefícios/Extras</th>
                            <th>Deduções/Faltas</th>
                            <th>Base INSS</th>
                            <th>INSS Trabalhador</th>
                            <th>IRT</th>
                            <th class="bg-primary text-white">Líquido a Receber</th>
                        </tr>
                    </thead>
                    <tbody class="text-end">
                        @foreach($results as $res)
                        <tr>
                            <td class="text-start fw-bold">{{ is_array($res['employee']) ? ($res['employee']['name'] ?? (($res['employee']['first_name'] ?? '') . ' ' . ($res['employee']['last_name'] ?? ''))) : ($res['employee']->name ?? 'Colaborador') }}</td>
                            <td>{{ number_format($res['base_salary'] ?? 0, 2, ',', '.') }}</td>
                            <td class="text-success">+ {{ number_format($res['additions'] ?? 0, 2, ',', '.') }}</td>
                            <td class="text-danger">- {{ number_format($res['other_deductions'] ?? $res['deductions'] ?? 0, 2, ',', '.') }}</td>
                            <td class="text-muted">{{ number_format($res['inss_base'] ?? 0, 2, ',', '.') }}</td>
                            <td class="text-danger">- {{ number_format($res['inss_employee'] ?? 0, 2, ',', '.') }}</td>
                            <td class="text-danger">- {{ number_format($res['irt'] ?? 0, 2, ',', '.') }}</td>
                            <td class="fw-bold fs-6">{{ number_format($res['net_total'] ?? $res['net_salary'] ?? 0, 2, ',', '.') }} Kz</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <a href="{{ route('rh.salarios.wizard') }}" class="btn btn-light border fw-bold text-muted">
                    <i class="fas fa-arrow-left me-1"></i> Voltar aos Parâmetros
                </a>
                
                <form action="{{ route('rh.salarios.process') }}" method="POST">
                    @csrf
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    @foreach($employees as $emp)
                        <input type="hidden" name="employee_ids[]" value="{{ is_array($emp) ? ($emp['id'] ?? $emp['employee_id'] ?? 1) : $emp->id }}">
                    @endforeach
                    <button type="submit" class="btn btn-success fw-bold px-4 py-2" onclick="return confirm('Ao fechar, serão gerados documentos na Tesouraria e lançamentos na Contabilidade. Confirma o fecho do processamento?');">
                        <i class="fas fa-check-circle me-2"></i> Fechar Processamento
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection

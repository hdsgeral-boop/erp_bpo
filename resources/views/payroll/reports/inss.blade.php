@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    @media print {
        .no-print { display: none !important; }
        .card-premium { border: none !important; box-shadow: none !important; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom no-print">
        <div>
            <h2 class="fw-extrabold text-dark mb-1">
                <i class="fas fa-shield-alt text-primary me-2"></i> Mapa de Contribuições à Segurança Social (INSS)
            </h2>
            <p class="text-muted small mb-0">Declaração de Remunerações Sujeitas (3% Trabalhador + 8% Entidade Empregadora).</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary fw-bold px-3 py-2" onclick="window.print()" style="border-radius: 10px;">
                <i class="fas fa-print me-1"></i> Imprimir Mapa
            </button>
            @if($run)
                <a href="{{ route('rh.salarios.export_inss', $run->id) }}" class="btn btn-success fw-bold px-3 py-2" style="border-radius: 10px;">
                    <i class="fas fa-file-excel me-1"></i> Exportar Excel INSS
                </a>
                <a href="{{ route('rh.salarios.export_agt', $run->id) }}" class="btn btn-primary fw-bold px-3 py-2" style="border-radius: 10px;">
                    <i class="fas fa-file-export me-1"></i> Exportar Excel IRT (AGT)
                </a>
            @endif
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card card-body border-0 shadow-sm rounded-4 mb-4 bg-white no-print">
        <form method="GET" action="{{ route('rh.reports.inss') }}" class="row g-3 align-items-center">
            <div class="col-md-6 col-lg-5">
                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-calendar-alt text-primary me-1"></i> Período de Processamento Salarial</label>
                <select name="run_id" class="form-select fw-bold border-secondary-subtle" onchange="this.form.submit()" style="border-radius: 10px;">
                    @forelse($runs as $r)
                        <option value="{{ $r->id }}" {{ $run && $run->id == $r->id ? 'selected' : '' }}>
                            Mês {{ $r->reference }} (v{{ $r->version }}) — Total INSS (11%): {{ number_format($r->total_inss, 2, ',', '.') }} Kz
                        </option>
                    @empty
                        <option value="">Nenhum processamento salarial fechado disponível</option>
                    @endforelse
                </select>
            </div>
            @if($run)
            <div class="col-md-6 col-lg-7 d-flex align-items-end justify-content-md-end gap-2 pt-2 pt-md-0">
                <span class="badge bg-primary-subtle text-primary border px-3 py-2 fw-bold" style="border-radius: 8px;">
                    <i class="fas fa-users me-1"></i> {{ $receipts->count() }} Colaboradores Processados
                </span>
                <span class="badge bg-success-subtle text-success border px-3 py-2 fw-bold" style="border-radius: 8px;">
                    <i class="fas fa-check-circle me-1"></i> Estado: FECHADO (v{{ $run->version }})
                </span>
            </div>
            @endif
        </form>
    </div>

    @if(!$run)
        <div class="alert alert-info rounded-4 p-4 text-center">
            <i class="fas fa-info-circle fa-2x mb-2 text-primary d-block"></i>
            Nenhum processamento salarial selecionado. Por favor efetue um processamento salarial primeiro.
        </div>
    @else
        <!-- Print Header Title -->
        <div class="d-none d-print-block mb-4 text-center">
            <h3 class="fw-bold mb-1">DECLARAÇÃO DE CONTRIBUIÇÕES À SEGURANÇA SOCIAL (INSS)</h3>
            <p class="mb-0">Período: {{ $run->reference }} | Taxa: 3% Trabalhador + 8% Entidade Empregadora</p>
        </div>

        <!-- Table Card -->
        <div class="card-premium overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-4 text-muted small text-uppercase fw-bold">#</th>
                            <th class="py-3 px-4 text-muted small text-uppercase fw-bold">Nº SEG. SOCIAL</th>
                            <th class="py-3 px-4 text-muted small text-uppercase fw-bold">COLABORADOR</th>
                            <th class="py-3 px-4 text-muted small text-uppercase fw-bold text-end">REMUNERAÇÃO ILÍQUIDA</th>
                            <th class="py-3 px-4 text-muted small text-uppercase fw-bold text-end">BASE INSS</th>
                            <th class="py-3 px-4 text-muted small text-uppercase fw-bold text-end text-primary">INSS TRABALHADOR (3%)</th>
                            <th class="py-3 px-4 text-muted small text-uppercase fw-bold text-end text-warning-emphasis">INSS EMPRESA (8%)</th>
                            <th class="py-3 px-4 text-muted small text-uppercase fw-bold text-end text-dark">TOTAL INSS (11%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totGross = 0;
                            $totBase = 0;
                            $totEmp = 0;
                            $totComp = 0;
                            $totGlobal = 0;
                        @endphp
                        @forelse($receipts as $idx => $rec)
                            @php
                                $gross = $rec->base_salary + $rec->other_additions;
                                $inssEmp = $rec->inss_employee ?? 0;
                                $inssComp = $rec->inss_company ?? ($rec->inss_base * 0.08);
                                $base = $rec->inss_base ?? $rec->base_salary;
                                $totalInssLine = $inssEmp + $inssComp;

                                $totGross += $gross;
                                $totBase += $base;
                                $totEmp += $inssEmp;
                                $totComp += $inssComp;
                                $totGlobal += $totalInssLine;
                            @endphp
                            <tr>
                                <td class="py-3 px-4 text-muted small">{{ $idx + 1 }}</td>
                                <td class="py-3 px-4 fw-extrabold text-primary font-monospace">{{ $rec->employee->inss ?? 'N/A' }}</td>
                                <td class="py-3 px-4">
                                    <div class="fw-bold text-dark">{{ $rec->employee->name }}</div>
                                    <div class="small text-muted font-monospace">NIF: {{ $rec->employee->nif ?? 'N/A' }}</div>
                                </td>
                                <td class="py-3 px-4 text-end fw-bold text-dark">{{ number_format($gross, 2, ',', '.') }} Kz</td>
                                <td class="py-3 px-4 text-end fw-extrabold text-dark">{{ number_format($base, 2, ',', '.') }} Kz</td>
                                <td class="py-3 px-4 text-end text-primary fw-bold">{{ number_format($inssEmp, 2, ',', '.') }} Kz</td>
                                <td class="py-3 px-4 text-end text-warning-emphasis fw-bold">{{ number_format($inssComp, 2, ',', '.') }} Kz</td>
                                <td class="py-3 px-4 text-end fw-extrabold text-success fs-6">{{ number_format($totalInssLine, 2, ',', '.') }} Kz</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-5">Nenhum recibo salarial para apresentar.</td></tr>
                        @endforelse
                    </tbody>
                    @if($receipts->count() > 0)
                        <tfoot class="bg-light fw-extrabold border-top">
                            <tr>
                                <td colspan="3" class="py-3 px-4 text-uppercase text-dark">TOTAL DAS CONTRIBUIÇÕES INSS</td>
                                <td class="py-3 px-4 text-end text-dark">{{ number_format($totGross, 2, ',', '.') }} Kz</td>
                                <td class="py-3 px-4 text-end text-dark">{{ number_format($totBase, 2, ',', '.') }} Kz</td>
                                <td class="py-3 px-4 text-end text-primary fs-6">{{ number_format($totEmp, 2, ',', '.') }} Kz</td>
                                <td class="py-3 px-4 text-end text-warning-emphasis fs-6">{{ number_format($totComp, 2, ',', '.') }} Kz</td>
                                <td class="py-3 px-4 text-end text-success fs-5">{{ number_format($totGlobal, 2, ',', '.') }} Kz</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h2 class="fw-bold text-dark mb-1"><i class="fas fa-shield-alt text-primary me-2"></i>Mapa de Contribuições à Segurança Social (INSS)</h2>
            <p class="text-muted mb-0 small">Declaração de Remunerações Sujeitas (3% Trabalhador + 8% Entidade Empregadora).</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm rounded-3" onclick="window.print()"><i class="fas fa-print me-1"></i> Imprimir</button>
            @if($run)
                <a href="{{ route('rh.salarios.export_inss', $run->id) }}" class="btn btn-success btn-sm rounded-3"><i class="fas fa-file-excel me-1"></i> Exportar Folha INSS</a>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-light">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('rh.reports.inss') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted">Período de Processamento</label>
                    <select name="run_id" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                        @forelse($runs as $r)
                            <option value="{{ $r->id }}" {{ $run && $run->id == $r->id ? 'selected' : '' }}>
                                Mês {{ $r->reference }} (v{{ $r->version }}) - {{ number_format($r->total_net, 2, ',', '.') }} Kz
                            </option>
                        @empty
                            <option value="">Sem processamentos disponíveis</option>
                        @endforelse
                    </select>
                </div>
            </form>
        </div>
    </div>

    @if(!$run)
        <div class="alert alert-info rounded-4">Nenhum processamento salarial selecionado.</div>
    @else
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nº Seg. Social</th>
                                <th>Colaborador</th>
                                <th class="text-end">Remuneração Ilíquida</th>
                                <th class="text-end">Base INSS</th>
                                <th class="text-end text-primary">INSS Trabalhador (3%)</th>
                                <th class="text-end text-warning">INSS Empresa (8%)</th>
                                <th class="text-end fw-bold text-dark">Total INSS (11%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totBase = 0;
                                $totEmp = 0;
                                $totComp = 0;
                                $totGlobal = 0;
                            @endphp
                            @forelse($receipts as $idx => $rec)
                                @php
                                    $inssEmp = $rec->inss_employee ?? 0;
                                    $inssComp = $rec->inss_company ?? 0;
                                    $base = $rec->inss_base ?? $rec->base_salary;
                                    $totalInssLine = $inssEmp + $inssComp;

                                    $totBase += $base;
                                    $totEmp += $inssEmp;
                                    $totComp += $inssComp;
                                    $totGlobal += $totalInssLine;
                                @endphp
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td class="fw-bold">{{ $rec->employee->social_security_number ?? '-' }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $rec->employee->full_name }}</div>
                                        <div class="small text-muted">NIF: {{ $rec->employee->tax_id ?? '-' }}</div>
                                    </td>
                                    <td class="text-end">{{ number_format($rec->base_salary, 2, ',', '.') }} Kz</td>
                                    <td class="text-end fw-bold">{{ number_format($base, 2, ',', '.') }} Kz</td>
                                    <td class="text-end text-primary fw-bold">{{ number_format($inssEmp, 2, ',', '.') }} Kz</td>
                                    <td class="text-end text-warning fw-bold">{{ number_format($inssComp, 2, ',', '.') }} Kz</td>
                                    <td class="text-end fw-bold text-dark">{{ number_format($totalInssLine, 2, ',', '.') }} Kz</td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center text-muted py-4">Sem recibos para apresentar.</td></tr>
                            @endforelse
                        </tbody>
                        @if($receipts->count() > 0)
                            <tfoot class="table-group-divider bg-light fw-bold">
                                <tr>
                                    <td colspan="4">TOTAL DAS CONTRIBUIÇÕES INSS</td>
                                    <td class="text-end">{{ number_format($totBase, 2, ',', '.') }} Kz</td>
                                    <td class="text-end text-primary">{{ number_format($totEmp, 2, ',', '.') }} Kz</td>
                                    <td class="text-end text-warning">{{ number_format($totComp, 2, ',', '.') }} Kz</td>
                                    <td class="text-end text-dark fs-6">{{ number_format($totGlobal, 2, ',', '.') }} Kz</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

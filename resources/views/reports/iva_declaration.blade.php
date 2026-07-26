@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="h4 font-weight-bold text-gray-800 mb-1">
                <i class="fas fa-file-invoice-dollar text-success me-2"></i> Declaração Periódica do IVA (AGT)
            </h3>
            <p class="text-muted small mb-0">Apuramento oficial do imposto organizado pelos quadros regulamentares da Administração Geral Tributária</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-dark btn-sm px-3">
                <i class="fas fa-print me-1"></i> Imprimir Declaração (PDF)
            </button>
        </div>
    </div>

    <!-- Filtros de Período -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('reports.iva.declaration') }}" class="row g-3 align-items-center">
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold mb-1">Ano Fiscal</label>
                    <select name="year" class="form-select form-select-sm rounded-3">
                        @for($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold mb-1">Periodicidade</label>
                    <select name="period_type" id="periodTypeSelect" onchange="togglePeriodFields()" class="form-select form-select-sm rounded-3">
                        <option value="MONTHLY" {{ $periodType == 'MONTHLY' ? 'selected' : '' }}>Mensal</option>
                        <option value="QUARTERLY" {{ $periodType == 'QUARTERLY' ? 'selected' : '' }}>Trimestral</option>
                        <option value="ANNUAL" {{ $periodType == 'ANNUAL' ? 'selected' : '' }}>Anual</option>
                    </select>
                </div>
                <div class="col-md-3" id="monthField" style="{{ $periodType == 'MONTHLY' ? '' : 'display:none;' }}">
                    <label class="form-label text-muted small fw-bold mb-1">Mês</label>
                    <select name="month" class="form-select form-select-sm rounded-3">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 10)) }} ({{ str_pad($m, 2, '0', STR_PAD_LEFT) }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3" id="quarterField" style="{{ $periodType == 'QUARTERLY' ? '' : 'display:none;' }}">
                    <label class="form-label text-muted small fw-bold mb-1">Trimestre</label>
                    <select name="quarter" class="form-select form-select-sm rounded-3">
                        <option value="1" {{ $quarter == 1 ? 'selected' : '' }}>1.º Trimestre (Jan - Mar)</option>
                        <option value="2" {{ $quarter == 2 ? 'selected' : '' }}>2.º Trimestre (Abr - Jun)</option>
                        <option value="3" {{ $quarter == 3 ? 'selected' : '' }}>3.º Trimestre (Jul - Set)</option>
                        <option value="4" {{ $quarter == 4 ? 'selected' : '' }}>4.º Trimestre (Out - Dez)</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-dark btn-sm w-100 rounded-3">
                        <i class="fas fa-calculator me-1"></i> Apurar Período
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cabeçalho do Mapa Fiscal -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-dark text-white p-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="fw-bold mb-1">{{ $company->name }}</h5>
                <div class="small opacity-75">NIF: <span class="fw-bold text-warning">{{ $company->nif ?? '999999999' }}</span> | Período de Apuramento: <span class="fw-bold text-white">{{ $periodLabel }}</span> ({{ $startDate }} a {{ $endDate }})</div>
            </div>
            <div class="col-md-4 text-end">
                <span class="badge bg-warning text-dark px-3 py-2 fs-6">Declaração Periódica AGT</span>
            </div>
        </div>
    </div>

    <!-- QUADRO 06: Operações Tributadas -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-light py-3 border-0">
            <h6 class="fw-bold mb-0 text-primary">QUADRO 06 — Operações Tributadas (Vendas e Prestação de Serviços)</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light text-muted small">
                        <tr>
                            <th class="ps-4">Campo AGT</th>
                            <th>Descrição da Operação</th>
                            <th class="text-end">Base Tributável / Incidência (Kz)</th>
                            <th class="text-center">Taxa</th>
                            <th class="text-end pe-4">Imposto Liquidado (Kz)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-4 font-monospace fw-bold text-primary">Campo 101</td>
                            <td>Transmissões de bens e prestações de serviços sujeitas à taxa normal (14%)</td>
                            <td class="text-end font-monospace">{{ number_format($taxableBase14, 2, ',', '.') }} Kz</td>
                            <td class="text-center"><span class="badge bg-primary">14%</span></td>
                            <td class="text-end pe-4 font-monospace fw-bold text-primary">{{ number_format($taxAmount14, 2, ',', '.') }} Kz</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- QUADRO 07: Operações Isentas (M01 - M99) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-light py-3 border-0 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">QUADRO 07 — Operações Isentas ou Não Sujeitas (Discriminado por Motivo AGT)</h6>
            <span class="badge bg-secondary">M01 a M99</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light text-muted small">
                        <tr>
                            <th class="ps-4">Código Motivo</th>
                            <th>Descrição Legal da Isenção (CIVA AGT)</th>
                            <th class="text-end pe-4">Valor Total Isento (Kz)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($exemptionsByReason as $code => $info)
                            <tr class="{{ $info['total'] > 0 ? 'bg-light-subtle' : '' }}">
                                <td class="ps-4 font-monospace fw-bold text-danger">{{ $code }}</td>
                                <td>{{ $info['description'] }}</td>
                                <td class="text-end pe-4 font-monospace fw-bold {{ $info['total'] > 0 ? 'text-dark' : 'text-muted' }}">{{ number_format($info['total'], 2, ',', '.') }} Kz</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light fw-bold">
                        <tr>
                            <td colspan="2" class="ps-4 text-uppercase">Total de Vendas Isentas no Período</td>
                            <td class="text-end pe-4 font-monospace text-dark">{{ number_format($totalExemptAmount, 2, ',', '.') }} Kz</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- QUADRO 08 & 09: IVA Dedutível e Apuramento Final -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-light py-3 border-0">
                    <h6 class="fw-bold mb-0 text-info">QUADRO 08 — IVA Suportado em Compras (Dedutível)</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Total de Compras / Despesas (Base):</span>
                        <span class="fw-bold font-monospace">{{ number_format($purchaseTaxableBase, 2, ',', '.') }} Kz</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3">
                        <span class="fw-bold text-info">Total IVA Dedutível (Campo 200):</span>
                        <span class="h5 font-weight-bold font-monospace text-info mb-0">{{ number_format($deductibleTaxAmount, 2, ',', '.') }} Kz</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-light py-3 border-0">
                    <h6 class="fw-bold mb-0 text-dark">QUADRO 09 — Apuramento Final do Imposto</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Total IVA Liquidado (Quadro 06):</span>
                        <span class="fw-bold font-monospace text-primary">{{ number_format($taxAmount14, 2, ',', '.') }} Kz</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">(-) Total IVA Dedutível (Quadro 08):</span>
                        <span class="fw-bold font-monospace text-info">- {{ number_format($deductibleTaxAmount, 2, ',', '.') }} Kz</span>
                    </div>
                    <hr>
                    @if($netTaxPayable > 0)
                        <div class="d-flex justify-content-between align-items-center p-3 bg-danger text-white rounded-3">
                            <span class="fw-bold">IMPOSTO A PAGAR À AGT (Campo 300):</span>
                            <span class="h4 font-weight-bold font-monospace mb-0">{{ number_format($netTaxPayable, 2, ',', '.') }} Kz</span>
                        </div>
                    @else
                        <div class="d-flex justify-content-between align-items-center p-3 bg-success text-white rounded-3">
                            <span class="fw-bold">CRÉDITO DE IMPOSTO A RECUPERAR (Campo 301):</span>
                            <span class="h4 font-weight-bold font-monospace mb-0">{{ number_format($taxCreditToRecover, 2, ',', '.') }} Kz</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePeriodFields() {
    const val = document.getElementById('periodTypeSelect').value;
    document.getElementById('monthField').style.display = val === 'MONTHLY' ? 'block' : 'none';
    document.getElementById('quarterField').style.display = val === 'QUARTERLY' ? 'block' : 'none';
}
</script>
@endsection

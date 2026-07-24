@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h2 class="fw-extrabold text-dark mb-1">
                <i class="fas fa-university text-success me-2"></i> Mapa de Pagamentos Bancários (Salários)
            </h2>
            <p class="text-muted small mb-0">Listagem de Ordenados Líquidos e Coordenadas Bancárias (IBAN e Banco) para Transferência.</p>
        </div>
        <div class="d-flex gap-2">
            @if($run)
                <a href="{{ route('rh.salarios.export_banco', $run->id) }}" class="btn btn-success fw-bold px-4 py-2" style="border-radius: 10px;">
                    <i class="fas fa-file-excel me-2"></i> Exportar Ficheiro Excel / PS2
                </a>
            @endif
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card card-body border-0 shadow-sm rounded-4 mb-4 bg-white">
        <form method="GET" action="{{ route('rh.reports.bank') }}" class="row g-3 align-items-center">
            <div class="col-md-6 col-lg-5">
                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-calendar-alt text-primary me-1"></i> Período de Processamento Salarial</label>
                <select name="run_id" class="form-select fw-bold border-secondary-subtle" onchange="this.form.submit()" style="border-radius: 10px;">
                    @forelse($runs as $r)
                        <option value="{{ $r->id }}" {{ $run && $run->id == $r->id ? 'selected' : '' }}>
                            Mês {{ $r->reference }} (v{{ $r->version }}) — Total Líquido: {{ number_format($r->total_net_paid, 2, ',', '.') }} Kz
                        </option>
                    @empty
                        <option value="">Nenhum processamento salarial fechado disponível</option>
                    @endforelse
                </select>
            </div>
            @if($run)
            <div class="col-md-6 col-lg-7 d-flex align-items-end justify-content-md-end gap-2 pt-2 pt-md-0">
                <span class="badge bg-primary-subtle text-primary border px-3 py-2 fw-bold" style="border-radius: 8px;">
                    <i class="fas fa-users me-1"></i> {{ $receipts->count() }} Beneficiários
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
        <!-- Table Card -->
        <div class="card-premium overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-4 text-muted small text-uppercase fw-bold">#</th>
                            <th class="py-3 px-4 text-muted small text-uppercase fw-bold">COLABORADOR</th>
                            <th class="py-3 px-4 text-muted small text-uppercase fw-bold">BANCO</th>
                            <th class="py-3 px-4 text-muted small text-uppercase fw-bold">COORDENADA BANCÁRIA (IBAN)</th>
                            <th class="py-3 px-4 text-muted small text-uppercase fw-bold text-end">VALOR A TRANSFERIR (LÍQUIDO)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totNet = 0; @endphp
                        @forelse($receipts as $idx => $rec)
                            @php 
                                $netVal = $rec->net_total ?? 0;
                                $totNet += $netVal; 
                            @endphp
                            <tr>
                                <td class="py-3 px-4 text-muted small">{{ $idx + 1 }}</td>
                                <td class="py-3 px-4">
                                    <div class="fw-bold text-dark">{{ $rec->employee->name ?? 'Colaborador' }}</div>
                                    <div class="small text-muted font-monospace">NIF: {{ $rec->employee->nif ?? 'N/A' }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="badge bg-primary-subtle text-primary border px-3 py-1 fw-bold">
                                        <i class="fas fa-university me-1"></i> {{ $rec->employee->bank_name ?? 'BAI' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <code class="fs-6 fw-bold text-dark font-monospace">{{ $rec->employee->iban ?? 'AO06 0040 0000 5214 1256 2514 1012 6' }}</code>
                                </td>
                                <td class="py-3 px-4 text-end fw-extrabold text-success fs-6">
                                    {{ number_format($netVal, 2, ',', '.') }} Kz
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-5">Nenhum recibo de vencimento encontrado para este período.</td></tr>
                        @endforelse
                    </tbody>
                    @if($receipts->count() > 0)
                        <tfoot class="bg-light fw-extrabold border-top">
                            <tr>
                                <td colspan="4" class="py-3 px-4 text-uppercase text-dark">TOTAL A PROCESSAR PELO BANCO</td>
                                <td class="py-3 px-4 text-end text-success fs-5">{{ number_format($totNet, 2, ',', '.') }} Kz</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

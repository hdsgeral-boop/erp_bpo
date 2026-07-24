@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h2 class="fw-bold text-dark mb-1"><i class="fas fa-university text-success me-2"></i>Mapa de Pagamentos Bancários (Salários)</h2>
            <p class="text-muted mb-0 small">Listagem de Ordenados Líquidos e Coordenadas Bancárias (IBAN e Banco) para Transferência.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm rounded-3" onclick="window.print()"><i class="fas fa-print me-1"></i> Imprimir</button>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-light">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('rh.reports.bank') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted">Período de Processamento</label>
                    <select name="run_id" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                        @forelse($runs as $r)
                            <option value="{{ $r->id }}" {{ $run && $run->id == $r->id ? 'selected' : '' }}>
                                Mês {{ $r->reference }} (v{{ $r->version }}) — {{ number_format($r->total_net, 2, ',', '.') }} Kz
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
                                <th>Colaborador</th>
                                <th>Banco</th>
                                <th>Coordenada Bancária (IBAN)</th>
                                <th class="text-end fw-bold">Valor a Transferir (Líquido)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totNet = 0; @endphp
                            @forelse($receipts as $idx => $rec)
                                @php $totNet += $rec->net_salary; @endphp
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $rec->employee->full_name }}</div>
                                        <div class="small text-muted">NIF: {{ $rec->employee->tax_id ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary bg-opacity-10 text-dark fw-semibold">
                                            {{ $rec->employee->bank_name ?? 'Banco Padrão' }}
                                        </span>
                                    </td>
                                    <td>
                                        <code class="fs-6 text-dark">{{ $rec->employee->iban ?? 'IBAN Não Cadastrado' }}</code>
                                    </td>
                                    <td class="text-end fw-bold text-success fs-6">
                                        {{ number_format($rec->net_salary, 2, ',', '.') }} Kz
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">Sem recibos para apresentar.</td></tr>
                            @endforelse
                        </tbody>
                        @if($receipts->count() > 0)
                            <tfoot class="table-group-divider bg-light fw-bold">
                                <tr>
                                    <td colspan="4">TOTAL A PROCESSAR PELO BANCO</td>
                                    <td class="text-end text-success fs-5">{{ number_format($totNet, 2, ',', '.') }} Kz</td>
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

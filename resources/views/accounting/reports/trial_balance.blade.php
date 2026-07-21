@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-auto">
            <div class="bg-success text-white p-3 rounded-3 shadow-sm">
                <i class="fas fa-balance-scale fa-2x"></i>
            </div>
        </div>
        <div class="col">
            <h2 class="mb-0 fw-bold">Balancete Analítico</h2>
            <p class="text-muted mb-0">Verificação de saldos contabilísticos do exercício de {{ $year }}</p>
        </div>
        <div class="col-auto">
            <form action="{{ route('contabilidade.trial_balance') }}" method="GET" class="d-flex gap-2">
                <select name="year" class="form-select fw-bold border-secondary" onchange="this.form.submit()">
                    @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                        <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>Exercício {{ $i }}</option>
                    @endfor
                </select>
                <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="fas fa-print"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 text-muted text-uppercase py-3">Código</th>
                            <th class="text-muted text-uppercase py-3">Descrição da Conta</th>
                            <th class="text-end text-muted text-uppercase py-3">Total Débito</th>
                            <th class="text-end text-muted text-uppercase py-3">Total Crédito</th>
                            <th class="text-end pe-4 text-muted text-uppercase py-3">Saldo (D - C)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grandDebit = 0;
                            $grandCredit = 0;
                        @endphp
                        @forelse($accounts as $acc)
                            @if($acc->total_debit > 0 || $acc->total_credit > 0)
                                @php
                                    // Add to grand total only if it's a top level account (length 2 or 1 depending on chart, usually 2 for classes)
                                    // We'll just sum the 'M' accounts for the grand total to avoid double counting
                                    if($acc->type == 'M') {
                                        $grandDebit += $acc->total_debit;
                                        $grandCredit += $acc->total_credit;
                                    }
                                @endphp
                                <tr class="{{ $acc->type == 'I' ? 'table-light fw-bold' : '' }}">
                                    <td class="ps-4" style="font-family: monospace;">{{ $acc->code }}</td>
                                    <td>
                                        @if($acc->type == 'M')
                                            <span class="ps-3"><i class="fas fa-level-up-alt fa-rotate-90 text-muted me-2"></i></span>
                                        @endif
                                        {{ $acc->description }}
                                    </td>
                                    <td class="text-end text-primary">{{ number_format($acc->total_debit, 2) }}</td>
                                    <td class="text-end text-warning">{{ number_format($acc->total_credit, 2) }}</td>
                                    <td class="text-end pe-4 {{ $acc->balance >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format(abs($acc->balance), 2) }} {{ $acc->balance >= 0 ? '(D)' : '(C)' }}
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    Nenhum movimento contabilístico registado para este exercício.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($accounts->count() > 0)
                    <tfoot class="bg-dark text-white fw-bold">
                        <tr>
                            <td colspan="2" class="text-end py-3">TOTAIS (Contas de Movimento):</td>
                            <td class="text-end text-info py-3">{{ number_format($grandDebit, 2) }}</td>
                            <td class="text-end text-warning py-3">{{ number_format($grandCredit, 2) }}</td>
                            <td class="text-end pe-4 py-3 {{ ($grandDebit - $grandCredit) == 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format(abs($grandDebit - $grandCredit), 2) }}
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

<style type="text/css" media="print">
    body * { visibility: hidden; }
    .container-fluid, .container-fluid * { visibility: visible; }
    .container-fluid { position: absolute; left: 0; top: 0; width: 100%; }
    .btn, form, a { display: none !important; }
    .card { box-shadow: none !important; border: 1px solid #ddd !important; }
</style>
@endsection

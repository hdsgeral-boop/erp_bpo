@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-auto">
            <div class="bg-success text-white p-3 rounded-3 shadow-sm">
                <i class="fas fa-check-double fa-2x"></i>
            </div>
        </div>
        <div class="col">
            <h2 class="mb-0 fw-bold">Reconciliações Bancárias</h2>
            <p class="text-muted mb-0">Histórico de conferência entre extratos bancários e tesouraria do ERP</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('tesouraria.reconciliations.create') }}" class="btn btn-primary px-4 py-2 fw-bold shadow-sm">
                <i class="fas fa-plus me-2"></i>Nova Reconciliação
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 text-muted text-uppercase py-3">Data</th>
                            <th class="text-muted text-uppercase py-3">Conta Bancária</th>
                            <th class="text-end text-muted text-uppercase py-3">Saldo Inicial</th>
                            <th class="text-end text-muted text-uppercase py-3">Saldo Final</th>
                            <th class="text-end text-muted text-uppercase py-3">Diferença</th>
                            <th class="text-end pe-4 text-muted text-uppercase py-3">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reconciliations as $rec)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">
                                {{ \Carbon\Carbon::parse($rec->reconciliation_date)->format('d/m/Y') }}
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary">
                                    <i class="fas fa-university me-1"></i> {{ $rec->account_code }}
                                </span>
                            </td>
                            <td class="text-end">{{ number_format($rec->opening_balance, 2) }}</td>
                            <td class="text-end">{{ number_format($rec->closing_balance, 2) }}</td>
                            <td class="text-end fw-bold {{ ($rec->closing_balance - $rec->opening_balance) >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($rec->closing_balance - $rec->opening_balance, 2) }}
                            </td>
                            <td class="text-end pe-4">
                                @if($rec->status == 'OPEN')
                                    <span class="badge bg-warning text-dark"><i class="fas fa-unlock me-1"></i>Em Curso</span>
                                @else
                                    <span class="badge bg-success"><i class="fas fa-lock me-1"></i>Fechada</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-check-double fa-3x mb-3 opacity-25"></i>
                                <h5>Nenhuma reconciliação efetuada</h5>
                                <p>Crie uma nova reconciliação para validar os movimentos bancários.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

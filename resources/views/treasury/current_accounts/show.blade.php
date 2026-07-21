@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        background: #ffffff;
    }
    .text-debit { color: #ef4444; }
    .text-credit { color: #10b981; }
    .table-statement th {
        background-color: #f8fafc;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: #64748b;
        font-weight: 700;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('tesouraria.current_accounts.index', ['type' => $type]) }}" class="text-decoration-none text-muted mb-2 d-inline-block">
                <i class="fas fa-arrow-left me-1"></i> Voltar às Contas Correntes
            </a>
            <h2 class="fw-bold mb-0 text-dark">
                <i class="fas fa-list-alt text-primary me-2"></i> Extrato de Conta Corrente
            </h2>
        </div>
        <button class="btn btn-outline-secondary fw-bold" onclick="window.print()">
            <i class="fas fa-print me-1"></i> Imprimir
        </button>
    </div>

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card-premium p-4 h-100">
                <h5 class="fw-bold border-bottom pb-2 mb-3 text-muted text-uppercase small">Detalhes da Entidade</h5>
                <h3 class="fw-bold text-dark mb-1">{{ $entity->name }}</h3>
                <div class="d-flex gap-4 mt-3">
                    <div>
                        <div class="small text-muted text-uppercase fw-bold">NIF</div>
                        <div class="fw-medium">{{ $entity->tax_id ?? 'N/D' }}</div>
                    </div>
                    <div>
                        <div class="small text-muted text-uppercase fw-bold">Email</div>
                        <div class="fw-medium">{{ $entity->email ?? 'N/D' }}</div>
                    </div>
                    <div>
                        <div class="small text-muted text-uppercase fw-bold">Telefone</div>
                        <div class="fw-medium">{{ $entity->phone ?? 'N/D' }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-premium p-4 h-100 d-flex flex-column justify-content-center align-items-center text-center bg-light border">
                <h5 class="fw-bold text-muted text-uppercase small mb-2">Saldo Atual Pendente</h5>
                @php
                    $finalBalance = $transactions->last() ? $transactions->last()->balance : 0;
                    $balanceClass = 'text-dark';
                    if ($finalBalance > 0) {
                        $balanceClass = $type === 'customer' ? 'text-success' : 'text-danger';
                    } elseif ($finalBalance < 0) {
                        $balanceClass = $type === 'customer' ? 'text-danger' : 'text-success';
                    }
                @endphp
                <h1 class="fw-bold mb-3 {{ $balanceClass }}" style="font-size: 2.5rem;">
                    {{ number_format($finalBalance, 2, ',', '.') }} <span class="fs-4">AOA</span>
                </h1>
                
                @if($finalBalance > 0)
                    @if($type === 'customer')
                        <a href="{{ route('tesouraria.documentos.create', ['category' => 'recebimentos', 'entity_id' => $entity->id]) }}" class="btn btn-success fw-bold px-4 rounded-pill">
                            <i class="fas fa-hand-holding-usd me-1"></i> Emitir Recibo
                        </a>
                    @else
                        <a href="{{ route('tesouraria.documentos.create', ['category' => 'pagamentos', 'entity_id' => $entity->id]) }}" class="btn btn-danger fw-bold px-4 rounded-pill">
                            <i class="fas fa-money-check-alt me-1"></i> Emitir Pagamento
                        </a>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <div class="card-premium">
        <div class="table-responsive">
            <table class="table table-hover table-statement align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Data</th>
                        <th>Documento</th>
                        <th>Descrição</th>
                        <th class="text-end">Débito (AOA)</th>
                        <th class="text-end">Crédito (AOA)</th>
                        <th class="text-end pe-4">Saldo Acumulado (AOA)</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($transactions) == 0)
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-folder-open fa-3x mb-3 text-light"></i>
                            <h5>Sem Movimentos</h5>
                            <p>Não existem movimentos financeiros registados para esta entidade.</p>
                        </td>
                    </tr>
                    @else
                        @foreach($transactions as $t)
                        <tr>
                            <td class="ps-4">{{ \Carbon\Carbon::parse($t->date)->format('d/m/Y') }}</td>
                            <td>
                                <span class="fw-bold">{{ $t->document }}</span>
                            </td>
                            <td class="text-muted">{{ $t->description }}</td>
                            
                            {{-- Débito --}}
                            <td class="text-end">
                                @if($t->debit > 0)
                                    <span class="text-dark">{{ number_format($t->debit, 2, ',', '.') }}</span>
                                @endif
                            </td>
                            
                            {{-- Crédito --}}
                            <td class="text-end">
                                @if($t->credit > 0)
                                    <span class="text-dark">{{ number_format($t->credit, 2, ',', '.') }}</span>
                                @endif
                            </td>
                            
                            {{-- Saldo --}}
                            <td class="text-end pe-4 fw-bold {{ $t->balance > 0 ? ($type == 'customer' ? 'text-success' : 'text-danger') : 'text-dark' }}">
                                {{ number_format($t->balance, 2, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        <div class="p-3 bg-light border-top text-end text-muted small rounded-bottom">
            <i class="fas fa-info-circle me-1"></i>
            @if($type === 'customer')
                O Débito aumenta a dívida do cliente. O Crédito representa pagamentos efetuados pelo cliente.
            @else
                O Crédito aumenta a nossa dívida para com o fornecedor. O Débito representa pagamentos que efetuámos.
            @endif
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    .info-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    .info-value {
        font-size: 1.05rem;
        color: #1e293b;
        font-weight: 500;
        margin-bottom: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('compras.pedidos.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
                <i class="fas fa-arrow-left me-1"></i> Voltar à Listagem
            </a>
            <h2 class="fw-bold mb-0 text-dark">Pedido Interno #REQ-{{ str_pad($purchaseRequest->id, 4, '0', STR_PAD_LEFT) }}</h2>
        </div>
        <div>
            <a href="{{ route('compras.pedidos.pdf', $purchaseRequest->id) }}" target="_blank" class="btn btn-outline-secondary fw-bold me-2" style="border-radius: 10px;">
                <i class="fas fa-file-pdf text-danger me-1"></i> Imprimir (PDF)
            </a>
            @if($purchaseRequest->status === 'PENDING')
                <form action="{{ route('compras.pedidos.reject', $purchaseRequest->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger text-white fw-bold me-2" onclick="return confirm('Tem certeza que deseja rejeitar este pedido?')">
                        <i class="fas fa-times me-1"></i> Rejeitar
                    </button>
                </form>
                <form action="{{ route('compras.pedidos.approve', $purchaseRequest->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success text-white fw-bold">
                        <i class="fas fa-check me-1"></i> Aprovar Pedido
                    </button>
                </form>
            @elseif($purchaseRequest->status === 'APPROVED')
                <a href="{{ route('compras.encomendas.create', ['from_request' => $purchaseRequest->id]) }}" class="btn btn-primary fw-bold">
                    <i class="fas fa-exchange-alt me-1"></i> Gerar Nota de Encomenda
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm" style="border-radius: 10px;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger shadow-sm" style="border-radius: 10px;">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card-premium p-4 h-100">
                <h5 class="fw-bold border-bottom pb-2 mb-4">Detalhes do Pedido</h5>
                
                <div class="info-label">Estado</div>
                <div class="mb-3">
                    @if($purchaseRequest->status === 'PENDING')
                        <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill"><i class="fas fa-clock me-1"></i> Pendente de Aprovação</span>
                    @elseif($purchaseRequest->status === 'APPROVED')
                        <span class="badge bg-success px-3 py-2 fs-6 rounded-pill"><i class="fas fa-check me-1"></i> Aprovado</span>
                    @elseif($purchaseRequest->status === 'REJECTED')
                        <span class="badge bg-danger px-3 py-2 fs-6 rounded-pill"><i class="fas fa-times me-1"></i> Rejeitado</span>
                    @elseif($purchaseRequest->status === 'CONVERTED')
                        <span class="badge bg-info px-3 py-2 fs-6 rounded-pill"><i class="fas fa-exchange-alt me-1"></i> Convertido em Encomenda</span>
                    @endif
                </div>

                <div class="info-label">Requerente</div>
                <div class="info-value">{{ $purchaseRequest->requester_name }}</div>

                <div class="info-label">Departamento</div>
                <div class="info-value">{{ $purchaseRequest->department ? $purchaseRequest->department->name : 'N/A' }}</div>

                <div class="info-label">Data do Pedido</div>
                <div class="info-value">{{ $purchaseRequest->date->format('d/m/Y') }}</div>

                <div class="info-label">Criado por</div>
                <div class="info-value">{{ $purchaseRequest->creator ? $purchaseRequest->creator->name : 'N/A' }} <small class="text-muted d-block">{{ $purchaseRequest->created_at->format('d/m/Y H:i') }}</small></div>

                @if($purchaseRequest->status !== 'PENDING' && $purchaseRequest->approver)
                    <div class="info-label border-top pt-3 mt-3">Avaliado por</div>
                    <div class="info-value">{{ $purchaseRequest->approver->name }} <small class="text-muted d-block">{{ $purchaseRequest->approved_at->format('d/m/Y H:i') }}</small></div>
                @endif
                
                @if($purchaseRequest->convertedToOrder)
                    <div class="info-label border-top pt-3 mt-3">Nota de Encomenda Gerada</div>
                    <div class="info-value"><a href="{{ route('compras.encomendas.show', $purchaseRequest->convertedToOrder->id) }}" class="fw-bold">{{ $purchaseRequest->convertedToOrder->order_number }}</a></div>
                @endif

                @if($purchaseRequest->notes)
                    <div class="info-label border-top pt-3 mt-3">Motivo / Justificação</div>
                    <p class="text-muted fst-italic">{{ $purchaseRequest->notes }}</p>
                @endif
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card-premium p-4 h-100">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-4">
                    <h5 class="fw-bold m-0">Artigos Solicitados</h5>
                    <span class="badge bg-primary rounded-pill px-3 py-2">{{ $purchaseRequest->items->count() }} Linhas</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Artigo</th>
                                <th class="text-center">Qtd. Solicitada</th>
                                <th>Notas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseRequest->items as $item)
                            <tr>
                                <td>
                                    <div class="fw-bold text-primary">{{ $item->product->name }}</div>
                                    <small class="text-muted font-monospace">{{ $item->product->code }}</small>
                                </td>
                                <td class="text-center fw-bold fs-5 text-dark">
                                    {{ number_format($item->quantity, 2, ',', '.') }}
                                </td>
                                <td class="text-muted small fst-italic">
                                    {{ $item->notes ?: '-' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

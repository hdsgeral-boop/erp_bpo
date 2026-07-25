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
            <a href="{{ route('compras.encomendas.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
                <i class="fas fa-arrow-left me-1"></i> Voltar à Listagem
            </a>
            <h2 class="fw-bold mb-0 text-dark">Nota de Encomenda <span class="text-primary">{{ $order->order_number }}</span></h2>
        </div>
        <div>
            @if($order->status === 'PENDING')
                <form action="{{ route('compras.encomendas.approve', $order->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary fw-bold">
                        <i class="fas fa-paper-plane me-1"></i> Aprovar e Enviar
                    </button>
                </form>
            @endif
            
            @if($order->status === 'APPROVED' || $order->status === 'PARTIAL')
                <a href="{{ route('compras.rececoes.create', ['order_id' => $order->id]) }}" class="btn btn-success fw-bold text-white">
                    <i class="fas fa-truck-loading me-1"></i> Rececionar Mercadoria
                </a>
            @endif
            
            <a href="{{ route('compras.encomendas.pdf', $order->id) }}" target="_blank" class="btn btn-outline-secondary fw-bold ms-2">
                <i class="fas fa-file-pdf text-danger me-1"></i> Imprimir (PDF)
            </a>
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
            <div class="card-premium p-4 mb-4">
                <h5 class="fw-bold border-bottom pb-2 mb-4">Detalhes da Encomenda</h5>
                
                <div class="info-label">Estado</div>
                <div class="mb-3">
                    @if($order->status === 'PENDING')
                        <span class="badge bg-secondary px-3 py-2 fs-6 rounded-pill">Pendente de Envio</span>
                    @elseif($order->status === 'APPROVED')
                        <span class="badge bg-primary px-3 py-2 fs-6 rounded-pill">Aprovada / Aguarda Entrega</span>
                    @elseif($order->status === 'PARTIAL')
                        <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill"><i class="fas fa-truck-loading me-1"></i> Parcialmente Recebida</span>
                    @elseif($order->status === 'COMPLETED')
                        <span class="badge bg-success px-3 py-2 fs-6 rounded-pill"><i class="fas fa-check-double me-1"></i> Totalmente Recebida</span>
                    @endif
                </div>

                <div class="info-label">Fornecedor</div>
                <div class="info-value">
                    @if($order->supplier)
                        <a href="{{ route('entidades.show', $order->supplier_id) }}" class="fw-bold text-decoration-none">{{ $order->supplier->name }}</a>
                        <small class="d-block text-muted">NIF: {{ $order->supplier->tax_id }}</small>
                    @else
                        N/A
                    @endif
                </div>

                <div class="info-label">Data de Emissão</div>
                <div class="info-value">{{ $order->date->format('d/m/Y') }}</div>

                <div class="info-label">Total S/ IVA</div>
                <div class="info-value fw-bold text-primary">{{ number_format($order->total_amount, 2, ',', '.') }} AOA</div>

                <div class="info-label">Criada por</div>
                <div class="info-value">{{ $order->creator ? $order->creator->name : 'N/A' }}</div>
            </div>
            
            <div class="card-premium p-4">
                <h5 class="fw-bold border-bottom pb-2 mb-3">Entregas Associadas</h5>
                @if($order->deliveries->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($order->deliveries as $del)
                            <a href="{{ route('compras.rececoes.show', $del->id) }}" class="list-group-item list-group-item-action px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 fw-bold text-primary">{{ $del->delivery_number }}</h6>
                                        <small class="text-muted">{{ $del->date->format('d/m/Y') }} &bull; {{ $del->warehouse->name ?? 'Armazém' }}</small>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted"></i>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-muted small fst-italic">Nenhuma receção efetuada até ao momento.</div>
                @endif
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card-premium p-4 h-100">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-4">
                    <h5 class="fw-bold m-0">Linhas da Encomenda</h5>
                    <span class="badge bg-light text-dark border">{{ $order->items->count() }} Artigos</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Artigo</th>
                                <th class="text-end">Preço Unit.</th>
                                <th class="text-center">Encomendado</th>
                                <th class="text-center">Recebido</th>
                                <th class="text-end">Total (AOA)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->product->name }}</div>
                                    <small class="text-muted font-monospace">{{ $item->product->code }}</small>
                                </td>
                                <td class="text-end">{{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                <td class="text-center fw-bold fs-5 text-dark">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                                <td class="text-center">
                                    @if($item->received_qty == $item->quantity)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="fas fa-check"></i> {{ number_format($item->received_qty, 2, ',', '.') }}</span>
                                    @elseif($item->received_qty > 0)
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">{{ number_format($item->received_qty, 2, ',', '.') }}</span>
                                    @else
                                        <span class="badge bg-light text-muted border">0,00</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold">{{ number_format($item->total_price, 2, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Subtotal:</td>
                                <td class="text-end">{{ number_format($order->total_amount, 2, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end fw-bold">IVA:</td>
                                <td class="text-end">{{ number_format($order->total_tax, 2, ',', '.') }}</td>
                            </tr>
                            <tr class="table-light">
                                <td colspan="4" class="text-end fw-bold fs-5">Total Geral:</td>
                                <td class="text-end fw-bold fs-5 text-primary">{{ number_format($order->total_amount + $order->total_tax, 2, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

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
            <a href="{{ route('compras.rececoes.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
                <i class="fas fa-arrow-left me-1"></i> Voltar à Listagem
            </a>
            <h2 class="fw-bold mb-0 text-dark">Registo de Receção <span class="text-success">{{ $delivery->delivery_number }}</span></h2>
        </div>
        <div>
            @if($delivery->order)
                <a href="{{ route('compras.encomendas.show', $delivery->order->id) }}" class="btn btn-outline-primary fw-bold me-2">
                    <i class="fas fa-file-invoice me-1"></i> Ver Encomenda
                </a>
            @endif
            <button class="btn btn-outline-secondary fw-bold" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Imprimir Guia de Entrada
            </button>
        </div>
    </div>

    <div class="alert alert-success shadow-sm d-flex align-items-center mb-4" style="border-radius: 10px;">
        <i class="fas fa-check-circle fa-2x me-3 text-success"></i>
        <div>
            <strong>Stock Atualizado!</strong> Todos os artigos listados abaixo deram entrada física no armazém selecionado, afetando o inventário em tempo real.
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card-premium p-4 h-100">
                <h5 class="fw-bold border-bottom pb-2 mb-4">Detalhes da Entrega</h5>
                
                <div class="info-label">Nota de Encomenda Origem</div>
                <div class="info-value">
                    @if($delivery->order)
                        <a href="{{ route('compras.encomendas.show', $delivery->order->id) }}" class="fw-bold">{{ $delivery->order->order_number }}</a>
                    @else
                        N/A
                    @endif
                </div>

                <div class="info-label">Fornecedor</div>
                <div class="info-value">
                    {{ $delivery->order && $delivery->order->supplier ? $delivery->order->supplier->name : 'N/A' }}
                </div>

                <div class="info-label">Armazém de Destino (Stock Integrado)</div>
                <div class="info-value fw-bold text-success"><i class="fas fa-warehouse me-1"></i> {{ $delivery->warehouse ? $delivery->warehouse->name : 'N/A' }}</div>

                <div class="info-label">Data da Receção</div>
                <div class="info-value">{{ $delivery->date->format('d/m/Y') }}</div>

                <div class="info-label">Guia Remessa (Fornecedor)</div>
                <div class="info-value">{{ $delivery->delivery_note_number ?: 'Não Registada' }}</div>

                <div class="info-label border-top pt-3 mt-3">Operador / Responsável</div>
                <div class="info-value">{{ $delivery->creator ? $delivery->creator->name : 'N/A' }} <small class="text-muted d-block">{{ $delivery->created_at->format('d/m/Y H:i') }}</small></div>

                @if($delivery->notes)
                    <div class="info-label border-top pt-3 mt-3">Notas da Operação</div>
                    <p class="text-muted fst-italic">{{ $delivery->notes }}</p>
                @endif
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card-premium p-4 h-100">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-4">
                    <h5 class="fw-bold m-0">Mercadoria Recebida (Entrada em Stock)</h5>
                    <span class="badge bg-success rounded-pill px-3 py-2">{{ $delivery->items->count() }} Linhas</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Artigo</th>
                                <th class="text-center">Quantidade Recebida</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($delivery->items as $item)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->product->name }}</div>
                                    <small class="text-muted font-monospace">{{ $item->product->code }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 fs-6">
                                        <i class="fas fa-plus me-1"></i> {{ number_format($item->quantity, 2, ',', '.') }}
                                    </span>
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

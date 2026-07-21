@extends('layouts.app')

@push('styles')
<style>
    .invoice-card {
        background: #ffffff;
        border: none;
        border-radius: 4px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .invoice-header {
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .invoice-title {
        color: #1e293b;
        font-weight: 800;
        font-size: 2rem;
    }
    .info-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #64748b;
        font-weight: 700;
        margin-bottom: 0.2rem;
    }
    .info-value {
        font-size: 1rem;
        color: #0f172a;
        font-weight: 500;
    }
    .watermark-cancelled {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-45deg);
        font-size: 8rem;
        color: rgba(239, 68, 68, 0.1);
        font-weight: 900;
        pointer-events: none;
        z-index: 0;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('vendas.documentos.index', $category) }}" class="text-decoration-none text-muted mb-2 d-inline-block">
                <i class="fas fa-arrow-left me-1"></i> Voltar à Lista
            </a>
            <h2 class="fw-bold mb-0 text-dark">
                {{ $invoice->doc_type }} {{ $invoice->doc_number }}
                @if($invoice->status === 'ISSUED')
                    <span class="badge bg-success ms-2 fs-6 align-middle"><i class="fas fa-check-circle"></i> Emitido</span>
                @else
                    <span class="badge bg-danger ms-2 fs-6 align-middle"><i class="fas fa-times-circle"></i> Anulado</span>
                @endif
                
                @if($invoice->status !== 'CANCELLED' && in_array($invoice->doc_type, ['FT', 'FR', 'ND']))
                    @if($invoice->payment_status === 'PAID')
                        <span class="badge bg-success ms-2 fs-6 align-middle"><i class="fas fa-check-double"></i> Liquidado</span>
                    @elseif($invoice->payment_status === 'PARTIAL')
                        <span class="badge bg-info text-dark ms-2 fs-6 align-middle"><i class="fas fa-star-half-alt"></i> Pagamento Parcial</span>
                    @else
                        <span class="badge bg-secondary ms-2 fs-6 align-middle"><i class="fas fa-hourglass-half"></i> Por Liquidar</span>
                    @endif
                @endif
            </h2>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary fw-bold" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Imprimir (A4)
            </button>
            @if($invoice->status === 'ISSUED')
                @if(in_array($invoice->doc_type, ['FT', 'FR', 'ND']) && $invoice->payment_status !== 'PAID')
                    <a href="{{ route('tesouraria.documentos.create', ['category' => 'recebimentos', 'entity_id' => $invoice->customer_id]) }}" class="btn btn-success fw-bold ms-2">
                        <i class="fas fa-hand-holding-usd me-1"></i> Liquidar / Emitir Recibo
                    </a>
                @endif
                <button type="button" class="btn btn-danger fw-bold ms-2" data-bs-toggle="modal" data-bs-target="#cancelModal">
                    <i class="fas fa-ban me-1"></i> Anular
                </button>
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

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card invoice-card p-5 position-relative overflow-hidden">
                @if($invoice->status === 'CANCELLED')
                    <div class="watermark-cancelled">ANULADA</div>
                @endif
                
                <div class="invoice-header d-flex justify-content-between align-items-start position-relative" style="z-index: 1;">
                    <div>
                        <h1 class="invoice-title text-uppercase">{{ $category }}</h1>
                        <h4 class="text-primary fw-bold">{{ $invoice->doc_number }}</h4>
                        
                        <div class="mt-4">
                            <div class="info-label">Emitida Por:</div>
                            <div class="info-value fw-bold">{{ $invoice->company->name ?? 'A Nossa Empresa' }}</div>
                            <div class="text-muted small">NIF: {{ $invoice->company->tax_id ?? '999999999' }}</div>
                        </div>
                    </div>
                    <div class="text-end text-muted small">
                        <div><strong class="text-dark">Data de Emissão:</strong> {{ $invoice->date->format('d/m/Y') }}</div>
                        <div><strong class="text-dark">Operador:</strong> {{ $invoice->creator->name ?? 'Sistema' }}</div>
                        <div><strong class="text-dark">Armazém Origem:</strong> {{ $invoice->warehouse->name ?? 'N/A' }}</div>
                    </div>
                </div>

                <div class="row mb-5 position-relative" style="z-index: 1;">
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded border">
                            <div class="info-label">Faturado a (Cliente):</div>
                            <div class="info-value fw-bold fs-5">{{ $invoice->customer->name ?? 'Cliente Final' }}</div>
                            <div class="text-muted mt-1">NIF: {{ $invoice->customer->tax_id ?? 'Consumidor Final' }}</div>
                            <div class="text-muted">{{ $invoice->customer->address ?? '' }}</div>
                        </div>
                    </div>
                    
                    @if($invoice->status === 'CANCELLED')
                    <div class="col-sm-6">
                        <div class="p-3 bg-danger bg-opacity-10 border border-danger rounded h-100">
                            <div class="text-danger fw-bold mb-1"><i class="fas fa-exclamation-circle me-1"></i> Informação de Anulação</div>
                            <div class="small"><strong>Por:</strong> {{ $invoice->canceller->name ?? 'N/A' }}</div>
                            <div class="small"><strong>Em:</strong> {{ $invoice->cancelled_at ? $invoice->cancelled_at->format('d/m/Y H:i') : '' }}</div>
                            <div class="small mt-2"><strong>Motivo:</strong> {{ $invoice->cancellation_reason }}</div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="table-responsive mb-4 position-relative" style="z-index: 1;">
                    <table class="table table-bordered border-light align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Artigo / Serviço</th>
                                <th class="text-center">Qtd</th>
                                <th class="text-end">Preço Unit.</th>
                                <th class="text-end">Desc.</th>
                                <th class="text-center">IVA</th>
                                <th class="text-end">Valor Líquido</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $item)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $item->product->name ?? 'Artigo Desconhecido' }}</div>
                                    <small class="text-muted">Ref: {{ $item->product->code ?? '-' }}</small>
                                </td>
                                <td class="text-center">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($item->discount_amount, 2, ',', '.') }}</td>
                                <td class="text-center">{{ number_format($item->tax_rate, 2, ',', '.') }}%</td>
                                <td class="text-end fw-bold">{{ number_format($item->subtotal, 2, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row justify-content-end position-relative" style="z-index: 1;">
                    <div class="col-sm-5">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-end text-muted">Subtotal (S/ IVA):</td>
                                <td class="text-end fw-bold">{{ number_format($invoice->total_amount, 2, ',', '.') }} AOA</td>
                            </tr>
                            <tr>
                                <td class="text-end text-muted border-bottom pb-2">Total IVA:</td>
                                <td class="text-end fw-bold border-bottom pb-2">{{ number_format($invoice->total_tax, 2, ',', '.') }} AOA</td>
                            </tr>
                            <tr>
                                <td class="text-end fs-5 fw-bold pt-2">Total a Pagar:</td>
                                <td class="text-end fs-5 fw-bold text-dark pt-2">{{ number_format($invoice->total_amount + $invoice->total_tax, 2, ',', '.') }} AOA</td>
                            </tr>
                            @if(in_array($invoice->doc_type, ['FT', 'FR', 'ND']))
                            <tr>
                                <td class="text-end text-success fw-bold">Valor Liquidado:</td>
                                <td class="text-end fw-bold text-success">{{ number_format($invoice->amount_paid, 2, ',', '.') }} AOA</td>
                            </tr>
                            <tr>
                                <td class="text-end fs-6 fw-bold text-danger pt-2">Saldo Pendente:</td>
                                <td class="text-end fs-6 fw-bold text-danger pt-2">{{ number_format(($invoice->total_amount + $invoice->total_tax) - $invoice->amount_paid, 2, ',', '.') }} AOA</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <div class="mt-5 text-center text-muted small position-relative" style="z-index: 1;">
                    @if($invoice->notes)
                        <div class="mb-3 fst-italic">{{ $invoice->notes }}</div>
                    @endif
                    <hr>
                    <p class="mb-0">Documento processado por programa certificado.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Anulação -->
@if($invoice->status === 'ISSUED')
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('vendas.documentos.cancel', ['category' => $category, 'id' => $invoice->id]) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i> Confirmar Anulação</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Tem a certeza que deseja anular a fatura <strong>{{ $invoice->doc_number }}</strong>?</p>
                    <p class="small text-muted">O stock correspondente será automaticamente devolvido ao armazém ({{ $invoice->warehouse->name ?? 'Armazém' }}).</p>
                    
                    <div class="mb-3 mt-4">
                        <label class="form-label fw-bold">Motivo da Anulação (Obrigatório) <span class="text-danger">*</span></label>
                        <textarea name="cancellation_reason" class="form-control" rows="3" required minlength="5" placeholder="Ex: Fatura emitida com NIF incorreto..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Anulação</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

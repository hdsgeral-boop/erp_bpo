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
@php
    $title = $category === 'recebimentos' ? 'Recibo' : 'Pagamento';
@endphp
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('tesouraria.documentos.index', $category) }}" class="text-decoration-none text-muted mb-2 d-inline-block">
                <i class="fas fa-arrow-left me-1"></i> Voltar à Lista
            </a>
            <h2 class="fw-bold mb-0 text-dark">
                {{ $receipt->doc_type }} {{ $receipt->doc_number }}
                @if($receipt->status === 'ISSUED')
                    <span class="badge bg-success ms-2 fs-6 align-middle"><i class="fas fa-check-circle"></i> Emitido</span>
                @else
                    <span class="badge bg-danger ms-2 fs-6 align-middle"><i class="fas fa-times-circle"></i> Anulado</span>
                @endif
            </h2>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('tesouraria.documentos.pdf', ['category' => $category, 'id' => $receipt->id]) }}" target="_blank" class="btn btn-outline-secondary fw-bold px-3 py-2" style="border-radius: 10px;">
                <i class="fas fa-file-pdf text-danger me-1"></i> Imprimir (A4 PDF)
            </a>
            @if($receipt->status === 'ISSUED')
                <button type="button" class="btn btn-danger fw-bold ms-2" data-bs-toggle="modal" data-bs-target="#cancelModal">
                    <i class="fas fa-ban me-1"></i> Anular {{ $title }}
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
                @if($receipt->status === 'CANCELLED')
                    <div class="watermark-cancelled">ANULADO</div>
                @endif
                
                <div class="invoice-header d-flex justify-content-between align-items-start position-relative" style="z-index: 1;">
                    <div>
                        <h1 class="invoice-title text-uppercase">{{ $title }}</h1>
                        <h4 class="text-primary fw-bold">{{ $receipt->doc_number }}</h4>
                        
                        <div class="mt-4">
                            <div class="info-label">Emitido Por:</div>
                            <div class="info-value fw-bold">{{ $receipt->company->name ?? 'A Nossa Empresa' }}</div>
                            <div class="text-muted small">NIF: {{ $receipt->company->tax_id ?? '999999999' }}</div>
                        </div>
                    </div>
                    <div class="text-end text-muted small">
                        <div><strong class="text-dark">Data de Emissão:</strong> {{ $receipt->date->format('d/m/Y') }}</div>
                        <div><strong class="text-dark">Conta Tesouraria:</strong> {{ $receipt->treasuryAccount->name ?? 'N/A' }}</div>
                        <div><strong class="text-dark">Método Pgto:</strong> {{ $receipt->payment_method }}</div>
                        @if($receipt->payment_reference)
                            <div><strong class="text-dark">Ref:</strong> {{ $receipt->payment_reference }}</div>
                        @endif
                    </div>
                </div>

                <div class="row mb-5 position-relative" style="z-index: 1;">
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded border">
                            <div class="info-label">{{ $category === 'recebimentos' ? 'Recebido de (Cliente)' : 'Pago a (Fornecedor)' }}:</div>
                            <div class="info-value fw-bold fs-5">{{ $receipt->thirdParty->name ?? 'Entidade Desconhecida' }}</div>
                            <div class="text-muted mt-1">NIF: {{ $receipt->thirdParty->tax_id ?? 'N/D' }}</div>
                            <div class="text-muted">{{ $receipt->thirdParty->address ?? '' }}</div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive mb-4 position-relative" style="z-index: 1;">
                    <h6 class="fw-bold text-muted text-uppercase mb-3">Documentos Liquidados</h6>
                    <table class="table table-bordered border-light align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Documento Referência</th>
                                <th>Data Doc.</th>
                                <th class="text-end">Valor Liquidado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($receipt->items as $item)
                            <tr>
                                <td>
                                    @if($item->sale_id && $item->sale)
                                        <span class="fw-bold">{{ $item->sale->doc_type }} {{ $item->sale->doc_number }}</span>
                                    @elseif($item->purchase_invoice_id && $item->purchaseInvoice)
                                        <span class="fw-bold">COMPRA {{ $item->purchaseInvoice->doc_number }}</span>
                                    @else
                                        <span class="text-muted">Desconhecido</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->sale_id && $item->sale)
                                        {{ $item->sale->date->format('d/m/Y') }}
                                    @elseif($item->purchase_invoice_id && $item->purchaseInvoice)
                                        {{ $item->purchaseInvoice->date->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-end fw-bold">{{ number_format($item->amount_paid, 2, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row justify-content-end position-relative" style="z-index: 1;">
                    <div class="col-sm-5">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-end fs-5 fw-bold pt-2">Total {{ $category === 'recebimentos' ? 'Recebido' : 'Pago' }}:</td>
                                <td class="text-end fs-5 fw-bold text-primary pt-2 border-top border-2 border-dark">{{ number_format($receipt->total_amount, 2, ',', '.') }} {{ $receipt->treasuryAccount->currency ?? 'AOA' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="mt-5 text-center text-muted small position-relative" style="z-index: 1;">
                    <hr>
                    <p class="mb-0">Documento processado por programa certificado.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Anulação -->
@if($receipt->status === 'ISSUED')
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('tesouraria.documentos.cancel', ['category' => $category, 'id' => $receipt->id]) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i> Confirmar Anulação</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Tem a certeza que deseja anular este documento <strong>{{ $receipt->doc_number }}</strong>?</p>
                    <p class="small text-muted">Aviso: Os saldos pendentes das faturas associadas serão repostos e o valor sairá/entrará novamente da sua Conta de Tesouraria ({{ $receipt->treasuryAccount->name }}).</p>
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

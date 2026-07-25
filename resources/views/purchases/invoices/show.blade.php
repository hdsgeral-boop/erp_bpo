@extends('layouts.app')

@push('styles')
<style>
    .invoice-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    .watermark-cancelled {
        position: absolute;
        top: 40%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-30deg);
        font-size: 7rem;
        color: rgba(239, 68, 68, 0.12);
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
            <a href="{{ route('compras.faturas.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
                <i class="fas fa-arrow-left me-1"></i> Voltar à Lista de Faturas
            </a>
            <h2 class="fw-bold mb-0 text-dark">
                Fatura nº {{ $invoice->invoice_number }}
                @if($invoice->status === 'CANCELLED')
                    <span class="badge bg-danger ms-2 fs-6 align-middle"><i class="fas fa-times-circle"></i> Anulada</span>
                @elseif($invoice->payment_status === 'PAID')
                    <span class="badge bg-success ms-2 fs-6 align-middle"><i class="fas fa-check-double"></i> Paga</span>
                @else
                    <span class="badge bg-info text-dark ms-2 fs-6 align-middle"><i class="fas fa-file-invoice"></i> Registada</span>
                @endif
            </h2>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('compras.faturas.pdf', $invoice->id) }}" target="_blank" class="btn btn-outline-secondary fw-bold px-3 py-2" style="border-radius: 10px;">
                <i class="fas fa-file-pdf text-danger me-1"></i> Imprimir (A4 PDF)
            </a>
            @if($invoice->status !== 'CANCELLED')
                <button type="button" class="btn btn-danger fw-bold ms-2 px-3 py-2" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#cancelInvoiceModal">
                    <i class="fas fa-ban me-1"></i> Anular Fatura
                </button>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card invoice-card p-5 position-relative overflow-hidden">
                @if($invoice->status === 'CANCELLED')
                    <div class="watermark-cancelled">ANULADA</div>
                @endif

                <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4 position-relative" style="z-index: 1;">
                    <div>
                        <h1 class="h3 fw-extrabold text-uppercase text-dark mb-1">FATURA DE FORNECEDOR</h1>
                        <h4 class="text-primary fw-bold mb-3">{{ $invoice->invoice_number }}</h4>
                        
                        <div>
                            <span class="text-muted small fw-bold text-uppercase d-block">Empresa Adquirente:</span>
                            <span class="fw-bold fs-6 text-dark">{{ $invoice->company->name ?? 'CONSULVOLT SOLUÇÕES - ERP' }}</span>
                            <div class="text-muted small">NIF: {{ $invoice->company->nif ?? '5417000000' }}</div>
                        </div>
                    </div>
                    <div class="text-end text-muted small">
                        <div><strong class="text-dark">Data de Emissão:</strong> {{ \Carbon\Carbon::parse($invoice->date)->format('d/m/Y') }}</div>
                        <div><strong class="text-dark">Data de Registo:</strong> {{ $invoice->created_at ? $invoice->created_at->format('d/m/Y H:i') : '-' }}</div>
                        <div><strong class="text-dark">Estado Pgto:</strong> {{ $invoice->payment_status ?? 'PENDING' }}</div>
                    </div>
                </div>

                <div class="row mb-4 position-relative" style="z-index: 1;">
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="text-muted small fw-bold text-uppercase d-block mb-1">Fornecedor / Emitente:</span>
                            <div class="fw-bold fs-5 text-dark">{{ $invoice->supplier->name ?? 'Fornecedor Desconhecido' }}</div>
                            <div class="text-muted small">NIF: {{ $invoice->supplier->nif ?? 'N/D' }}</div>
                            <div class="text-muted small">{{ $invoice->supplier->address ?? '' }}</div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive mb-4 position-relative" style="z-index: 1;">
                    <h6 class="fw-bold text-muted text-uppercase mb-3">Linhas de Artigos & Despesas</h6>
                    <table class="table table-bordered border-light align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Artigo / Descrição</th>
                                <th class="text-center">Qtd.</th>
                                <th class="text-end">Preço Unit. (AKZ)</th>
                                <th class="text-end">Total Linha (AKZ)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoice->items as $item)
                            <tr>
                                <td class="font-monospace text-muted">{{ $item->product->code ?? '-' }}</td>
                                <td><strong class="text-dark">{{ $item->product->name ?? 'Artigo/Despesa' }}</strong></td>
                                <td class="text-center font-monospace">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                                <td class="text-end font-monospace">{{ number_format($item->unit_price, 2, ',', '.') }} Kz</td>
                                <td class="text-end font-monospace fw-bold">{{ number_format($item->total_price ?? ($item->quantity * $item->unit_price), 2, ',', '.') }} Kz</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Sem linhas discriminadas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="row justify-content-end position-relative" style="z-index: 1;">
                    <div class="col-sm-5">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-end fs-5 fw-bold pt-2">Total Fatura (AKZ):</td>
                                <td class="text-end fs-5 fw-bold text-primary pt-2 border-top border-2 border-dark">{{ number_format($invoice->total_amount, 2, ',', '.') }} Kz</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Anulacao -->
@if($invoice->status !== 'CANCELLED')
<div class="modal fade" id="cancelInvoiceModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('compras.faturas.cancel', $invoice->id) }}" method="POST">
            @csrf
            <div class="modal-content rounded-4">
                <div class="modal-header bg-danger text-white rounded-top-4">
                    <h5 class="modal-title fw-bold"><i class="fas fa-exclamation-triangle me-2"></i> Confirmar Anulação</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p>Tem a certeza que deseja anular a fatura de fornecedor <strong>{{ $invoice->invoice_number }}</strong>?</p>
                    <p class="small text-muted mb-0">Nota: O stock adicionado por esta fatura será automaticamente estornado do inventário.</p>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger rounded-3 fw-bold">Confirmar Anulação</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

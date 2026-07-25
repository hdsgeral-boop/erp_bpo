@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    .badge-issued { background-color: #dcfce7; color: #15803d; }
    .badge-cancelled { background-color: #fee2e2; color: #b91c1c; }
</style>
@endpush

@section('content')
@php
    $isReceipt = $category === 'recebimentos';
    $title = $isReceipt ? 'Documentos de Tesouraria (Recebimentos)' : 'Documentos de Tesouraria (Pagamentos)';
    $subtitle = $isReceipt ? 'Recibos de liquidação de clientes e entradas de tesouraria.' : 'Recibos de pagamentos a fornecedores e saídas de tesouraria.';
    $btnText = $isReceipt ? 'Emitir Novo Recibo/Liquidação' : 'Emitir Novo Pagamento';
@endphp
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-extrabold text-dark mb-1">
                <i class="fas {{ $isReceipt ? 'fa-hand-holding-usd text-primary' : 'fa-money-bill-wave text-danger' }} me-2"></i> {{ $title }}
            </h2>
            <p class="text-muted small mb-0">{{ $subtitle }}</p>
        </div>
        <div>
            <a href="{{ route('tesouraria.documentos.create', $category) }}" class="btn btn-primary fw-bold px-3 py-2" style="border-radius: 10px;">
                <i class="fas fa-plus-circle me-1"></i> {{ $btnText }}
            </a>
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

    <!-- Card & Table -->
    <div class="card-premium overflow-hidden">
        <!-- Filter Header -->
        <div class="p-3 border-bottom bg-light">
            <form action="{{ route('tesouraria.documentos.index', $category) }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label text-muted small fw-bold mb-1">Pesquisar Entidade ou Nº Documento</label>
                    <input type="text" name="search" class="form-control form-control-sm rounded-3" placeholder="Ex: RC 2026/001, Cliente..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold mb-1">Estado do Documento</label>
                    <select name="status" class="form-select form-select-sm rounded-3">
                        <option value="">Todos os Estados</option>
                        <option value="ISSUED" {{ request('status') == 'ISSUED' ? 'selected' : '' }}>Emitido (Ativo)</option>
                        <option value="CANCELLED" {{ request('status') == 'CANCELLED' ? 'selected' : '' }}>Anulado</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm rounded-3 grow fw-bold"><i class="fas fa-filter me-1"></i> Filtrar</button>
                    <a href="{{ route('tesouraria.documentos.index', $category) }}" class="btn btn-outline-secondary btn-sm rounded-3"><i class="fas fa-undo me-1"></i> Limpar</a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">N.º RECIBO</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">ENTIDADE / CLIENTE</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">CONTA BANCÁRIA</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">DATA LIQUIDAÇÃO</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold text-end">VALOR TOTAL</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold text-center">ESTADO</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold text-center">AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receipts as $receipt)
                    <tr>
                        <td class="py-3 px-4 font-monospace">
                            <a href="{{ route('tesouraria.documentos.show', ['category' => $category, 'id' => $receipt->id]) }}" class="fw-bold text-primary text-decoration-none">
                                {{ $receipt->doc_type }} {{ $receipt->doc_number }}
                            </a>
                        </td>
                        <td class="py-3 px-4">
                            <div class="fw-bold text-dark">{{ $receipt->thirdParty->name ?? 'Consumidor Final / Geral' }}</div>
                            <div class="small text-muted font-monospace">NIF: {{ $receipt->thirdParty->nif ?? '999999999' }}</div>
                        </td>
                        <td class="py-3 px-4 text-muted">
                            <span class="badge bg-secondary-subtle text-dark border"><i class="fas fa-university me-1"></i> {{ $receipt->treasuryAccount->name ?? 'Caixa Geral / BAI' }}</span>
                        </td>
                        <td class="py-3 px-4 text-muted font-monospace">
                            {{ $receipt->date ? $receipt->date->format('d/m/Y') : date('d/m/Y') }}
                        </td>
                        <td class="py-3 px-4 text-end fw-extrabold text-success fs-6">
                            {{ number_format($receipt->total_amount, 2, ',', '.') }} Kz
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($receipt->status === 'ISSUED')
                                <span class="badge badge-issued px-3 py-2 rounded-pill fw-bold"><i class="fas fa-check me-1"></i> Emitido</span>
                            @else
                                <span class="badge badge-cancelled px-3 py-2 rounded-pill fw-bold"><i class="fas fa-ban me-1"></i> Anulado</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            <a href="{{ route('tesouraria.documentos.show', ['category' => $category, 'id' => $receipt->id]) }}" class="btn btn-sm btn-outline-primary fw-bold px-3 py-1" style="border-radius: 8px;">
                                <i class="fas fa-print me-1"></i> Imprimir Recibo
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-receipt fa-2x mb-2 d-block text-secondary opacity-50"></i>
                            Nenhum documento de tesouraria encontrado para este filtro.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($receipts->hasPages())
        <div class="p-3 border-top bg-light">
            {{ $receipts->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection

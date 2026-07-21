@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        background: #ffffff;
    }
    .btn-add-new {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        color: white;
        border: none;
        padding: 0.6rem 1.2rem;
        border-radius: 8px;
        font-weight: 600;
    }
    .btn-add-new:hover {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: white;
    }
    .badge-issued { background-color: #10b981; }
    .badge-cancelled { background-color: #ef4444; }
</style>
@endpush

@section('content')
@php
    $title = $category === 'recebimentos' ? 'Recebimentos (Recibos a Clientes)' : 'Pagamentos (Fornecedores)';
    $icon = $category === 'recebimentos' ? 'fa-hand-holding-usd text-success' : 'fa-money-check-alt text-danger';
    $btnText = $category === 'recebimentos' ? 'Emitir Recibo' : 'Emitir Pagamento';
@endphp
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas {{ $icon }} me-2"></i>{{ $title }}</h2>
            <p class="text-muted mb-0">Gestão de liquidações financeiras e entradas/saídas de tesouraria.</p>
        </div>
        <a href="{{ route('tesouraria.documentos.create', $category) }}" class="btn btn-add-new">
            <i class="fas fa-plus me-2"></i> {{ $btnText }}
        </a>
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

    <div class="card-premium">
        <div class="p-4 border-bottom bg-light">
            <form action="{{ route('tesouraria.documentos.index', $category) }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold mb-1">Pesquisar Entidade ou Nº Doc</label>
                    <input type="text" name="search" class="form-control" placeholder="..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold mb-1">Estado</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="ISSUED" {{ request('status') == 'ISSUED' ? 'selected' : '' }}>Emitido</option>
                        <option value="CANCELLED" {{ request('status') == 'CANCELLED' ? 'selected' : '' }}>Anulado</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1" style="border-radius: 8px;">Filtrar</button>
                    <a href="{{ route('tesouraria.documentos.index', $category) }}" class="btn btn-outline-secondary" style="border-radius: 8px;">Limpar</a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Documento</th>
                        <th>Data</th>
                        <th>Entidade</th>
                        <th>Conta de Tesouraria</th>
                        <th class="text-end">Valor Total</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receipts as $receipt)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold text-dark">{{ $receipt->doc_type }} {{ $receipt->doc_number }}</span>
                        </td>
                        <td>{{ $receipt->date->format('d/m/Y') }}</td>
                        <td>
                            <div class="fw-bold">{{ $receipt->thirdParty->name ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <span class="badge bg-secondary"><i class="fas fa-university me-1"></i> {{ $receipt->treasuryAccount->name ?? 'N/A' }}</span>
                        </td>
                        <td class="text-end fw-bold">
                            {{ number_format($receipt->total_amount, 2, ',', '.') }} {{ $receipt->treasuryAccount->currency ?? 'AOA' }}
                        </td>
                        <td class="text-center">
                            @if($receipt->status === 'ISSUED')
                                <span class="badge badge-issued px-3 py-2 rounded-pill"><i class="fas fa-check me-1"></i> Emitido</span>
                            @else
                                <span class="badge badge-cancelled px-3 py-2 rounded-pill"><i class="fas fa-ban me-1"></i> Anulado</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('tesouraria.documentos.show', ['category' => $category, 'id' => $receipt->id]) }}" class="btn btn-sm btn-light text-primary border" title="Ver Detalhes">
                                <i class="fas fa-eye"></i> Detalhes
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            Nenhum documento financeiro encontrado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">
            {{ $receipts->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    .table-custom thead th {
        background-color: #ffffff;
        color: #475569;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        padding: 1rem 1.5rem;
        border-bottom: 2px solid #e2e8f0;
    }
    .table-custom tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .btn-add-new {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border-radius: 10px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
    }
    .btn-add-new:hover { color: white; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.4); }
    .mov-badge { padding: 0.4em 0.8em; border-radius: 6px; font-weight: 600; font-size: 0.8rem; }
    .type-in { background-color: rgba(16, 185, 129, 0.1); color: #10b981; }
    .type-out { background-color: rgba(239, 68, 68, 0.1); color: #ef4444; }
    .type-transfer { background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .type-adjustment { background-color: rgba(99, 102, 241, 0.1); color: #6366f1; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-exchange-alt text-primary me-2"></i>Movimentos de Stock</h2>
            <p class="text-muted mb-0">Histórico de entradas, saídas e transferências.</p>
        </div>
        <a href="{{ route('inventario.movimentos.create') }}" class="btn btn-add-new">
            <i class="fas fa-plus me-2"></i> Novo Movimento
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm" style="border-radius: 10px;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card-premium">
        <div class="p-4 border-bottom bg-light">
            <form action="{{ route('inventario.movimentos.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold mb-1">Artigo</label>
                    <select name="product_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}" {{ request('product_id') == $prod->id ? 'selected' : '' }}>{{ $prod->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold mb-1">Armazém</label>
                    <select name="warehouse_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold mb-1">Tipo de Movimento</label>
                    <select name="type" class="form-select">
                        <option value="">Todos</option>
                        <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Entrada</option>
                        <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Saída</option>
                        <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>Transferência (Envio/Receção)</option>
                        <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Ajuste</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1" style="border-radius: 8px;">Filtrar</button>
                    @if(request('product_id') || request('warehouse_id') || request('type'))
                        <a href="{{ route('inventario.movimentos.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">Limpar</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th>Data/Hora</th>
                        <th>Artigo</th>
                        <th>Tipo</th>
                        <th>Detalhe / Locais</th>
                        <th class="text-end">Qtd</th>
                        <th>Utilizador</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                    <tr>
                        <td class="text-muted small">
                            <div class="fw-bold text-dark">{{ $movement->created_at->format('d/m/Y') }}</div>
                            {{ $movement->created_at->format('H:i:s') }}
                        </td>
                        <td>
                            <div class="fw-bold text-primary">{{ $movement->product->name }}</div>
                            <small class="text-muted font-monospace">{{ $movement->product->code }}</small>
                        </td>
                        <td>
                            @php
                                $badgeClass = 'type-' . ($movement->type === 'transfer_in' || $movement->type === 'transfer_out' ? 'transfer' : $movement->type);
                            @endphp
                            <span class="mov-badge {{ $badgeClass }}">
                                @if($movement->type === 'in') Entrada
                                @elseif($movement->type === 'out') Saída
                                @elseif($movement->type === 'transfer_in') Receção (Transf.)
                                @elseif($movement->type === 'transfer_out') Envio (Transf.)
                                @elseif($movement->type === 'adjustment') Ajuste
                                @else {{ ucfirst($movement->type) }} @endif
                            </span>
                        </td>
                        <td class="small">
                            @if($movement->fromWarehouse)
                                <div class="text-danger"><i class="fas fa-arrow-right me-1"></i> De: {{ $movement->fromWarehouse->name }}</div>
                            @endif
                            @if($movement->toWarehouse)
                                <div class="text-success"><i class="fas fa-arrow-right me-1"></i> Para: {{ $movement->toWarehouse->name }}</div>
                            @endif
                            @if($movement->notes)
                                <div class="text-muted mt-1 fst-italic border-top pt-1">{{ Str::limit($movement->notes, 30) }}</div>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="fw-bold {{ $movement->type === 'in' || $movement->type === 'transfer_in' || $movement->type === 'adjustment' && $movement->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $movement->type === 'out' || $movement->type === 'transfer_out' ? '-' : '+' }}{{ number_format($movement->quantity, 2, ',', '.') }}
                            </div>
                            <small class="text-muted">Saldo: {{ number_format($movement->balance_after, 2) }}</small>
                        </td>
                        <td>
                            <span class="text-muted small"><i class="fas fa-user-circle me-1"></i> {{ $movement->creator ? $movement->creator->name : 'Auto' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-history fa-2x mb-3 d-block opacity-50"></i>
                            Nenhum movimento de stock encontrado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($movements->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $movements->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection

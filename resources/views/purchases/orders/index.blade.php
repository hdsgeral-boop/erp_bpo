@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
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
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border-radius: 10px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
    }
    .btn-add-new:hover { color: white; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4); }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-file-invoice text-primary me-2"></i>Notas de Encomenda</h2>
            <p class="text-muted mb-0">Documentos oficiais de encomenda a fornecedores.</p>
        </div>
        <a href="{{ route('compras.encomendas.create') }}" class="btn btn-add-new">
            <i class="fas fa-plus me-2"></i> Nova Encomenda
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
            <form action="{{ route('compras.encomendas.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold mb-1">Pesquisar Nº Enc. ou Fornecedor</label>
                    <input type="text" name="search" class="form-control" placeholder="..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold mb-1">Estado</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>Pendente de Envio</option>
                        <option value="APPROVED" {{ request('status') == 'APPROVED' ? 'selected' : '' }}>Aprovada/Enviada</option>
                        <option value="PARTIAL" {{ request('status') == 'PARTIAL' ? 'selected' : '' }}>Parcialmente Recebida</option>
                        <option value="COMPLETED" {{ request('status') == 'COMPLETED' ? 'selected' : '' }}>Totalmente Recebida</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1" style="border-radius: 8px;">Filtrar</button>
                    <a href="{{ route('compras.encomendas.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">Limpar</a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th>Nº Encomenda</th>
                        <th>Fornecedor</th>
                        <th>Data</th>
                        <th>Valor Total</th>
                        <th>Estado</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td class="fw-bold text-primary">{{ $order->order_number }}</td>
                        <td class="fw-bold text-dark">{{ $order->supplier ? $order->supplier->name : 'N/A' }}</td>
                        <td>{{ $order->date->format('d/m/Y') }}</td>
                        <td class="fw-bold">{{ number_format($order->total_amount, 2, ',', '.') }} AOA</td>
                        <td>
                            @if($order->status === 'PENDING')
                                <span class="badge bg-secondary px-2 py-1">Pendente</span>
                            @elseif($order->status === 'APPROVED')
                                <span class="badge bg-primary px-2 py-1">Aprovada/Enviada</span>
                            @elseif($order->status === 'PARTIAL')
                                <span class="badge bg-warning text-dark px-2 py-1"><i class="fas fa-truck-loading me-1"></i> Parcial</span>
                            @elseif($order->status === 'COMPLETED')
                                <span class="badge bg-success px-2 py-1"><i class="fas fa-check-double me-1"></i> Completa</span>
                            @else
                                <span class="badge bg-light text-dark">{{ $order->status }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('compras.encomendas.show', $order->id) }}" class="btn btn-sm btn-light text-primary border" title="Ver Detalhes">
                                <i class="fas fa-eye"></i> Abrir
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-file-invoice fa-2x mb-3 d-block opacity-50"></i>
                            Nenhuma nota de encomenda encontrada.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($orders->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $orders->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection

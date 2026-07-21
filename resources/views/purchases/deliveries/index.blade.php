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
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-truck-loading text-success me-2"></i>Receções de Mercadoria</h2>
            <p class="text-muted mb-0">Histórico de entregas de fornecedores com injeção automática em armazém.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm" style="border-radius: 10px;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card-premium">
        <div class="p-4 border-bottom bg-light">
            <form action="{{ route('compras.rececoes.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label text-muted small fw-bold mb-1">Pesquisar Nº Receção ou Guia</label>
                    <input type="text" name="search" class="form-control" placeholder="..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1" style="border-radius: 8px;">Filtrar</button>
                    <a href="{{ route('compras.rececoes.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">Limpar</a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th>Nº Registo</th>
                        <th>Nº Encomenda</th>
                        <th>Fornecedor</th>
                        <th>Armazém Destino</th>
                        <th>Data</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deliveries as $del)
                    <tr>
                        <td class="fw-bold text-success">{{ $del->delivery_number }}</td>
                        <td>
                            @if($del->order)
                                <a href="{{ route('compras.encomendas.show', $del->order->id) }}">{{ $del->order->order_number }}</a>
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="fw-bold text-dark">{{ $del->order && $del->order->supplier ? $del->order->supplier->name : 'N/A' }}</td>
                        <td>{{ $del->warehouse ? $del->warehouse->name : 'N/A' }}</td>
                        <td>{{ $del->date->format('d/m/Y') }}</td>
                        <td class="text-center">
                            <a href="{{ route('compras.rececoes.show', $del->id) }}" class="btn btn-sm btn-light text-primary border" title="Ver Detalhes">
                                <i class="fas fa-eye"></i> Analisar
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-box-open fa-2x mb-3 d-block opacity-50"></i>
                            Nenhuma receção efetuada.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($deliveries->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $deliveries->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection

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
    .btn-edit {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border-radius: 10px;
        padding: 0.6rem 2rem;
        font-weight: 600;
        border: none;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
    }
    .btn-edit:hover { color: white; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4); }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('inventario.armazens.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
                <i class="fas fa-arrow-left me-1"></i> Voltar à Listagem
            </a>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-pallet text-primary me-2"></i>Armazém: {{ $warehouse->name }}</h2>
            <p class="text-muted"><i class="fas fa-map-marker-alt me-1"></i> {{ $warehouse->location ?: 'Localização não especificada' }}</p>
        </div>
        <div>
            <a href="{{ route('inventario.movimentos.create') }}" class="btn btn-outline-primary fw-bold me-2" style="border-radius: 10px;">
                <i class="fas fa-exchange-alt me-1"></i> Movimentar Stock
            </a>
            <a href="{{ route('inventario.armazens.edit', $warehouse->id) }}" class="btn btn-edit">
                <i class="fas fa-edit me-2"></i> Editar
            </a>
        </div>
    </div>

    <div class="card-premium p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h5 class="fw-bold text-dark m-0">Artigos neste Armazém</h5>
            <span class="badge bg-primary rounded-pill px-3 py-2">{{ $warehouse->stocks->count() }} Artigos</span>
        </div>
        
        @if($warehouse->stocks && $warehouse->stocks->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Artigo</th>
                            <th>Categoria</th>
                            <th class="text-end">Quantidade em Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($warehouse->stocks as $stock)
                            @if($stock->stock_qty > 0)
                            <tr>
                                <td class="font-monospace text-muted">{{ $stock->product->code }}</td>
                                <td class="fw-bold text-primary">
                                    <a href="{{ route('inventario.artigos.show', $stock->product->id) }}" class="text-decoration-none">{{ $stock->product->name }}</a>
                                </td>
                                <td>{{ $stock->product->category ? $stock->product->category->name : '-' }}</td>
                                <td class="text-end fw-bold {{ $stock->stock_qty > 10 ? 'text-success' : 'text-warning' }}">
                                    {{ number_format($stock->stock_qty, 2, ',', '.') }} UN
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-box-open text-muted fa-4x mb-3 opacity-25"></i>
                <h5 class="text-muted">Armazém Vazio</h5>
                <p class="text-muted mb-0">Não existem artigos com stock neste armazém.</p>
            </div>
        @endif
    </div>
</div>
@endsection

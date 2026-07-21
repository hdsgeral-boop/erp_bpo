@extends('layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    .card-premium {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    .table-custom {
        margin-bottom: 0;
    }
    .table-custom thead th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 1rem 1.5rem;
        border-bottom: 2px solid #e2e8f0;
    }
    .table-custom tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-custom tbody tr:hover {
        background-color: #f8fafc;
    }
    .badge-custom {
        padding: 0.5em 1em;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.75rem;
    }
    .badge-service { background: #fef3c7; color: #b45309; }
    .badge-stock { background: #dcfce7; color: #166534; }
    .badge-critical { background: #fee2e2; color: #b91c1c; }
    .badge-blocked { background: #f3f4f6; color: #4b5563; }
    .btn-action {
        border-radius: 8px;
        padding: 0.4rem 0.8rem;
        transition: all 0.2s;
    }
    .btn-action:hover {
        background: #f1f5f9;
        transform: translateY(-2px);
    }
    .btn-add-new {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border-radius: 10px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
        transition: all 0.2s;
    }
    .btn-add-new:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4);
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-box text-primary me-2"></i>Produtos e Serviços</h2>
            <p class="text-muted mb-0">Gestão do Catálogo de Artigos, Preços e Stock.</p>
        </div>
        <a href="{{ route('logistica.products.create') }}" class="btn btn-add-new"><i class="fas fa-plus me-2"></i> Novo Produto</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm" style="border-radius: 10px; border-left: 4px solid #10b981;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card-premium">
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Artigo</th>
                        <th>Categoria</th>
                        <th>Preço (Kz)</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr class="{{ $product->is_blocked ? 'opacity-50' : '' }}">
                        <td class="font-monospace fw-bold text-muted">{{ $product->code }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div style="width: 40px; height: 40px; border-radius: 10px; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; color: #64748b;">
                                    <i class="fas {{ $product->is_inventory ? 'fa-box' : 'fa-tools' }}"></i>
                                </div>
                                <div>
                                    <strong style="color: #0f172a; display: block;">{{ $product->name }}</strong>
                                    @if($product->is_asset)
                                        <small class="text-info"><i class="fas fa-landmark me-1"></i>Imobilizado</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($product->category)
                                <span class="badge bg-light text-dark border"><i class="fas fa-tags me-1 text-muted"></i> {{ $product->category->name }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="fw-bold">{{ number_format($product->unit_price, 2, ',', '.') }}</td>
                        <td>
                            @if($product->is_inventory)
                                @if($product->stock_qty <= 0)
                                    <span class="badge-custom badge-critical"><i class="fas fa-exclamation-triangle me-1"></i> Sem Stock (0)</span>
                                @else
                                    <span class="badge-custom badge-stock"><i class="fas fa-check-circle me-1"></i> {{ $product->stock_qty }} un</span>
                                @endif
                            @else
                                <span class="badge-custom badge-service"><i class="fas fa-concierge-bell me-1"></i> Serviço</span>
                            @endif
                        </td>
                        <td>
                            @if($product->is_blocked)
                                <span class="badge-custom badge-blocked"><i class="fas fa-ban me-1"></i> Bloqueado</span>
                            @else
                                <span class="badge-custom badge-stock"><i class="fas fa-check me-1"></i> Ativo</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('logistica.products.edit', $product) }}" class="btn btn-action text-primary" title="Editar"><i class="fas fa-edit"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3" style="opacity: 0.5;"></i>
                            <h5 class="text-muted">Nenhum produto registado no catálogo.</h5>
                            <a href="{{ route('logistica.products.create') }}" class="btn btn-outline-primary mt-2">Criar Primeiro Produto</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

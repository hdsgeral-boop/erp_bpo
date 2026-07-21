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
    .btn-action {
        border-radius: 8px;
        padding: 0.4rem 0.8rem;
        transition: all 0.2s;
    }
    .btn-action:hover { background: #f1f5f9; transform: translateY(-2px); }
    .btn-add-new {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border-radius: 10px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
    }
    .btn-add-new:hover { color: white; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4); }
    .stock-badge { padding: 0.4em 0.8em; border-radius: 6px; font-weight: 600; }
    .stock-ok { background-color: rgba(16, 185, 129, 0.1); color: #10b981; }
    .stock-low { background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .stock-empty { background-color: rgba(239, 68, 68, 0.1); color: #ef4444; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-boxes text-primary me-2"></i>Gestão de Artigos</h2>
            <p class="text-muted mb-0">Catálogo de produtos, consumíveis e matérias-primas.</p>
        </div>
        <a href="{{ route('inventario.artigos.create') }}" class="btn btn-add-new">
            <i class="fas fa-plus me-2"></i> Novo Artigo
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm" style="border-radius: 10px; border-left: 4px solid #10b981;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger shadow-sm" style="border-radius: 10px; border-left: 4px solid #ef4444;">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card-premium">
        <div class="p-4 border-bottom bg-light">
            <form action="{{ route('inventario.artigos.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold mb-1">Pesquisa</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Código ou Nome..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold mb-1">Categoria</label>
                    <select name="category_id" class="form-select">
                        <option value="">Todas as Categorias</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1" style="border-radius: 8px;">Filtrar</button>
                    @if(request('search') || request('category_id'))
                        <a href="{{ route('inventario.artigos.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">Limpar</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th>Artigo</th>
                        <th>Categoria</th>
                        <th>Preço Base</th>
                        <th>Stock Global</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 rounded p-2 me-3 text-primary">
                                    <i class="fas fa-box fa-lg"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $product->name }}</div>
                                    <small class="text-muted font-monospace">COD: {{ $product->code }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $product->category ? $product->category->name : 'Sem Categoria' }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ number_format($product->unit_price, 2, ',', '.') }} AOA</div>
                            <small class="text-muted">IVA: {{ number_format($product->tax_rate, 2) }}%</small>
                        </td>
                        <td>
                            @php
                                $stockClass = $product->stock_qty > 10 ? 'stock-ok' : ($product->stock_qty > 0 ? 'stock-low' : 'stock-empty');
                            @endphp
                            <span class="stock-badge {{ $stockClass }}">
                                {{ number_format($product->stock_qty, 2, ',', '.') }} UN
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('inventario.artigos.show', $product->id) }}" class="btn btn-sm btn-action text-info" title="Ver Perfil">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('inventario.artigos.edit', $product->id) }}" class="btn btn-sm btn-action text-primary" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($product->stock_qty <= 0)
                            <form action="{{ route('inventario.artigos.destroy', $product->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-action text-danger" title="Eliminar Artigo" onclick="return confirm('Tem certeza que deseja eliminar este artigo?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-box-open fa-2x mb-3 d-block"></i>
                            Nenhum artigo encontrado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($products->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection

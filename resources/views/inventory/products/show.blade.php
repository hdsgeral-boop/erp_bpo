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
    .info-value {
        font-size: 1.05rem;
        color: #1e293b;
        font-weight: 500;
        margin-bottom: 1.5rem;
    }
    .product-icon-large {
        width: 120px;
        height: 120px;
        border-radius: 20px;
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        color: #3b82f6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3.5rem;
        margin: 0 auto;
        box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
        overflow: hidden;
    }
    .product-icon-large img {
        width: 100%;
        height: 100%;
        object-fit: cover;
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
    
    .timeline {
        border-left: 2px solid #e2e8f0;
        padding-left: 20px;
        margin-left: 10px;
        position: relative;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -27px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: #3b82f6;
        border: 2px solid #ffffff;
        box-shadow: 0 0 0 2px #bfdbfe;
    }
    .timeline-item.type-in::before { background-color: #10b981; box-shadow: 0 0 0 2px #a7f3d0; }
    .timeline-item.type-out::before { background-color: #ef4444; box-shadow: 0 0 0 2px #fecaca; }
    .timeline-item.type-transfer::before { background-color: #f59e0b; box-shadow: 0 0 0 2px #fde68a; }
    .timeline-item.type-adjustment::before { background-color: #6366f1; box-shadow: 0 0 0 2px #c7d2fe; }
    
    .stock-ok { color: #10b981; }
    .stock-low { color: #f59e0b; }
    .stock-empty { color: #ef4444; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('inventario.artigos.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
                <i class="fas fa-arrow-left me-1"></i> Voltar à Listagem
            </a>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-box text-primary me-2"></i>Perfil do Artigo</h2>
        </div>
        <a href="{{ route('inventario.artigos.edit', $product->id) }}" class="btn btn-edit">
            <i class="fas fa-edit me-2"></i> Editar Artigo
        </a>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card-premium p-4 text-center h-100 d-flex flex-column align-items-center">
                <div class="product-icon-large mb-4 border border-white border-4 shadow-sm">
                    @php
                        $image = $product->attachments ? $product->attachments->first(fn($att) => str_contains($att->file_type, 'image')) : null;
                    @endphp
                    @if($image)
                        <img src="{{ Storage::url($image->file_path) }}" alt="{{ $product->name }}">
                    @else
                        <i class="fas fa-box-open"></i>
                    @endif
                </div>
                <h4 class="fw-bold text-dark mb-1">{{ $product->name }}</h4>
                <p class="text-primary font-monospace fw-bold mb-1">{{ $product->code }}</p>
                <p class="text-muted mb-4">{{ $product->category ? $product->category->name : 'Sem Categoria' }}</p>
                
                <div class="d-flex justify-content-center gap-2 mb-4">
                    @if($product->is_inventory)
                        <span class="badge bg-info bg-opacity-10 text-info border border-info px-3 py-2"><i class="fas fa-cubes me-1"></i>Controla Stock</span>
                    @endif
                    @if($product->is_blocked)
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2"><i class="fas fa-lock me-1"></i>Bloqueado</span>
                    @endif
                </div>

                <div class="w-100 border-top pt-4 text-start mt-auto">
                    <div class="p-3 bg-light rounded border border-primary mb-3">
                        <div class="info-label text-primary"><i class="fas fa-layer-group me-1"></i> Stock Global Atual</div>
                        @php
                            $stockClass = $product->stock_qty > 10 ? 'stock-ok' : ($product->stock_qty > 0 ? 'stock-low' : 'stock-empty');
                        @endphp
                        <div class="fs-1 fw-bold {{ $stockClass }} mb-1 text-center py-2">{{ number_format($product->stock_qty, 2, ',', '.') }} <span class="fs-5 text-muted">UN</span></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card-premium p-4 p-md-5 h-100">
                <ul class="nav nav-pills mb-4 border-bottom pb-3" id="productTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold" id="geral-tab" data-bs-toggle="pill" data-bs-target="#geral" type="button" role="tab">Informações</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="stock-tab" data-bs-toggle="pill" data-bs-target="#stock" type="button" role="tab">Stock por Armazém</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="historico-tab" data-bs-toggle="pill" data-bs-target="#historico" type="button" role="tab">Extrato de Movimentos</button>
                    </li>
                </ul>

                <div class="tab-content" id="productTabsContent">
                    <!-- Informações Gerais -->
                    <div class="tab-pane fade show active" id="geral" role="tabpanel">
                        <div class="row">
                            <div class="col-12 mb-3"><h6 class="fw-bold text-dark border-bottom pb-2">Preços e Contabilidade</h6></div>
                            <div class="col-md-6">
                                <div class="info-label">Preço Base (Sem IVA)</div>
                                <div class="info-value text-primary fw-bold">{{ number_format($product->unit_price, 2, ',', '.') }} AOA</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Taxa de IVA</div>
                                <div class="info-value">{{ number_format($product->tax_rate, 2) }} %</div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="info-label">Conta Base</div>
                                <div class="info-value">{{ $product->account_code ?: '-' }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-label">Conta Compras</div>
                                <div class="info-value">{{ $product->account_purchase ?: '-' }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-label">Conta Custos</div>
                                <div class="info-value">{{ $product->account_cost ?: '-' }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-label">Conta Inventário</div>
                                <div class="info-value">{{ $product->account_inventory ?: '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Stock por Armazém -->
                    <div class="tab-pane fade" id="stock" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="fw-bold text-dark m-0">Distribuição Física</h6>
                            <a href="{{ route('inventario.movimentos.create') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Novo Movimento</a>
                        </div>
                        
                        @if($product->stocks && $product->stocks->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover border">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Armazém</th>
                                            <th>Localização</th>
                                            <th class="text-end">Quantidade Existente</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($product->stocks as $stock)
                                        <tr>
                                            <td class="fw-bold"><i class="fas fa-warehouse text-muted me-2"></i> {{ $stock->warehouse->name }}</td>
                                            <td class="text-muted">{{ $stock->warehouse->location ?: '-' }}</td>
                                            <td class="text-end fw-bold text-primary">{{ number_format($stock->stock_qty, 2, ',', '.') }} UN</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-pallet text-muted fa-3x mb-3 opacity-50"></i>
                                <h5 class="text-muted">Sem Stock Registado</h5>
                                <p class="text-muted mb-0">Este artigo não tem stock em nenhum armazém.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Histórico / Extrato -->
                    <div class="tab-pane fade" id="historico" role="tabpanel">
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark mb-3">Últimas Movimentações</h6>
                            
                            @if($product->movements && $product->movements->count() > 0)
                                <div class="timeline">
                                    @foreach($product->movements->take(20) as $movement)
                                        @php
                                            $typeClass = 'type-' . ($movement->type === 'transfer_in' || $movement->type === 'transfer_out' ? 'transfer' : $movement->type);
                                        @endphp
                                        <div class="timeline-item {{ $typeClass }}">
                                            <div class="d-flex justify-content-between">
                                                <div class="fw-bold text-dark">{{ $movement->created_at->format('d/m/Y H:i') }}</div>
                                                <div class="fw-bold {{ $movement->type === 'in' || $movement->type === 'transfer_in' || $movement->type === 'adjustment' && $movement->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $movement->type === 'out' || $movement->type === 'transfer_out' ? '-' : '+' }}{{ number_format($movement->quantity, 2, ',', '.') }}
                                                </div>
                                            </div>
                                            
                                            <div class="small text-muted mb-1">
                                                @if($movement->type === 'in') Entrada
                                                @elseif($movement->type === 'out') Saída
                                                @elseif($movement->type === 'transfer_in') Receção de Transferência
                                                @elseif($movement->type === 'transfer_out') Envio de Transferência
                                                @elseif($movement->type === 'adjustment') Ajuste de Stock
                                                @else {{ ucfirst($movement->type) }} @endif
                                            </div>
                                            
                                            <div class="bg-light p-3 rounded border mt-2">
                                                <div class="row g-2 small">
                                                    @if($movement->fromWarehouse)
                                                        <div class="col-sm-6 text-danger"><i class="fas fa-sign-out-alt me-1"></i> De: {{ $movement->fromWarehouse->name }}</div>
                                                    @endif
                                                    @if($movement->toWarehouse)
                                                        <div class="col-sm-6 text-success"><i class="fas fa-sign-in-alt me-1"></i> Para: {{ $movement->toWarehouse->name }}</div>
                                                    @endif
                                                </div>
                                                
                                                @if($movement->notes)
                                                    <div class="mt-2 pt-2 border-top small text-muted">
                                                        <i class="fas fa-comment-alt me-1"></i> {{ $movement->notes }}
                                                    </div>
                                                @endif
                                                
                                                <div class="d-flex justify-content-between mt-2 pt-2 border-top small">
                                                    <span class="text-muted"><i class="fas fa-user me-1"></i> {{ $movement->creator ? $movement->creator->name : 'Sistema' }}</span>
                                                    <span class="fw-bold text-primary">Saldo após: {{ number_format($movement->balance_after, 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="text-center mt-3"><small class="text-muted">A mostrar os últimos 20 movimentos.</small></div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-history text-muted fa-3x mb-3 opacity-50"></i>
                                    <p class="text-muted">Sem movimentos registados para este artigo.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

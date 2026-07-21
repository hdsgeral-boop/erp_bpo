@extends('layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f8fafc;
    }
    .product-card-wrap {
        perspective: 1000px;
    }
    .product-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 16px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    .product-card:hover {
        border-color: #3b82f6;
        box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.15);
        transform: translateY(-5px);
    }
    .product-card i.fa-box {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .cart-sidebar {
        border-radius: 20px;
        border: none;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    .cart-sidebar .card-header {
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        padding: 20px;
    }
    .btn-checkout {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
        box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);
        transition: all 0.3s;
        border-radius: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .btn-checkout:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 15px 20px -5px rgba(16, 185, 129, 0.4);
        background: linear-gradient(135deg, #34d399, #10b981);
    }
    .cart-item-row {
        animation: slideIn 0.3s ease-out forwards;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        margin-bottom: 10px;
        padding: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateX(10px); }
        to { opacity: 1; transform: translateX(0); }
    }
    #posSearch {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    #posSearch:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-cash-register"></i> POS de Armazém</h2>
            <p class="text-muted">Atendimento ao balcão e saídas rápidas de material.</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('logistica.pos.balcao') }}" class="btn btn-primary"><i class="fas fa-desktop"></i> Atendimento ao Balcão</a>
            <a href="{{ route('logistica.pos.picking') }}" class="btn btn-outline-primary"><i class="fas fa-clipboard-list"></i> Fila de Picking</a>
        </div>
    </div>

    @if($warehouses->isEmpty())
        <div class="alert alert-warning text-center py-5">
            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
            <h3>Nenhum Armazém Configurado</h3>
            <p>Por favor, configure pelo menos um armazém antes de aceder ao POS.</p>
            <a href="{{ route('logistica.warehouses.index') }}" class="btn btn-primary mt-3">Configurar Armazéns</a>
        </div>
    @else
        <div class="row">
            <!-- Catálogo de Produtos -->
            <div class="col-md-8">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <form method="GET" action="{{ route('logistica.pos.balcao') }}" class="d-flex gap-3 align-items-center">
                            <i class="fas fa-boxes text-primary fa-lg"></i>
                            <select name="warehouse_id" class="form-select w-auto fw-bold" onchange="this.form.submit()">
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}" {{ $activeWhId == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                                @endforeach
                            </select>
                            <input type="text" id="posSearch" class="form-control" placeholder="Pesquisar produto..." onkeyup="filterProducts()">
                        </form>
                    </div>
                    <div class="card-body bg-light" style="max-height: 700px; overflow-y: auto;">
                        <div class="row g-3" id="productGrid">
                            @forelse($products as $p)
                                @php
                                    $qty = $stocks->get($p->id)->stock_qty ?? 0;
                                    $color = $qty > 10 ? 'success' : ($qty > 0 ? 'warning' : 'danger');
                                @endphp
                                <div class="col-md-4 col-sm-6 product-card-wrap" data-name="{{ strtolower($p->name) }}" data-code="{{ strtolower($p->code) }}">
                                    <div class="card h-100 product-card text-center position-relative">
                                        <span class="badge bg-{{ $color }} position-absolute top-0 start-0 m-3 shadow-sm rounded-pill px-3 py-2">
                                            {{ $qty > 0 ? $qty . ' U' : 'Esgotado' }}
                                        </span>
                                        <div class="card-body mt-4 d-flex flex-column justify-content-between">
                                            <div>
                                                <i class="fas fa-box fa-3x mb-3 mt-2"></i>
                                                <h6 class="card-title fw-bold text-truncate" title="{{ $p->name }}" style="color: #1e293b; font-size: 0.95rem;">{{ $p->name }}</h6>
                                                <p class="small text-muted mb-3 font-monospace bg-light rounded d-inline-block px-2 py-1">{{ $p->code }}</p>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-sm w-100 fw-bold rounded-pill shadow-sm" style="transition: all 0.2s;"
                                                onclick="addToCart({{ $p->id }}, '{{ addslashes($p->name) }}', {{ $p->unit_price ?? 0 }}, {{ $qty }})"
                                                {{ $qty <= 0 ? 'disabled' : '' }}>
                                                <i class="fas fa-plus"></i> Adicionar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center text-muted py-5">
                                    <i class="fas fa-search fa-2x mb-3"></i>
                                    <p>Nenhum produto em stock ou registado.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carrinho Lateral -->
            <div class="col-md-4">
                <div class="card cart-sidebar h-100">
                    <div class="card-header d-flex align-items-center gap-2">
                        <i class="fas fa-shopping-cart text-primary fa-lg"></i>
                        <h5 class="mb-0 fw-bold text-dark">Carrinho de Picking</h5>
                    </div>
                    <div class="card-body d-flex flex-column bg-light">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Cliente / Destinatário</label>
                            <select id="posClient" class="form-select shadow-sm border-0 py-2">
                                <option value="">-- Cliente de Balcão (Geral) --</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->nif ?? 'NIF -' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="cartItems" class="flex-grow-1 overflow-auto mb-3 px-1" style="min-height: 350px;">
                            <div class="text-center text-muted mt-5" id="emptyCart" style="opacity: 0.6;">
                                <i class="fas fa-shopping-basket fa-4x mb-3 text-secondary"></i>
                                <p class="fw-bold">Carrinho vazio.</p>
                                <small>Adicione produtos da lista ao lado.</small>
                            </div>
                        </div>

                        <button type="button" class="btn btn-checkout text-white py-3 fs-5 mt-auto" onclick="checkout()" id="btnCheckout" disabled>
                            <i class="fas fa-check-circle me-2"></i> Confirmar Saída
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    let cart = {};

    function filterProducts() {
        const query = document.getElementById('posSearch').value.toLowerCase();
        document.querySelectorAll('.product-card').forEach(card => {
            const name = card.dataset.name;
            const code = card.dataset.code;
            if (name.includes(query) || code.includes(query)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function addToCart(id, name, price, maxQty) {
        if (!cart[id]) {
            cart[id] = { name: name, price: price, qty: 1, maxQty: maxQty };
        } else {
            if (cart[id].qty < maxQty) {
                cart[id].qty++;
            } else {
                alert(`Apenas ${maxQty} disponíveis em stock.`);
            }
        }
        renderCart();
    }

    function updateQty(id, qty) {
        qty = parseFloat(qty);
        if (qty > cart[id].maxQty) {
            alert(`Excede stock disponível (${cart[id].maxQty}).`);
            cart[id].qty = cart[id].maxQty;
        } else if (qty <= 0) {
            delete cart[id];
        } else {
            cart[id].qty = qty;
        }
        renderCart();
    }

    function renderCart() {
        const container = document.getElementById('cartItems');
        const emptyMsg = document.getElementById('emptyCart');
        const btnCheckout = document.getElementById('btnCheckout');
        
        const keys = Object.keys(cart);
        if (keys.length === 0) {
            container.innerHTML = '';
            container.appendChild(emptyMsg);
            emptyMsg.style.display = 'block';
            btnCheckout.disabled = true;
            return;
        }

        emptyMsg.style.display = 'none';
        btnCheckout.disabled = false;
        
        let html = '';
        keys.forEach(k => {
            const item = cart[k];
            html += `
                <div class="cart-item-row d-flex justify-content-between align-items-center">
                    <div class="text-truncate flex-grow-1 pe-3">
                        <strong class="d-block text-truncate text-dark" style="font-size: 0.95rem;" title="${item.name}">${item.name}</strong>
                    </div>
                    <div class="d-flex align-items-center gap-2 bg-light p-1 rounded-pill border">
                        <button class="btn btn-sm btn-link text-muted p-0 ms-2" onclick="updateQty(${k}, ${item.qty - 1})"><i class="fas fa-minus"></i></button>
                        <input type="number" class="form-control form-control-sm text-center border-0 bg-transparent p-0" value="${item.qty}" min="1" max="${item.maxQty}" onchange="updateQty(${k}, this.value)" style="width: 35px; font-weight: 700; color: #3b82f6;">
                        <button class="btn btn-sm btn-link text-muted p-0" onclick="updateQty(${k}, ${item.qty + 1})"><i class="fas fa-plus"></i></button>
                        <button class="btn btn-sm btn-danger rounded-circle ms-1" style="width: 24px; height: 24px; padding: 0; display: flex; align-items: center; justify-content: center;" onclick="updateQty(${k}, 0)"><i class="fas fa-times" style="font-size: 10px;"></i></button>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    function checkout() {
        const keys = Object.keys(cart);
        if(keys.length === 0) return;

        if(!confirm('Deseja confirmar a saída destas mercadorias e emitir a Guia de Saída?')) return;

        const payload = {
            warehouse_id: '{{ $activeWhId }}',
            client_id: document.getElementById('posClient').value,
            cart: keys.map(k => ({ product_id: k, quantity: cart[k].qty }))
        };

        document.getElementById('btnCheckout').disabled = true;
        document.getElementById('btnCheckout').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processando...';

        fetch('{{ route('logistica.pos.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Erro: ' + data.message);
                document.getElementById('btnCheckout').disabled = false;
                document.getElementById('btnCheckout').innerHTML = '<i class="fas fa-check-circle"></i> Confirmar Saída';
            }
        })
        .catch(err => {
            console.error(err);
            alert('Erro de rede.');
            document.getElementById('btnCheckout').disabled = false;
            document.getElementById('btnCheckout').innerHTML = '<i class="fas fa-check-circle"></i> Confirmar Saída';
        });
    }
</script>
@endsection

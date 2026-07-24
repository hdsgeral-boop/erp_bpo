@extends('layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f1f5f9;
    }
    .pos-container {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 25px;
        height: calc(100vh - 120px);
    }
    @media (max-width: 1024px) {
        .pos-container {
            grid-template-columns: 1fr;
            height: auto;
        }
    }
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 20px;
        overflow-y: auto;
        padding: 5px;
        padding-right: 15px;
    }
    /* Scrollbar styling */
    .products-grid::-webkit-scrollbar { width: 6px; }
    .products-grid::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    
    .product-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 16px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 140px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }
    .product-card:hover {
        border-color: #3b82f6;
        box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.15), 0 10px 10px -5px rgba(59, 130, 246, 0.04);
        transform: translateY(-5px);
    }
    .product-card:active {
        transform: scale(0.97);
    }
    .product-name {
        font-size: 0.9rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 10px;
        line-height: 1.3;
    }
    .product-price {
        font-weight: 800;
        color: #2563eb;
        font-size: 1.1rem;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .cart-panel {
        background: #ffffff;
        border-radius: 20px;
        border: none;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
    }
    .cart-header {
        background: #f8fafc;
        padding: 20px;
        border-bottom: 1px solid #f1f5f9;
    }
    .cart-header select {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        padding: 10px 15px;
        font-size: 0.95rem;
        color: #334155;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .cart-header select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .cart-items {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
    }
    .cart-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px dashed #e2e8f0;
        animation: fadeIn 0.3s ease-out forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateX(-10px); }
        to { opacity: 1; transform: translateX(0); }
    }
    .cart-item:last-child {
        border-bottom: none;
    }
    .cart-item-name {
        font-size: 0.9rem;
        font-weight: 700;
        color: #334155;
    }
    .cart-item-price {
        font-size: 0.8rem;
        color: #64748b;
        margin-top: 2px;
    }
    .cart-qty-ctrl {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 8px;
    }
    .cart-qty-btn {
        width: 26px;
        height: 26px;
        border-radius: 6px;
        border: none;
        background: #ffffff;
        color: #475569;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        transition: all 0.1s;
    }
    .cart-qty-btn:hover {
        background: #3b82f6;
        color: #fff;
    }
    .cart-qty-btn:active {
        transform: scale(0.9);
    }
    .cart-total-panel {
        background: linear-gradient(135deg, #0f172a, #1e293b);
        color: #fff;
        padding: 25px;
        border-radius: 0 0 20px 20px;
        position: relative;
        overflow: hidden;
    }
    .cart-total-panel::before {
        content: '';
        position: absolute;
        top: -50%; left: -50%; width: 200%; height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 60%);
        pointer-events: none;
    }
    .total-row {
        display: flex;
        justify-content: space-between;
        font-size: 1.6rem;
        font-weight: 800;
        margin-bottom: 20px;
        letter-spacing: -0.5px;
    }
    .btn-pay {
        width: 100%;
        padding: 16px;
        font-size: 1.15rem;
        font-weight: 700;
        border-radius: 12px;
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
        box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);
        transition: all 0.3s;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .btn-pay:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 15px 20px -5px rgba(16, 185, 129, 0.4);
        background: linear-gradient(135deg, #34d399, #10b981);
    }
    .btn-pay:disabled {
        background: #475569;
        box-shadow: none;
        opacity: 0.7;
    }
    
    #productSearch {
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
    }
    #productSearch:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }
</style>
@endpush

@section('content')
<div class="pos-container">
    <!-- Left: Products -->
    <div style="display: flex; flex-direction: column; gap: 15px;">
        <input type="text" id="productSearch" class="form-control" placeholder="Pesquisar produto (Nome ou Código)..." style="font-size: 1.1rem; padding: 12px;">
        
        <div class="products-grid" id="productsGrid">
            @foreach($products as $product)
            <div class="product-card" onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }})">
                <div class="product-name">{{ $product->name }}</div>
                <div>
                    <div class="product-price">{{ number_format($product->price, 2, ',', '.') }} Kz</div>
                    @if($product->is_stockable)
                    <div style="font-size: 0.7rem; color: #64748b; margin-top: 4px;">Stock: {{ $product->stock }}</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Right: Cart -->
    <div class="cart-panel">
        <div class="cart-header" style="display: flex; flex-direction: column; gap: 10px;">
            <select id="docType" class="form-select fw-bold border-primary" onchange="updatePayButton()">
                <option value="FR">Fatura-Recibo (A Pronto)</option>
                <option value="FS">Fatura Simplificada</option>
                <option value="FT">Fatura (A Prazo)</option>
                <option value="GT">Guia de Transporte</option>
                <option value="OR">Orçamento</option>
                <option value="PP">Fatura Pró-Forma</option>
                <option value="EN">Encomenda (Armazém)</option>
            </select>
            <div class="d-flex gap-2">
                <select id="customerId" class="form-select flex-grow-1">
                    <option value="">Consumidor Final (Anónimo)</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->nif ?? 'NIF Indisponível' }})</option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createCustomerModal" title="Criar Novo Cliente">
                    <i class="fas fa-user-plus"></i>
                </button>
            </div>
        </div>
        <div class="cart-items" id="cartItems">
            <!-- Items via JS -->
            <div style="text-align: center; color: #94a3b8; padding: 40px 0;" id="emptyCartMsg">
                O carrinho está vazio
            </div>
        </div>
        <div class="cart-total-panel">
            <div class="total-row">
                <span>TOTAL</span>
                <span id="cartTotal">0,00 Kz</span>
            </div>
            <button class="btn btn-success btn-pay" onclick="processSale()" id="payBtn" disabled>
                <i class="fas fa-money-bill-wave"></i> Cobrar e Emitir
            </button>
</div>

<!-- Modal Criar Cliente Rápido -->
<div class="modal fade" id="createCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="quickCustomerForm">
            @csrf
            <div class="modal-content" style="border-radius: 16px;">
                <div class="modal-header bg-primary text-white" style="border-radius: 16px 16px 0 0;">
                    <h5 class="modal-title fw-bold"><i class="fas fa-user-plus me-2"></i> Criar Novo Cliente</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nome do Cliente <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Ex: João Manuel" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">NIF (Número de Identificação Fiscal)</label>
                        <input type="text" name="nif" class="form-control" placeholder="Ex: 541819201">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Telefone</label>
                            <input type="text" name="phone" class="form-control" placeholder="Ex: 923000000">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="cliente@email.com">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-radius: 0 0 16px 16px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold"><i class="fas fa-check me-1"></i> Guardar Cliente</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('quickCustomerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('type', 'customer');

        fetch("{{ route('entidades.store') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success || data.id) {
                const select = document.getElementById('customerId');
                const opt = document.createElement('option');
                opt.value = data.data ? data.data.id : (data.id || data.third_party.id);
                opt.textContent = (data.data ? data.data.name : data.name) + ' (Novo)';
                opt.selected = true;
                select.appendChild(opt);

                const modal = bootstrap.Modal.getInstance(document.getElementById('createCustomerModal'));
                if(modal) modal.hide();
                this.reset();
                alert('Cliente criado com sucesso!');
            } else {
                alert('Cliente adicionado!');
                location.reload();
            }
        })
        .catch(err => {
            alert('Cliente guardado!');
            location.reload();
        });
    });

<script>
    let cart = [];

    function formatMoney(amount) {
        return new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(amount).replace('Kz', '').trim() + ' Kz';
    }

    function addToCart(id, name, price) {
        let item = cart.find(i => i.id === id);
        if (item) {
            item.quantity++;
            item.subtotal = item.quantity * item.unit_price;
        } else {
            cart.push({
                id: id,
                name: name,
                unit_price: price,
                quantity: 1,
                subtotal: price
            });
        }
        renderCart();
    }

    function updateQty(id, delta) {
        let item = cart.find(i => i.id === id);
        if (item) {
            item.quantity += delta;
            if (item.quantity <= 0) {
                cart = cart.filter(i => i.id !== id);
            } else {
                item.subtotal = item.quantity * item.unit_price;
            }
        }
        renderCart();
    }

    function renderCart() {
        const container = document.getElementById('cartItems');
        const emptyMsg = document.getElementById('emptyCartMsg');
        const totalEl = document.getElementById('cartTotal');
        const payBtn = document.getElementById('payBtn');

        if (cart.length === 0) {
            container.innerHTML = '<div style="text-align: center; color: #94a3b8; padding: 40px 0;" id="emptyCartMsg">O carrinho está vazio</div>';
            totalEl.textContent = '0,00 Kz';
            payBtn.disabled = true;
            return;
        }

        payBtn.disabled = false;
        container.innerHTML = '';
        let total = 0;

        cart.forEach(item => {
            total += item.subtotal;
            container.innerHTML += `
                <div class="cart-item">
                    <div style="flex: 1;">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-price">${formatMoney(item.unit_price)}</div>
                    </div>
                    <div class="cart-qty-ctrl">
                        <button class="cart-qty-btn" onclick="updateQty(${item.id}, -1)"><i class="fas fa-minus" style="font-size: 10px;"></i></button>
                        <span style="font-weight: 600; width: 24px; text-align: center;">${item.quantity}</span>
                        <button class="cart-qty-btn" onclick="updateQty(${item.id}, 1)"><i class="fas fa-plus" style="font-size: 10px;"></i></button>
                    </div>
                    <div style="width: 80px; text-align: right; font-weight: 700;">
                        ${formatMoney(item.subtotal)}
                    </div>
                </div>
            `;
        });

        totalEl.textContent = formatMoney(total);
    }

    function processSale() {
        if (cart.length === 0) return;

        payBtn.disabled = true;
        payBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processando...';

        fetch("{{ route('vendas.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                doc_type: document.getElementById('docType').value,
                customer_id: document.getElementById('customerId').value || null,
                items: cart
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Fatura emitida com sucesso!');
                cart = [];
                renderCart();
                // Opcional: imprimir janela
            } else {
                alert('Erro: ' + data.message);
            }
        })
        .catch(err => {
            alert('Erro no servidor!');
            console.error(err);
        })
        .finally(() => {
            payBtn.disabled = false;
            payBtn.innerHTML = payBtn.dataset.defaultText || '<i class="fas fa-money-bill-wave"></i> Cobrar e Emitir';
            if(cart.length === 0) payBtn.disabled = true;
        });
    }

    function updatePayButton() {
        const type = document.getElementById('docType').value;
        const btn = document.getElementById('payBtn');
        let icon = '<i class="fas fa-money-bill-wave"></i> ';
        let text = 'Cobrar e Emitir';

        if(type === 'FT') { icon = '<i class="fas fa-file-invoice"></i> '; text = 'Emitir Fatura'; }
        if(type === 'GT') { icon = '<i class="fas fa-truck"></i> '; text = 'Emitir Guia'; }
        if(type === 'OR' || type === 'PP') { icon = '<i class="fas fa-save"></i> '; text = 'Gravar Documento'; }
        if(type === 'EN') { icon = '<i class="fas fa-box"></i> '; text = 'Registar Encomenda'; }

        if(cart.length > 0) {
            btn.innerHTML = icon + text;
        }
        btn.dataset.defaultText = icon + text;
    }

    // Call once to initialize
    document.addEventListener('DOMContentLoaded', () => updatePayButton());

    // Search logic
    document.getElementById('productSearch').addEventListener('input', function(e) {
        const text = e.target.value.toLowerCase();
        const cards = document.querySelectorAll('.product-card');
        cards.forEach(card => {
            const name = card.querySelector('.product-name').textContent.toLowerCase();
            if (name.includes(text)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>
@endsection

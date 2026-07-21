@extends('layouts.app')

@section('content')
<!-- Esconder Sidebar e Topbar clássicos para modo ecrã inteiro -->
<style>
    .sidebar { display: none !important; }
    .topbar { display: none !important; }
    .main-content { margin-left: 0 !important; padding-top: 0 !important; }
    body { background-color: #f4f6f9; overflow-y: hidden; }
    
    .pos-layout {
        display: flex;
        height: 100vh;
        width: 100vw;
    }
    .pos-products {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #fff;
    }
    .pos-sidebar {
        width: 400px;
        background: #fff;
        border-left: 1px solid #e0e0e0;
        display: flex;
        flex-direction: column;
        box-shadow: -4px 0 15px rgba(0,0,0,0.05);
        z-index: 10;
    }
    .pos-header {
        height: 70px;
        background: #2c3e50;
        color: white;
        display: flex;
        align-items: center;
        padding: 0 20px;
        justify-content: space-between;
    }
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 15px;
        padding: 20px;
        overflow-y: auto;
        flex: 1;
        background: #f8f9fa;
    }
    .product-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        cursor: pointer;
        transition: transform 0.1s, box-shadow 0.1s;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid #eee;
    }
    .product-card:active {
        transform: scale(0.96);
    }
    .product-img {
        height: 100px;
        background-color: #f1f3f5;
        background-size: cover;
        background-position: center;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #adb5bd;
    }
    .product-info {
        padding: 10px;
        text-align: center;
    }
    .product-name {
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 5px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 38px;
    }
    .product-price {
        color: #e74c3c;
        font-weight: bold;
        font-size: 1rem;
    }
    .cart-items {
        flex: 1;
        overflow-y: auto;
        padding: 0;
        margin: 0;
        list-style: none;
    }
    .cart-item {
        padding: 15px 20px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .cart-item-info {
        flex: 1;
    }
    .cart-item-title {
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 5px;
    }
    .cart-item-price {
        font-size: 0.85rem;
        color: #6c757d;
    }
    .cart-item-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .qty-btn {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 1px solid #dee2e6;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #495057;
    }
    .qty-btn:active { background: #e9ecef; }
    .cart-summary {
        padding: 20px;
        background: #f8f9fa;
        border-top: 1px solid #e0e0e0;
    }
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 0.95rem;
    }
    .summary-total {
        display: flex;
        justify-content: space-between;
        font-size: 1.4rem;
        font-weight: bold;
        color: #2c3e50;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 2px dashed #dee2e6;
    }
    .pay-btn {
        height: 60px;
        font-size: 1.2rem;
        font-weight: bold;
        border-radius: 10px;
        background: #27ae60;
        color: white;
        border: none;
        width: 100%;
        margin-top: 15px;
        transition: background 0.2s;
    }
    .pay-btn:hover { background: #219653; }
    .cat-pill {
        cursor: pointer;
        padding: 8px 16px;
        border-radius: 20px;
        background: #e9ecef;
        color: #495057;
        font-weight: 500;
        white-space: nowrap;
        transition: all 0.2s;
    }
    .cat-pill.active {
        background: #3498db;
        color: white;
    }
</style>

<div class="pos-layout" id="posApp">
    <!-- Left Area: Products -->
    <div class="pos-products">
        <div class="pos-header">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('dashboard') }}" class="text-white text-decoration-none"><i class="fas fa-arrow-left fa-lg"></i></a>
                <h4 class="mb-0">Frente de Caixa</h4>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-0" id="searchInput" placeholder="Pesquisar artigo ou código...">
                </div>
                <button type="button" class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#closeSessionModal">
                    <i class="fas fa-lock"></i> Fechar Caixa
                </button>
            </div>
        </div>
        
        <div class="p-3 bg-white border-bottom d-flex gap-2 overflow-auto" id="categoryContainer" style="scrollbar-width: none;">
            <div class="cat-pill active" onclick="filterCategory('all')">Todos</div>
            @foreach($categories as $cat)
                <div class="cat-pill" onclick="filterCategory({{ $cat->id }})">{{ $cat->name }}</div>
            @endforeach
        </div>

        <div class="product-grid" id="productGrid">
            @foreach($products as $p)
                <div class="product-card" data-id="{{ $p->id }}" data-name="{{ $p->name }}" data-price="{{ $p->price }}" data-tax="{{ $p->tax->rate ?? 0 }}" data-cat="{{ $p->category_id }}" onclick="addToCart({{ $p->id }})">
                    <div class="product-img">
                        <i class="fas fa-box fa-3x"></i>
                    </div>
                    <div class="product-info">
                        <div class="product-name" title="{{ $p->name }}">{{ $p->name }}</div>
                        <div class="product-price">{{ number_format($p->price, 2) }} Kz</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Right Area: Cart -->
    <div class="pos-sidebar">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light">
            <div class="dropdown w-100">
                <button class="btn btn-outline-secondary w-100 d-flex justify-content-between align-items-center" type="button" data-bs-toggle="modal" data-bs-target="#customerModal">
                    <span id="customerNameDisplay"><i class="fas fa-user me-2"></i> Consumidor Final</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
        </div>

        <ul class="cart-items" id="cartItems">
            <!-- Items rendered via JS -->
            <div class="text-center text-muted mt-5 pt-5" id="emptyCartMsg">
                <i class="fas fa-shopping-basket fa-4x mb-3 opacity-25"></i>
                <h5>Carrinho Vazio</h5>
                <p>Selecione produtos para começar</p>
            </div>
        </ul>

        <div class="cart-summary">
            <div class="summary-row">
                <span class="text-muted">Subtotal</span>
                <span id="subtotalDisplay">0.00 Kz</span>
            </div>
            <div class="summary-row">
                <span class="text-muted">Impostos (IVA)</span>
                <span id="taxDisplay">0.00 Kz</span>
            </div>
            <div class="summary-row text-success d-none" id="discountRow">
                <span>Desconto</span>
                <span id="discountDisplay">-0.00 Kz</span>
            </div>
            <div class="summary-total">
                <span>Total a Pagar</span>
                <span id="totalDisplay">0.00 Kz</span>
            </div>
            <button class="pay-btn" onclick="openPayment()" id="btnPay" disabled>
                PAGAR <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </div>
    </div>
</div>

<!-- Modal: Seleção Cliente -->
<div class="modal fade" id="customerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Selecionar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <select id="customerSelect" class="form-select form-select-lg mb-3">
                    <option value="CF">Consumidor Final</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} (NIF: {{ $c->nif ?? 'N/D' }})</option>
                    @endselect
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="setCustomer()" data-bs-dismiss="modal">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Pagamento -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h4 class="modal-title mb-0"><i class="fas fa-wallet me-2"></i>Finalizar Pagamento</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-5 bg-light p-4 border-end">
                        <h6 class="text-muted mb-3">Resumo</h6>
                        <div class="d-flex justify-content-between mb-2 fs-5">
                            <span>Total Fatura:</span>
                            <strong id="payTotalAmount">0.00 Kz</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2 fs-5 text-success">
                            <span>Total Pago:</span>
                            <strong id="payTotalTendered">0.00 Kz</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fs-4 fw-bold" id="payChangeContainer">
                            <span>Falta Pagar:</span>
                            <span class="text-danger" id="payMissing">0.00 Kz</span>
                        </div>
                    </div>
                    <div class="col-md-7 p-4">
                        <h6 class="text-muted mb-3">Métodos de Pagamento</h6>
                        
                        <div class="input-group input-group-lg mb-3">
                            <span class="input-group-text"><i class="fas fa-money-bill-wave text-success" style="width:25px"></i> Numerário</span>
                            <input type="number" class="form-control text-end payment-input" id="payCash" value="0" step="0.01" oninput="calcChange()">
                        </div>
                        
                        <div class="input-group input-group-lg mb-3">
                            <span class="input-group-text"><i class="fas fa-credit-card text-primary" style="width:25px"></i> Multibanco</span>
                            <input type="number" class="form-control text-end payment-input" id="payCard" value="0" step="0.01" oninput="calcChange()">
                        </div>
                        
                        <div class="input-group input-group-lg mb-4">
                            <span class="input-group-text"><i class="fas fa-university text-info" style="width:25px"></i> Transferência</span>
                            <input type="number" class="form-control text-end payment-input" id="payTransfer" value="0" step="0.01" oninput="calcChange()">
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Tipo de Documento</label>
                                <select id="docType" class="form-select form-select-lg">
                                    <option value="FR">Fatura-Recibo</option>
                                    <option value="VD">Venda a Dinheiro</option>
                                    <option value="FT">Fatura (A Prazo)</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-primary btn-lg w-100 mt-4" onclick="fillExact()">Valor Exato</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer p-3 bg-light">
                <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">Voltar</button>
                <button type="button" class="btn btn-success btn-lg px-5 fw-bold" id="btnConfirmPay" onclick="submitSale()" disabled>Emitir Fatura</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Fechar Caixa -->
<div class="modal fade" id="closeSessionModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('vendas.pos.close') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-lock me-2"></i> Fechar Turno de Caixa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-4">Ao fechar a caixa, o sistema irá comparar o valor inserido com o saldo esperado das vendas.</p>
                <div class="mb-3">
                    <label class="form-label fw-bold">Contagem Física (Gaveta)</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-light">Kz</span>
                        <input type="number" step="0.01" min="0" name="closing_balance" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger"><i class="fas fa-check me-2"></i> Confirmar Fecho</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Database Mock (Injected via Blade)
    const products = @json($products);
    let cart = [];
    let currentCustomer = 'CF';
    let currentCustomerName = 'Consumidor Final';

    // Formatters
    const formatMoney = (val) => Number(val).toLocaleString('pt-AO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' Kz';

    // Add to Cart
    function addToCart(productId) {
        let product = products.find(p => p.id === productId);
        if(!product) return;

        let existing = cart.find(i => i.id === productId);
        if (existing) {
            existing.qty++;
        } else {
            cart.push({
                id: product.id,
                name: product.name,
                price: Number(product.price),
                tax_percent: product.tax ? Number(product.tax.rate) : 0,
                qty: 1,
                discount: 0
            });
        }
        renderCart();
    }

    function updateQty(index, delta) {
        cart[index].qty += delta;
        if (cart[index].qty <= 0) {
            cart.splice(index, 1);
        }
        renderCart();
    }

    function renderCart() {
        let html = '';
        let subtotal = 0;
        let tax = 0;

        if (cart.length === 0) {
            document.getElementById('emptyCartMsg').classList.remove('d-none');
            document.getElementById('btnPay').disabled = true;
        } else {
            document.getElementById('emptyCartMsg').classList.add('d-none');
            document.getElementById('btnPay').disabled = false;
        }

        cart.forEach((item, index) => {
            let itemSub = item.price * item.qty;
            let itemTax = itemSub * (item.tax_percent / 100);
            
            subtotal += itemSub;
            tax += itemTax;

            html += `
                <li class="cart-item">
                    <div class="cart-item-info">
                        <div class="cart-item-title">${item.name}</div>
                        <div class="cart-item-price">${formatMoney(item.price)} x ${item.qty}</div>
                    </div>
                    <div class="cart-item-actions">
                        <strong class="me-3">${formatMoney(itemSub + itemTax)}</strong>
                        <button class="qty-btn" onclick="updateQty(${index}, -1)"><i class="fas fa-minus fa-sm"></i></button>
                        <span class="fw-bold px-1">${item.qty}</span>
                        <button class="qty-btn" onclick="updateQty(${index}, 1)"><i class="fas fa-plus fa-sm"></i></button>
                    </div>
                </li>
            `;
        });

        document.getElementById('cartItems').innerHTML = html || document.getElementById('emptyCartMsg').outerHTML;
        
        let total = subtotal + tax;
        document.getElementById('subtotalDisplay').innerText = formatMoney(subtotal);
        document.getElementById('taxDisplay').innerText = formatMoney(tax);
        document.getElementById('totalDisplay').innerText = formatMoney(total);
        
        // Store globally for payment modal
        window.cartTotal = total;
    }

    function setCustomer() {
        let sel = document.getElementById('customerSelect');
        currentCustomer = sel.value;
        currentCustomerName = sel.options[sel.selectedIndex].text;
        document.getElementById('customerNameDisplay').innerHTML = `<i class="fas fa-user me-2"></i> ${currentCustomerName}`;
    }

    // Filter
    function filterCategory(catId) {
        document.querySelectorAll('.cat-pill').forEach(el => el.classList.remove('active'));
        event.target.classList.add('active');

        document.querySelectorAll('.product-card').forEach(card => {
            if (catId === 'all' || card.dataset.cat == catId) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    document.getElementById('searchInput').addEventListener('input', function(e) {
        let text = e.target.value.toLowerCase();
        document.querySelectorAll('.product-card').forEach(card => {
            if (card.dataset.name.toLowerCase().includes(text)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    });

    // Payment Logic
    function openPayment() {
        if(cart.length === 0) return;
        document.getElementById('payTotalAmount').innerText = formatMoney(window.cartTotal);
        document.getElementById('payCash').value = 0;
        document.getElementById('payCard').value = 0;
        document.getElementById('payTransfer').value = 0;
        calcChange();
        var myModal = new bootstrap.Modal(document.getElementById('paymentModal'));
        myModal.show();
    }

    function fillExact() {
        document.getElementById('payCash').value = window.cartTotal;
        document.getElementById('payCard').value = 0;
        document.getElementById('payTransfer').value = 0;
        calcChange();
    }

    function calcChange() {
        let cash = Number(document.getElementById('payCash').value) || 0;
        let card = Number(document.getElementById('payCard').value) || 0;
        let trans = Number(document.getElementById('payTransfer').value) || 0;
        let totalTendered = cash + card + trans;
        
        document.getElementById('payTotalTendered').innerText = formatMoney(totalTendered);

        let missing = window.cartTotal - totalTendered;
        let changeContainer = document.getElementById('payChangeContainer');
        let btnConfirm = document.getElementById('btnConfirmPay');
        
        let isCredit = document.getElementById('docType').value === 'FT';

        if (missing <= 0 || isCredit) {
            changeContainer.innerHTML = `<span>Troco:</span><span class="text-success">${formatMoney(Math.abs(missing))}</span>`;
            btnConfirm.disabled = false;
        } else {
            changeContainer.innerHTML = `<span>Falta Pagar:</span><span class="text-danger">${formatMoney(missing)}</span>`;
            btnConfirm.disabled = true;
        }
    }

    document.getElementById('docType').addEventListener('change', calcChange);

    function submitSale() {
        let btn = document.getElementById('btnConfirmPay');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processando...';

        let payments = [
            { method: 'NUMERARIO', amount: Number(document.getElementById('payCash').value) },
            { method: 'MULTIBANCO', amount: Number(document.getElementById('payCard').value) },
            { method: 'TRANSFERENCIA', amount: Number(document.getElementById('payTransfer').value) }
        ];

        let payload = {
            _token: '{{ csrf_token() }}',
            customer_id: currentCustomer,
            doc_type: document.getElementById('docType').value,
            items: cart,
            payments: payments
        };

        fetch('{{ route("vendas.pos.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                alert('Documento ' + data.doc_number + ' emitido com sucesso!');
                window.location.reload(); // Reinicia o POS para a próxima venda
            } else {
                alert('Erro: ' + data.message);
                btn.disabled = false;
                btn.innerText = 'Emitir Fatura';
            }
        })
        .catch(err => {
            alert('Erro de comunicação.');
            btn.disabled = false;
            btn.innerText = 'Emitir Fatura';
        });
    }
</script>
@endsection

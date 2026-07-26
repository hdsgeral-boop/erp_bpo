@extends('layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f8fafc;
    }
    .pos-header-bar {
        background: #ffffff;
        border-radius: 16px;
        padding: 12px 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .pos-container {
        display: grid;
        grid-template-columns: 1fr 420px;
        gap: 20px;
        height: calc(100vh - 170px);
    }
    @media (max-width: 1024px) {
        .pos-container {
            grid-template-columns: 1fr;
            height: auto;
        }
    }
    .category-pills {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding-bottom: 8px;
        margin-bottom: 12px;
        scrollbar-width: thin;
    }
    .category-pill {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 8px 16px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.2s ease;
    }
    .category-pill:hover, .category-pill.active {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
        gap: 16px;
        overflow-y: auto;
        padding-right: 8px;
    }
    .products-grid::-webkit-scrollbar { width: 6px; }
    .products-grid::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    
    .product-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
        text-align: left;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 145px;
        position: relative;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }
    .product-card:hover {
        border-color: #3b82f6;
        box-shadow: 0 12px 20px -5px rgba(59, 130, 246, 0.15);
        transform: translateY(-3px);
    }
    .product-card:active {
        transform: scale(0.97);
    }
    .product-card.out-of-stock {
        opacity: 0.6;
        border-color: #fca5a5;
        background: #fef2f2;
    }
    .product-name {
        font-size: 0.92rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 6px;
        line-height: 1.3;
    }
    .product-category-name {
        font-size: 0.72rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 8px;
    }
    .product-price {
        font-weight: 800;
        color: #2563eb;
        font-size: 1.1rem;
    }
    .stock-badge {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 12px;
        display: inline-block;
    }
    .stock-badge.in-stock { background: #d1fae5; color: #047857; }
    .stock-badge.low-stock { background: #fef3c7; color: #b45309; }
    .stock-badge.no-stock { background: #fee2e2; color: #b91c1c; }

    .cart-panel {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04);
    }
    .cart-header {
        background: #f8fafc;
        padding: 16px;
        border-bottom: 1px solid #e2e8f0;
    }
    .cart-items {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
    }
    .cart-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px dashed #e2e8f0;
    }
    .cart-item-name {
        font-size: 0.9rem;
        font-weight: 700;
        color: #334155;
    }
    .cart-item-price {
        font-size: 0.8rem;
        color: #64748b;
    }
    .cart-qty-ctrl {
        display: flex;
        align-items: center;
        gap: 6px;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 8px;
    }
    .cart-qty-btn {
        width: 24px;
        height: 24px;
        border-radius: 6px;
        border: none;
        background: #ffffff;
        color: #475569;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }
    .cart-qty-btn:hover {
        background: #2563eb;
        color: #fff;
    }
    .cart-summary {
        background: #f8fafc;
        padding: 16px;
        border-top: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
    }
    .cart-total-panel {
        background: linear-gradient(135deg, #0f172a, #1e293b);
        color: #fff;
        padding: 20px;
    }
    .total-row {
        display: flex;
        justify-content: space-between;
        font-size: 1.6rem;
        font-weight: 800;
        margin-bottom: 15px;
    }
    .btn-pay {
        width: 100%;
        padding: 16px;
        font-size: 1.15rem;
        font-weight: 800;
        border-radius: 12px;
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
        box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);
        transition: all 0.3s;
        color: white;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .btn-pay:hover:not(:disabled) {
        transform: translateY(-2px);
        background: linear-gradient(135deg, #34d399, #10b981);
    }
    .btn-pay:disabled {
        background: #475569;
        box-shadow: none;
        opacity: 0.6;
    }
    .cash-shortcut-btn {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 8px 12px;
        font-weight: 700;
        color: #334155;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .cash-shortcut-btn:hover {
        background: #2563eb;
        color: white;
        border-color: #2563eb;
    }
    .shortcuts-bar {
        background: #ffffff;
        border-radius: 12px;
        padding: 8px 16px;
        margin-top: 15px;
        display: flex;
        gap: 20px;
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 600;
        border: 1px solid #e2e8f0;
    }
    .shortcut-key {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        padding: 2px 6px;
        font-family: monospace;
        color: #1e293b;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-2">
    <!-- POS Header Info Bar -->
    <div class="pos-header-bar">
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-primary fs-6 px-3 py-2" style="border-radius: 10px;">
                <i class="fas fa-store me-1"></i> {{ session('company_name', 'ERP Consulvolt') }}
            </span>
            <span class="text-muted fw-semibold">
                <i class="fas fa-desktop text-success me-1"></i> {{ $activeSession ? 'Caixa Aberta (' . ($activeSession->posRegister->name ?? 'POS-01') . ')' : 'Caixa Geral' }}
            </span>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if($activeSession)
            <button type="button" class="btn btn-outline-warning btn-sm fw-bold shadow-sm" onclick="openHeldOrdersModal()">
                <i class="fas fa-pause-circle me-1"></i> Comandas em Espera <span id="heldOrdersBadge" class="badge bg-danger ms-1">0</span>
            </button>
            <button type="button" class="btn btn-outline-warning btn-sm fw-bold shadow-sm" onclick="openCashMovementModal()">
                <i class="fas fa-coins me-1"></i> Sangria / Reforço
            </button>
            <a href="{{ route('sales.pos.report_x', $activeSession->id) }}?auto_print=1" target="_blank" class="btn btn-outline-info btn-sm fw-bold shadow-sm">
                <i class="fas fa-print me-1"></i> Relatório X
            </a>
            <button type="button" class="btn btn-outline-danger btn-sm fw-bold shadow-sm" onclick="openCloseSessionModal()">
                <i class="fas fa-lock me-1"></i> Fechar Turno (Z)
            </button>
            @endif
            <span class="text-dark fw-bold">
                <i class="fas fa-user-circle text-primary me-1"></i> {{ auth()->user()->name ?? 'Operador' }}
            </span>
            <span class="badge bg-light text-dark border px-3 py-2">
                <i class="far fa-clock me-1"></i> <span id="liveClock">{{ date('d/m/Y H:i') }}</span>
            </span>
        </div>
    </div>

    <div class="pos-container">
        <!-- Left Section: Search, Category Pills & Products -->
        <div style="display: flex; flex-direction: column; height: 100%;">
            <!-- Search & Barcode -->
            <div class="mb-3 position-relative">
                <i class="fas fa-barcode position-absolute top-50 inset-s-0 translate-middle-y ms-3 text-muted fs-5"></i>
                <input type="text" id="productSearch" class="form-control ps-5" placeholder="Pesquisar produto ou passar Código de Barras (F2)..." style="font-size: 1.05rem; padding: 12px 12px 12px 45px; border-radius: 14px;" autofocus>
            </div>

            <!-- Category Pills Bar -->
            <div class="category-pills">
                <div class="category-pill active" onclick="filterCategory('all', this)">
                    <i class="fas fa-th-large me-1"></i> Todos os Artigos ({{ count($products) }})
                </div>
                @foreach($categories as $cat)
                <div class="category-pill" onclick="filterCategory('{{ $cat->id }}', this)">
                    {{ $cat->name }} ({{ $cat->products_count ?? 0 }})
                </div>
                @endforeach
            </div>

            <!-- Products Grid -->
            <div class="products-grid" id="productsGrid">
                @foreach($products as $product)
                @php
                    $isStockable = $product->is_inventory;
                    $stockQty = (int)$product->stock_qty;
                    $outOfStock = $isStockable && $stockQty <= 0;
                @endphp
                <div class="product-card {{ $outOfStock ? 'out-of-stock' : '' }}" 
                     data-category="{{ $product->category_id }}"
                     data-name="{{ strtolower($product->name) }}"
                     data-code="{{ strtolower($product->code) }}"
                     onclick="handleProductClick({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->unit_price }}, {{ $isStockable ? 1 : 0 }}, {{ $stockQty }})">
                    
                    <div>
                        <div class="product-category-name">{{ $product->category->name ?? 'Geral' }}</div>
                        <div class="product-name">{{ $product->name }}</div>
                        <div class="font-monospace text-muted small mb-2">#{{ $product->code }}</div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                        <div class="product-price">{{ number_format($product->unit_price, 2, ',', '.') }} Kz</div>
                        <div>
                            @if(!$isStockable)
                                <span class="stock-badge in-stock"><i class="fas fa-infinity"></i> Serviço</span>
                            @elseif($stockQty > 5)
                                <span class="stock-badge in-stock"><i class="fas fa-boxes me-1"></i>{{ $stockQty }}</span>
                            @elseif($stockQty > 0)
                                <span class="stock-badge low-stock"><i class="fas fa-exclamation-triangle me-1"></i>{{ $stockQty }}</span>
                            @else
                                <span class="stock-badge no-stock"><i class="fas fa-times me-1"></i>0</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Right Section: Cart & Checkout -->
        <div class="cart-panel">
            <div class="cart-header">
                <div class="mb-2">
                    <label class="form-label small fw-bold text-muted mb-1">TIPO DE DOCUMENTO</label>
                    <select id="docType" class="form-select fw-bold border-primary" onchange="updatePayButton()">
                        <option value="FR">Fatura-Recibo (A Pronto)</option>
                        <option value="FS">Fatura Simplificada</option>
                        <option value="FT">Fatura (A Prazo)</option>
                        <option value="GT">Guia de Transporte</option>
                        <option value="OR">Orçamento</option>
                        <option value="PP">Fatura Pró-Forma</option>
                    </select>
                </div>
                <div>
                    <label class="form-label small fw-bold text-muted mb-1">CLIENTE</label>
                    <div class="d-flex gap-2">
                        <select id="customerId" class="form-select grow">
                            <option value="">Consumidor Final (Anónimo)</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->nif ?? 'Sem NIF' }})</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createCustomerModal" title="Criar Novo Cliente">
                            <i class="fas fa-user-plus"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Cart Items -->
            <div class="cart-items" id="cartItems">
                <div style="text-align: center; color: #94a3b8; padding: 50px 0;" id="emptyCartMsg">
                    <i class="fas fa-shopping-cart fa-3x mb-3 text-light-gray"></i>
                    <div>O carrinho está vazio</div>
                    <small class="text-muted">Clique nos produtos à esquerda para adicionar</small>
                </div>
            </div>

            <!-- Cart Summary & Discount -->
            <div class="cart-summary">
                <div class="d-flex justify-content-between align-items-center mb-1 text-muted small">
                    <span>Subtotal Artigos:</span>
                    <span id="summarySubtotal" class="fw-bold">0,00 Kz</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-1 text-muted small">
                    <span>Imposto (IVA 14%):</span>
                    <span id="summaryTax" class="fw-bold">0,00 Kz</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2 text-muted small">
                    <span>Desconto Global (%):</span>
                    <div style="width: 110px;">
                        <input type="number" id="globalDiscountPercent" class="form-control form-control-sm text-end" min="0" max="100" value="0" oninput="renderCart()">
                    </div>
                </div>
            </div>

            <!-- Total Panel & Pay Button -->
            <div class="cart-total-panel">
                <div class="total-row">
                    <span>TOTAL</span>
                    <span id="cartTotal">0,00 Kz</span>
                </div>
                <div class="d-flex gap-2 mb-2">
                    <button type="button" class="btn btn-warning w-50 fw-bold py-2" onclick="openHoldOrderModal()" id="holdBtn" disabled style="border-radius: 10px;">
                        <i class="fas fa-pause-circle me-1"></i> Suspender
                    </button>
                    <button type="button" class="btn btn-outline-secondary w-50 fw-bold py-2" onclick="clearCart()" id="clearBtn" disabled style="border-radius: 10px;">
                        <i class="fas fa-trash me-1"></i> Limpar
                    </button>
                </div>
                <button class="btn btn-success btn-pay" onclick="openCheckoutModal()" id="payBtn" disabled>
                    <i class="fas fa-credit-card me-2"></i> Cobrar e Emitir (F9)
                </button>
            </div>
        </div>
    </div>

    <!-- Shortcuts Footer -->
    <div class="shortcuts-bar">
        <div><span class="shortcut-key">F2</span> Pesquisar Produto</div>
        <div><span class="shortcut-key">F9</span> Abrir Cobrança</div>
        <div><span class="shortcut-key">ESC</span> Limpar Carrinho</div>
    </div>
</div>

<!-- Modal de Cobrança / Pagamento & Troco -->
<div class="modal fade" id="checkoutModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-header bg-dark text-white p-4" style="border-radius: 20px 20px 0 0;">
                <div>
                    <h5 class="modal-title fw-bold mb-0"><i class="fas fa-cash-register me-2 text-emerald"></i> Finalizar Venda</h5>
                    <small class="text-white-50" id="checkoutDocTitle">Fatura-Recibo A Pronto</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Total a Pagar -->
                <div class="p-3 bg-light rounded-3 text-center mb-4 border">
                    <span class="text-muted fw-semibold small d-block">TOTAL A PAGAR</span>
                    <span class="display-6 fw-extrabold text-primary" id="checkoutTotalDisplay">0,00 Kz</span>
                </div>

                <!-- Método de Pagamento -->
                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted">FORMA DE PAGAMENTO</label>
                    <div class="grid gap-2 d-flex flex-wrap" id="paymentMethodButtons">
                        <button type="button" class="btn btn-outline-primary flex-fill fw-bold active btn-payment-method" data-method="CASH" onclick="selectPaymentMethod('CASH', this)">
                            <i class="fas fa-money-bill-wave me-1"></i> Numerário
                        </button>
                        <button type="button" class="btn btn-outline-primary flex-fill fw-bold btn-payment-method" data-method="MULTICAIXA" onclick="selectPaymentMethod('MULTICAIXA', this)">
                            <i class="fas fa-credit-card me-1"></i> TPA / Multicaixa
                        </button>
                        <button type="button" class="btn btn-outline-primary flex-fill fw-bold btn-payment-method" data-method="TRANSFER" onclick="selectPaymentMethod('TRANSFER', this)">
                            <i class="fas fa-university me-1"></i> Transferência
                        </button>
                    </div>
                </div>

                <!-- Quantia Entregue -->
                <div class="mb-4" id="cashInputContainer">
                    <label class="form-label fw-bold small text-muted">VALOR ENTREGUE PELO CLIENTE (KZ)</label>
                    <input type="number" step="0.01" min="0" id="amountPaidInput" class="form-control form-control-lg fw-bold text-primary text-end fs-3" oninput="calculateChange()">
                    
                    <!-- Botões Rápidos de Dinheiro -->
                    <div class="d-flex gap-2 mt-2 flex-wrap">
                        <button type="button" class="cash-shortcut-btn" onclick="setExactAmount()">Exato</button>
                        <button type="button" class="cash-shortcut-btn" onclick="addCashShortcut(1000)">+1.000</button>
                        <button type="button" class="cash-shortcut-btn" onclick="addCashShortcut(5000)">+5.000</button>
                        <button type="button" class="cash-shortcut-btn" onclick="addCashShortcut(10000)">+10.000</button>
                        <button type="button" class="cash-shortcut-btn" onclick="addCashShortcut(20000)">+20.000</button>
                    </div>
                </div>

                <!-- Painel de Troco -->
                <div class="p-3 bg-emerald-light rounded-3 text-center border border-emerald" style="background: #ecfdf5; border-color: #a7f3d0;" id="changeContainer">
                    <span class="text-emerald-700 fw-bold small d-block" style="color: #047857;">TROCO A ENTREGAR</span>
                    <span class="fs-2 fw-extrabold text-emerald-800" style="color: #065f46;" id="changeDisplay">0,00 Kz</span>
                </div>
            </div>
            <div class="modal-footer bg-light p-3" style="border-radius: 0 0 20px 20px;">
                <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success px-5 py-3 fw-extrabold text-uppercase fs-5" id="confirmCheckoutBtn" onclick="submitFinalSale()">
                    <i class="fas fa-check-circle me-2"></i> Confirmar & Emitir
                </button>
            </div>
        </div>
    </div>
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

<!-- Modal Sucesso Venda -->
<div class="modal fade" id="saleSuccessModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-body text-center p-4">
                <div class="text-success mb-3" style="font-size: 3.8rem;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 class="fw-extrabold mb-1 text-dark">Venda Processada!</h3>
                <p class="text-muted mb-3" id="successDocNumber">Documento assinado digitalmente segundo as normas da AGT.</p>
                
                <div class="p-3 bg-light rounded-3 mb-4 border d-inline-block px-5">
                    <span class="text-muted small fw-bold d-block">TROCO A DEVOLVER</span>
                    <span class="fs-2 fw-extrabold text-success" id="successChangeDisplay">0,00 Kz</span>
                </div>

                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <a id="printThermalBtn" href="#" target="_blank" class="btn btn-primary px-4 py-2.5 fw-bold" style="border-radius: 10px;">
                        <i class="fas fa-receipt me-1"></i> Imprimir Talão
                    </a>
                    <a id="printPdfBtn" href="#" target="_blank" class="btn btn-outline-secondary px-4 py-2.5 fw-bold" style="border-radius: 10px;">
                        <i class="fas fa-file-pdf me-1"></i> Ver PDF A4
                    </a>
                    <button type="button" class="btn btn-success px-4 py-2.5 fw-bold" style="border-radius: 10px;" onclick="resetPos()">
                        <i class="fas fa-plus me-1"></i> Nova Venda
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sangria / Reforço -->
<div class="modal fade" id="cashMovementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-dark text-white" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
                <h5 class="modal-title fw-bold"><i class="fas fa-coins me-2 text-warning"></i> Registo de Sangria / Reforço</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tipo de Operação</label>
                    <select id="cashMovementType" class="form-select fw-bold">
                        <option value="REFORCO">🟢 Reforço de Fundo de Maneio (+)</option>
                        <option value="SANGRIA">🔴 Sangria de Caixa (-)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Valor (Kz)</label>
                    <input type="number" id="cashMovementAmount" class="form-control form-control-lg fw-bold" placeholder="0.00" step="0.01" min="0">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Motivo / Observação</label>
                    <input type="text" id="cashMovementReason" class="form-control" placeholder="Ex.: Reforço de trocos para a tarde">
                </div>
            </div>
            <div class="modal-footer border-0 p-3 bg-light">
                <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary px-4 fw-bold" onclick="submitCashMovement()">Salvar Movimento</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Fechar Turno / Caixa -->
<div class="modal fade" id="closeSessionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-danger text-white" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
                <h5 class="modal-title fw-bold"><i class="fas fa-lock me-2"></i> Fechamento de Turno de Caixa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-warning border-0 small mb-3">
                    <i class="fas fa-exclamation-triangle me-1"></i> Ao fechar o turno, a caixa será trancada e o <strong>Relatório Z</strong> será emitido automaticamente.
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Valor Total Contado em Caixa (Dinheiro)</label>
                    <input type="number" id="closingBalanceInput" class="form-control form-control-lg fw-bold text-end" placeholder="0.00" step="0.01" min="0">
                </div>
            </div>
            <div class="modal-footer border-0 p-3 bg-light">
                <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger px-4 fw-bold" onclick="submitCloseSession()">Fechar & Gerar Relatório Z</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let cart = [];
    let activePaymentMethod = 'CASH';
    let currentTotalToPay = 0;

    function openCloseSessionModal() {
        const modal = new bootstrap.Modal(document.getElementById('closeSessionModal'));
        modal.show();
    }

    function submitCloseSession() {
        const closingBalance = parseFloat(document.getElementById('closingBalanceInput').value) || 0;

        fetch("{{ route('vendas.pos.close') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ closing_balance: closingBalance })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                if (data.report_z_url) {
                    window.open(data.report_z_url + '?auto_print=1', '_blank');
                }
                location.reload();
            } else {
                alert('Erro: ' + (data.message || 'Falha ao fechar turno.'));
            }
        })
        .catch(err => alert('Erro de comunicação com o servidor.'));
    }

    function openCashMovementModal() {
        const modal = new bootstrap.Modal(document.getElementById('cashMovementModal'));
        modal.show();
    }

    function submitCashMovement() {
        const type = document.getElementById('cashMovementType').value;
        const amount = parseFloat(document.getElementById('cashMovementAmount').value);
        const reason = document.getElementById('cashMovementReason').value;

        if (!amount || amount <= 0) {
            alert('Por favor introduza um valor válido.');
            return;
        }

        fetch("{{ route('sales.pos.cash_movement') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ type, amount, reason })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                const modalEl = document.getElementById('cashMovementModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
                document.getElementById('cashMovementAmount').value = '';
                document.getElementById('cashMovementReason').value = '';
            } else {
                alert('Erro: ' + (data.message || 'Falha ao gravar movimento.'));
            }
        })
        .catch(err => alert('Erro de comunicação com o servidor.'));
    }

    function formatMoney(amount) {
        return new Intl.NumberFormat('pt-AO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(amount) + ' Kz';
    }

    function handleProductClick(id, name, price, isStockable, stockQty) {
        if (isStockable && stockQty <= 0) {
            alert('Atenção: Este artigo encontra-se sem stock disponível no momento.');
            return;
        }

        let item = cart.find(i => i.product_id === id);
        if (item) {
            if (isStockable && (item.quantity + 1) > stockQty) {
                alert(`Stock máximo disponível atingido (${stockQty} unidades).`);
                return;
            }
            item.quantity++;
            item.subtotal = item.quantity * item.unit_price;
        } else {
            cart.push({
                product_id: id,
                id: id,
                name: name,
                unit_price: price,
                quantity: 1,
                subtotal: price,
                is_stockable: isStockable,
                stock_qty: stockQty
            });
        }
        renderCart();
    }

    function updateQty(id, delta) {
        let item = cart.find(i => i.product_id === id);
        if (item) {
            if (delta > 0 && item.is_stockable && (item.quantity + delta) > item.stock_qty) {
                alert(`Stock máximo disponível atingido (${item.stock_qty} unidades).`);
                return;
            }
            item.quantity += delta;
            if (item.quantity <= 0) {
                cart = cart.filter(i => i.product_id !== id);
            } else {
                item.subtotal = item.quantity * item.unit_price;
            }
        }
        renderCart();
    }

    function removeFromCart(id) {
        cart = cart.filter(i => i.product_id !== id);
        renderCart();
    }

    function getCartTotals() {
        let subtotal = 0;
        cart.forEach(item => subtotal += item.subtotal);

        const discountPercent = parseFloat(document.getElementById('globalDiscountPercent').value) || 0;
        const discountAmount = subtotal * (discountPercent / 100);
        const netSubtotal = subtotal - discountAmount;
        const taxAmount = netSubtotal * 0.14; // IVA 14%
        const grandTotal = netSubtotal + taxAmount;

        return {
            subtotal,
            discountPercent,
            discountAmount,
            taxAmount,
            grandTotal
        };
    }

    function renderCart() {
        const container = document.getElementById('cartItems');
        const payBtn = document.getElementById('payBtn');
        const holdBtn = document.getElementById('holdBtn');
        const clearBtn = document.getElementById('clearBtn');
        const totals = getCartTotals();

        if (cart.length === 0) {
            container.innerHTML = `
                <div style="text-align: center; color: #94a3b8; padding: 50px 0;">
                    <i class="fas fa-shopping-cart fa-3x mb-3 text-muted opacity-50"></i>
                    <div>O carrinho está vazio</div>
                    <small class="text-muted">Clique nos produtos à esquerda para adicionar</small>
                </div>`;
            document.getElementById('summarySubtotal').textContent = '0,00 Kz';
            document.getElementById('summaryTax').textContent = '0,00 Kz';
            document.getElementById('cartTotal').textContent = '0,00 Kz';
            payBtn.disabled = true;
            if (holdBtn) holdBtn.disabled = true;
            if (clearBtn) clearBtn.disabled = true;
            return;
        }

        payBtn.disabled = false;
        if (holdBtn) holdBtn.disabled = false;
        if (clearBtn) clearBtn.disabled = false;
        container.innerHTML = '';

        cart.forEach(item => {
            container.innerHTML += `
                <div class="cart-item">
                    <div style="flex: 1; padding-right: 10px;">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-price">${formatMoney(item.unit_price)} x ${item.quantity}</div>
                    </div>
                    <div class="cart-qty-ctrl me-2">
                        <button type="button" class="cart-qty-btn" onclick="updateQty(${item.product_id}, -1)">-</button>
                        <span style="font-weight: 700; width: 22px; text-align: center;">${item.quantity}</span>
                        <button type="button" class="cart-qty-btn" onclick="updateQty(${item.product_id}, 1)">+</button>
                    </div>
                    <div style="width: 85px; text-align: right; font-weight: 800;" class="text-dark">
                        ${formatMoney(item.subtotal)}
                    </div>
                    <button type="button" class="btn btn-link text-danger p-0 ms-2" onclick="removeFromCart(${item.product_id})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        });

        document.getElementById('summarySubtotal').textContent = formatMoney(totals.subtotal);
        document.getElementById('summaryTax').textContent = formatMoney(totals.taxAmount);
        document.getElementById('cartTotal').textContent = formatMoney(totals.grandTotal);
        currentTotalToPay = totals.grandTotal;
    }

    function openCheckoutModal() {
        if (cart.length === 0) return;
        const totals = getCartTotals();
        currentTotalToPay = totals.grandTotal;

        document.getElementById('checkoutTotalDisplay').textContent = formatMoney(currentTotalToPay);
        const docSelect = document.getElementById('docType');
        const docText = docSelect.options[docSelect.selectedIndex].text;
        document.getElementById('checkoutDocTitle').textContent = docText;

        // Reset inputs
        document.getElementById('amountPaidInput').value = currentTotalToPay.toFixed(2);
        calculateChange();

        const modal = new bootstrap.Modal(document.getElementById('checkoutModal'));
        modal.show();
    }

    function selectPaymentMethod(method, btn) {
        activePaymentMethod = method;
        document.querySelectorAll('.btn-payment-method').forEach(b => b.classList.remove('active', 'btn-primary'));
        document.querySelectorAll('.btn-payment-method').forEach(b => b.classList.add('btn-outline-primary'));
        
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-primary', 'active');

        if (method !== 'CASH') {
            document.getElementById('amountPaidInput').value = currentTotalToPay.toFixed(2);
        }
        calculateChange();
    }

    function setExactAmount() {
        document.getElementById('amountPaidInput').value = currentTotalToPay.toFixed(2);
        calculateChange();
    }

    function addCashShortcut(amount) {
        const input = document.getElementById('amountPaidInput');
        const current = parseFloat(input.value) || 0;
        input.value = (current + amount).toFixed(2);
        calculateChange();
    }

    function calculateChange() {
        const inputVal = parseFloat(document.getElementById('amountPaidInput').value) || 0;
        const change = Math.max(0, inputVal - currentTotalToPay);
        document.getElementById('changeDisplay').textContent = formatMoney(change);
    }

    function submitFinalSale() {
        if (cart.length === 0) return;

        const confirmBtn = document.getElementById('confirmCheckoutBtn');
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Emitindo...';

        const totals = getCartTotals();
        const amountPaid = parseFloat(document.getElementById('amountPaidInput').value) || totals.grandTotal;

        fetch("{{ route('vendas.pos.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                doc_type: document.getElementById('docType').value,
                customer_id: document.getElementById('customerId').value || null,
                payment_method: activePaymentMethod,
                amount_paid: amountPaid,
                total_discount: totals.discountAmount,
                items: cart
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Fechar Modal de Pagamento
                const checkoutModalEl = document.getElementById('checkoutModal');
                const checkoutModal = bootstrap.Modal.getInstance(checkoutModalEl);
                if (checkoutModal) checkoutModal.hide();

                // Exibir Modal de Sucesso
                const saleId = data.sale_id || (data.data ? data.data.id : '');
                document.getElementById('successDocNumber').textContent = data.message || 'Documento emitido com sucesso.';
                document.getElementById('successChangeDisplay').textContent = data.formatted_change || formatMoney(data.change_amount || 0);
                
                document.getElementById('printThermalBtn').href = `/vendas/documentos/${saleId}/talao`;
                document.getElementById('printPdfBtn').href = `/vendas/documentos/${saleId}/pdf`;
                
                const successModal = new bootstrap.Modal(document.getElementById('saleSuccessModal'));
                successModal.show();

                cart = [];
                renderCart();
            } else {
                alert('Erro ao emitir venda: ' + (data.message || 'Falha na emissão.'));
            }
        })
        .catch(err => {
            alert('Erro no servidor ao processar venda.');
            console.error(err);
        })
        .finally(() => {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i> Confirmar & Emitir';
        });
    }

    function resetPos() {
        location.reload();
    }

    function filterCategory(catId, element) {
        document.querySelectorAll('.category-pill').forEach(p => p.classList.remove('active'));
        element.classList.add('active');

        const cards = document.querySelectorAll('.product-card');
        cards.forEach(card => {
            if (catId === 'all' || card.dataset.category === catId) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function updatePayButton() {
        const payBtn = document.getElementById('payBtn');
        payBtn.disabled = cart.length === 0;
    }

    function fetchHeldOrdersCount() {
        fetch("{{ route('sales.pos.held.list') }}")
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const badge = document.getElementById('heldOrdersBadge');
                    if (badge) badge.textContent = data.held_orders.length;
                }
            })
            .catch(err => console.error(err));
    }

    function openHoldOrderModal() {
        if (cart.length === 0) return;
        document.getElementById('holdReferenceInput').value = '';
        const modal = new bootstrap.Modal(document.getElementById('holdOrderModal'));
        modal.show();
    }

    function submitHoldOrder() {
        const refName = document.getElementById('holdReferenceInput').value.trim();
        const totals = getCartTotals();

        fetch("{{ route('sales.pos.held.hold') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                reference_name: refName,
                customer_id: document.getElementById('customerId').value || null,
                items: cart,
                totals: totals
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const modalEl = document.getElementById('holdOrderModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
                cart = [];
                renderCart();
                fetchHeldOrdersCount();
                alert(data.message);
            } else {
                alert(data.message || 'Erro ao suspender venda.');
            }
        });
    }

    function openHeldOrdersModal() {
        const tbody = document.getElementById('heldOrdersTableBody');
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-2"></i>A carregar comandas...</td></tr>';
        
        const modal = new bootstrap.Modal(document.getElementById('heldOrdersModal'));
        modal.show();

        fetch("{{ route('sales.pos.held.list') }}")
            .then(res => res.json())
            .then(data => {
                if (data.success && data.held_orders.length > 0) {
                    let html = '';
                    data.held_orders.forEach(o => {
                        const dateStr = new Date(o.created_at).toLocaleTimeString('pt-PT', { hour: '2-digit', minute: '2-digit' });
                        const itemsCount = o.items_json ? o.items_json.length : 0;
                        const totalKz = o.totals_json && o.totals_json.grandTotal ? formatMoney(o.totals_json.grandTotal) : '0,00 Kz';

                        html += `
                            <tr>
                                <td><span class="badge bg-light text-dark font-monospace">${dateStr}</span></td>
                                <td><strong class="text-dark">${o.reference_name || 'Comanda #' + o.id}</strong></td>
                                <td><span class="badge bg-info text-dark">${itemsCount} artigos</span></td>
                                <td class="text-end font-monospace fw-bold text-success">${totalKz}</td>
                                <td class="text-center">
                                    <button class="btn btn-success btn-sm me-1" onclick="restoreHeldOrder(${o.id})">
                                        <i class="fas fa-play me-1"></i> Retomar
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" onclick="cancelHeldOrder(${o.id})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    tbody.innerHTML = html;
                } else {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted"><i class="fas fa-check-circle me-1"></i>Nenhuma comanda em espera no momento.</td></tr>';
                }
            });
    }

    function restoreHeldOrder(id) {
        if (cart.length > 0 && !confirm('O carrinho atual contém artigos. Deseja substituir pelo conteúdo da comanda suspensa?')) {
            return;
        }

        fetch(`/vendas/pos/held-orders/${id}/restore`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.held_order) {
                cart = data.held_order.items_json || [];
                if (data.held_order.customer_id) {
                    document.getElementById('customerId').value = data.held_order.customer_id;
                }
                renderCart();
                fetchHeldOrdersCount();
                const modalEl = document.getElementById('heldOrdersModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            } else {
                alert(data.message || 'Erro ao retomar comanda.');
            }
        });
    }

    function cancelHeldOrder(id) {
        if (!confirm('Deseja eliminar esta comanda em espera?')) return;

        fetch(`/vendas/pos/held-orders/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                openHeldOrdersModal();
                fetchHeldOrdersCount();
            }
        });
    }

    function clearCart() {
        if (cart.length === 0) return;
        if (confirm('Deseja limpar todos os artigos do carrinho?')) {
            cart = [];
            renderCart();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        fetchHeldOrdersCount();

        // Foco automático no leitor de código de barras / pesquisa
        const searchInput = document.getElementById('productSearch');
        if (searchInput) searchInput.focus();

        // Pesquisa Dinâmica em Tempo Real
        searchInput.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase().trim();
            const cards = document.querySelectorAll('.product-card');

            cards.forEach(card => {
                const name = card.dataset.name || '';
                const code = card.dataset.code || '';
                if (name.includes(query) || code.includes(query)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Atalhos de Teclado (F2, F9, ESC)
        document.addEventListener('keydown', function(e) {
            if (e.key === 'F2') {
                e.preventDefault();
                document.getElementById('productSearch').focus();
            } else if (e.key === 'F9') {
                e.preventDefault();
                if (cart.length > 0) openCheckoutModal();
            } else if (e.key === 'Escape') {
                const checkoutModalEl = document.getElementById('checkoutModal');
                const checkoutModal = bootstrap.Modal.getInstance(checkoutModalEl);
                if (checkoutModal) checkoutModal.hide();
            }
        });

        // Relógio em tempo real
        setInterval(() => {
            const clock = document.getElementById('liveClock');
            if (clock) {
                const now = new Date();
                clock.textContent = now.toLocaleDateString('pt-PT') + ' ' + now.toLocaleTimeString('pt-PT', { hour: '2-digit', minute: '2-digit' });
            }
        }, 30000);
    });
</script>

<!-- Modal Suspender Venda (Hold) -->
<div class="modal fade" id="holdOrderModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-warning"><i class="fas fa-pause-circle me-2"></i> Suspender Venda (Comanda)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small">Atribua um nome ou referência para identificar esta comanda em espera (ex: Mesa 4, Cliente João):</p>
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Nome / Referência da Comanda</label>
                    <input type="text" id="holdReferenceInput" class="form-control form-control-lg" placeholder="Ex: Cliente Mesa 2" style="border-radius: 12px;">
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning rounded-3 px-4 fw-bold" onclick="submitHoldOrder()">
                    <i class="fas fa-save me-1"></i> Confirmar Suspender
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Comandas em Espera -->
<div class="modal fade" id="heldOrdersModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-list-alt text-warning me-2"></i> Comandas em Espera (Vendas Suspensas)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="bg-light text-muted small">
                            <tr>
                                <th>Hora</th>
                                <th>Referência</th>
                                <th>Itens</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Ação</th>
                            </tr>
                        </thead>
                        <tbody id="heldOrdersTableBody">
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">A carregar comandas...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endpush
@endsection

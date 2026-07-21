@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    .form-control, .form-select {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border: 1px solid #cbd5e1;
    }
    .form-control:focus, .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .btn-save {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border-radius: 10px;
        padding: 0.6rem 2rem;
        font-weight: 600;
        border: none;
    }
    .total-box {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1.5rem;
        border: 1px solid #e2e8f0;
    }
    .total-box h4 {
        color: #1e293b;
        font-weight: 800;
        margin: 0;
    }
</style>
@endpush

@section('content')
@php
    $title = match($category) {
        'faturas' => 'Nova Fatura / Fatura-Recibo',
        'orcamentos' => 'Novo Orçamento / Pró-forma',
        'guias' => 'Nova Guia de Remessa / Transporte',
        'notas' => 'Nova Nota de Crédito / Débito',
        default => 'Novo Documento'
    };
@endphp
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('vendas.documentos.index', $category) }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="fas fa-arrow-left me-1"></i> Voltar à Lista
        </a>
        <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-file-invoice-dollar text-primary me-2"></i>{{ $title }}</h2>
        <p class="text-muted mt-1">Ao gravar, o stock será deduzido automaticamente do armazém selecionado.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm" style="border-radius: 10px;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger shadow-sm" style="border-radius: 10px;">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('vendas.documentos.store', $category) }}" method="POST" id="invoice-form">
        @csrf
        
        <div class="card-premium p-4 mb-4">
            <h5 class="fw-bold border-bottom pb-2 mb-4">Cabeçalho do Documento</h5>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Data de Emissão <span class="text-danger">*</span></label>
                    <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Série Documental <span class="text-danger">*</span></label>
                    <select name="series_id" class="form-select" required>
                        @foreach($series as $s)
                            <option value="{{ $s->id }}" {{ $s->is_default ? 'selected' : '' }}>{{ $s->identifier }} (Próx: {{ $s->current_number + 1 }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Cliente <span class="text-danger">*</span></label>
                    <select name="customer_id" class="form-select" required>
                        <option value="">Selecione o cliente...</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }} (NIF: {{ $customer->tax_id }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Armazém (Origem da Mercadoria) <span class="text-danger">*</span></label>
                    <select name="warehouse_id" class="form-select" required>
                        <option value="">Selecione de onde sai o stock...</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Notas Opcionais (Rodapé)</label>
                    <input type="text" name="notes" class="form-control" value="{{ old('notes') }}" placeholder="Ex: Isenção Artigo 9º...">
                </div>
            </div>
        </div>

        <div class="card-premium p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-4">
                <h5 class="fw-bold m-0">Linhas de Faturação</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add-item-btn"><i class="fas fa-plus me-1"></i> Adicionar Artigo</button>
            </div>
            
            <div class="table-responsive">
                <table class="table align-middle" id="items-table">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 25%;">Artigo</th>
                            <th style="width: 10%;">Qtd</th>
                            <th style="width: 15%;">Preço Unit.</th>
                            <th style="width: 10%;">Desconto</th>
                            <th style="width: 15%;">Imposto</th>
                            <th style="width: 10%;">Motivo Isenção</th>
                            <th style="width: 10%;">Total Linha</th>
                            <th style="width: 5%;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Linha Base -->
                        <tr>
                            <td>
                                <select name="items[0][product_id]" class="form-select product-select" required>
                                    <option value="" data-price="0" data-taxid="">Selecione...</option>
                                    @foreach($products as $prod)
                                        <option value="{{ $prod->id }}" data-price="{{ $prod->unit_price }}" data-taxid="{{ $prod->tax_id }}">[{{ $prod->code }}] {{ $prod->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0.01" name="items[0][quantity]" class="form-control calc-trigger" value="1" required>
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0" name="items[0][unit_price]" class="form-control calc-trigger price-input" required>
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0" name="items[0][discount_amount]" class="form-control calc-trigger" value="0">
                            </td>
                            <td>
                                <select name="items[0][tax_id]" class="form-select calc-trigger tax-select" required>
                                    <option value="" data-rate="0">Selecione...</option>
                                    @foreach($taxes as $tax)
                                        <option value="{{ $tax->id }}" data-rate="{{ $tax->rate }}">{{ $tax->name }} ({{ number_format($tax->rate, 2) }}%)</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" name="items[0][exemption_reason]" class="form-control exemption-input" placeholder="M04..." readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control bg-light total-line fw-bold" value="0.00" readonly>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm text-danger remove-line" disabled><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row justify-content-end mt-4">
                <div class="col-md-5">
                    <div class="total-box">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fw-bold">Subtotal (Sem IVA):</span>
                            <span id="summary-subtotal">0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fw-bold">Desconto Total:</span>
                            <span id="summary-discount" class="text-danger">0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                            <span class="text-muted fw-bold">Total IVA:</span>
                            <span id="summary-tax">0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0 text-dark">Total a Pagar:</h5>
                            <h4 class="text-primary" id="summary-grand-total">0.00 AOA</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end mt-5 pt-3 border-top">
            <a href="{{ route('vendas.documentos.index', $category) }}" class="btn btn-light border fw-bold me-2 px-4" style="border-radius:10px;">Cancelar</a>
            <button type="submit" class="btn btn-primary fw-bold px-4" style="border-radius:10px; font-size: 1.1rem;">
                <i class="fas fa-check-circle me-2"></i> Emitir Documento
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let lineIndex = 1;
        const tbody = document.querySelector('#items-table tbody');
        
        function calculateTotals() {
            let globalSubtotal = 0;
            let globalDiscount = 0;
            let globalTax = 0;

            const rows = tbody.querySelectorAll('tr');
            rows.forEach(row => {
                const qty = parseFloat(row.querySelector('input[name*="[quantity]"]').value) || 0;
                const price = parseFloat(row.querySelector('input[name*="[unit_price]"]').value) || 0;
                const discount = parseFloat(row.querySelector('input[name*="[discount_amount]"]').value) || 0;
                
                const taxSelect = row.querySelector('.tax-select');
                const taxOption = taxSelect.options[taxSelect.selectedIndex];
                const taxRate = parseFloat(taxOption ? taxOption.dataset.rate : 0) || 0;
                
                // Tratar Motivo de Isenção na View
                const exemptionInput = row.querySelector('.exemption-input');
                if (taxRate === 0 && taxSelect.value !== '') {
                    exemptionInput.removeAttribute('readonly');
                    exemptionInput.setAttribute('required', 'required');
                } else {
                    exemptionInput.setAttribute('readonly', 'readonly');
                    exemptionInput.removeAttribute('required');
                    exemptionInput.value = '';
                }
                
                const lineSubtotal = (qty * price) - discount;
                const lineTax = lineSubtotal * (taxRate / 100);
                const lineTotal = lineSubtotal + lineTax;

                row.querySelector('.total-line').value = lineTotal.toFixed(2);

                globalSubtotal += lineSubtotal;
                globalDiscount += discount;
                globalTax += lineTax;
            });

            document.getElementById('summary-subtotal').textContent = globalSubtotal.toFixed(2);
            document.getElementById('summary-discount').textContent = globalDiscount.toFixed(2);
            document.getElementById('summary-tax').textContent = globalTax.toFixed(2);
            document.getElementById('summary-grand-total').textContent = (globalSubtotal + globalTax).toFixed(2) + ' AOA';
        }

        // Auto-fill price and tax when product is selected
        tbody.addEventListener('change', function(e) {
            if(e.target.classList.contains('product-select')) {
                const option = e.target.options[e.target.selectedIndex];
                const row = e.target.closest('tr');
                if(option.value) {
                    row.querySelector('.price-input').value = option.dataset.price;
                    row.querySelector('.tax-select').value = option.dataset.taxid;
                    calculateTotals();
                }
            } else if (e.target.classList.contains('tax-select')) {
                calculateTotals();
            }
        });

        tbody.addEventListener('input', function(e) {
            if(e.target.classList.contains('calc-trigger')) {
                calculateTotals();
            }
        });

        document.getElementById('add-item-btn').addEventListener('click', function() {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <select name="items[${lineIndex}][product_id]" class="form-select product-select" required>
                        <option value="" data-price="0" data-taxid="">Selecione...</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}" data-price="{{ $prod->unit_price }}" data-taxid="{{ $prod->tax_id }}">[{{ $prod->code }}] {{ $prod->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" step="0.01" min="0.01" name="items[${lineIndex}][quantity]" class="form-control calc-trigger" value="1" required></td>
                <td><input type="number" step="0.01" min="0" name="items[${lineIndex}][unit_price]" class="form-control calc-trigger price-input" required></td>
                <td><input type="number" step="0.01" min="0" name="items[${lineIndex}][discount_amount]" class="form-control calc-trigger" value="0"></td>
                <td>
                    <select name="items[${lineIndex}][tax_id]" class="form-select calc-trigger tax-select" required>
                        <option value="" data-rate="0">Selecione...</option>
                        @foreach($taxes as $tax)
                            <option value="{{ $tax->id }}" data-rate="{{ $tax->rate }}">{{ $tax->name }} ({{ number_format($tax->rate, 2) }}%)</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="text" name="items[${lineIndex}][exemption_reason]" class="form-control exemption-input" placeholder="M04..." readonly></td>
                <td><input type="text" class="form-control bg-light total-line fw-bold" value="0.00" readonly></td>
                <td class="text-center"><button type="button" class="btn btn-sm text-danger remove-line"><i class="fas fa-trash"></i></button></td>
            `;
            tbody.appendChild(tr);
            lineIndex++;
            updateRemoveButtons();
        });

        tbody.addEventListener('click', function(e) {
            if (e.target.closest('.remove-line')) {
                e.target.closest('tr').remove();
                updateRemoveButtons();
                calculateTotals();
            }
        });

        function updateRemoveButtons() {
            const btns = tbody.querySelectorAll('.remove-line');
            btns.forEach(btn => btn.disabled = btns.length === 1);
        }

        calculateTotals();
    });
</script>
@endpush
@endsection

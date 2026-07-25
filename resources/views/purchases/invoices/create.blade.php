@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    .summary-card {
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        padding: 1.25rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-extrabold text-dark mb-1">
                <i class="fas fa-file-invoice-dollar text-primary me-2"></i> Registar Fatura de Fornecedor
            </h2>
            <p class="text-muted small mb-0">Lançamento manual de despesas, aprovisionamento ou faturas de compra de fornecedores.</p>
        </div>
        <div>
            <a href="{{ route('compras.faturas.index') }}" class="btn btn-outline-secondary fw-bold px-3 py-2" style="border-radius: 10px;">
                <i class="fas fa-arrow-left me-1"></i> Voltar à Lista
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> <strong>Por favor verifique os erros abaixo:</strong>
            <ul class="mb-0 mt-2 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card-premium">
        <div class="card-body p-4">
            <form action="{{ route('compras.faturas.store') }}" method="POST" id="purchaseInvoiceForm">
                @csrf
                <div class="row g-3 mb-4">
                    <div class="col-md-5">
                        <label class="form-label fw-bold text-dark small">Fornecedor / Emitente <span class="text-danger">*</span></label>
                        <select name="supplier_id" class="form-select rounded-3" required>
                            <option value="">-- Selecione o Fornecedor --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }} (NIF: {{ $supplier->nif ?? 'N/D' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small">Nº da Fatura (Documento Original) <span class="text-danger">*</span></label>
                        <input type="text" name="invoice_number" class="form-control rounded-3 font-monospace" value="{{ old('invoice_number') }}" required placeholder="Ex: FT 2026/123">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark small">Data de Emissão <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control rounded-3 font-monospace" value="{{ old('date') ?? date('Y-m-d') }}" required>
                    </div>
                </div>

                <hr class="my-4 border-light">
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-list text-secondary me-2"></i>Linhas da Fatura</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary fw-bold rounded-3" onclick="addRow()">
                        <i class="fas fa-plus me-1"></i> Adicionar Linha
                    </button>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle mb-0" id="itemsTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 45%;">Artigo / Produto / Despesa <span class="text-danger">*</span></th>
                                <th style="width: 15%;" class="text-center">Quantidade <span class="text-danger">*</span></th>
                                <th style="width: 20%;" class="text-end">Preço Unitário (AKZ) <span class="text-danger">*</span></th>
                                <th style="width: 15%;" class="text-end">Total Linha (AKZ)</th>
                                <th style="width: 5%;" class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="item-row">
                                <td>
                                    <select name="items[0][product_id]" class="form-select product-select rounded-3" onchange="updateRowPrice(this)" required>
                                        <option value="" data-price="0">-- Selecione o Artigo --</option>
                                        @foreach($products as $prod)
                                            <option value="{{ $prod->id }}" data-price="{{ $prod->unit_price ?? 0 }}">
                                                {{ $prod->code }} - {{ $prod->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0.01" name="items[0][quantity]" class="form-control text-center font-monospace row-qty rounded-3" required value="1" oninput="calculateTotals()">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" name="items[0][unit_price]" class="form-control text-end font-monospace row-price rounded-3" required placeholder="0.00" oninput="calculateTotals()">
                                </td>
                                <td class="text-end font-monospace fw-bold text-dark row-total align-middle">
                                    0,00 Kz
                                </td>
                                <td class="text-center align-middle">
                                    <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeRow(this)"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row justify-content-end align-items-center g-3 border-top pt-4">
                    <div class="col-md-5">
                        <div class="summary-card">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small fw-bold text-uppercase">Subtotal Mercadorias / Serviços:</span>
                                <span class="font-monospace fw-bold fs-6 text-dark" id="summarySubtotal">0,00 Kz</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary-subtle">
                                <span class="fw-bold text-primary text-uppercase">TOTAL GERAL DA FATURA:</span>
                                <span class="font-monospace fw-extrabold fs-4 text-primary" id="summaryTotal">0,00 Kz</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary px-5 py-2.5 fw-bold text-uppercase rounded-3 shadow-sm" id="submitBtn">
                        <i class="fas fa-save me-2"></i> Lançar Fatura
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let rowIdx = 1;

    function updateRowPrice(selectElem) {
        const selectedOption = selectElem.options[selectElem.selectedIndex];
        const price = selectedOption.getAttribute('data-price') || 0;
        const row = selectElem.closest('tr');
        const priceInput = row.querySelector('.row-price');
        
        if (priceInput) {
            priceInput.value = parseFloat(price).toFixed(2);
        }
        calculateTotals();
    }

    function addRow() {
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td>
                <select name="items[${rowIdx}][product_id]" class="form-select product-select rounded-3" onchange="updateRowPrice(this)" required>
                    <option value="" data-price="0">-- Selecione o Artigo --</option>
                    @foreach($products as $prod)
                        <option value="{{ $prod->id }}" data-price="{{ $prod->unit_price ?? 0 }}">
                            {{ $prod->code }} - {{ $prod->name }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" step="0.01" min="0.01" name="items[${rowIdx}][quantity]" class="form-control text-center font-monospace row-qty rounded-3" required value="1" oninput="calculateTotals()">
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="items[${rowIdx}][unit_price]" class="form-control text-end font-monospace row-price rounded-3" required placeholder="0.00" oninput="calculateTotals()">
            </td>
            <td class="text-end font-monospace fw-bold text-dark row-total align-middle">
                0,00 Kz
            </td>
            <td class="text-center align-middle">
                <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeRow(this)"><i class="fas fa-trash"></i></button>
            </td>
        `;
        document.querySelector('#itemsTable tbody').appendChild(tr);
        rowIdx++;
        calculateTotals();
    }

    function removeRow(btn) {
        const rows = document.querySelectorAll('#itemsTable tbody tr');
        if (rows.length > 1) {
            btn.closest('tr').remove();
            calculateTotals();
        } else {
            alert('A fatura deve conter pelo menos uma linha de artigo/despesa.');
        }
    }

    function formatKz(value) {
        return new Intl.NumberFormat('pt-AO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value) + ' Kz';
    }

    function calculateTotals() {
        let totalGeneral = 0;
        const rows = document.querySelectorAll('#itemsTable tbody tr');

        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.row-qty').value) || 0;
            const price = parseFloat(row.querySelector('.row-price').value) || 0;
            const lineTotal = qty * price;
            
            totalGeneral += lineTotal;
            
            const totalTd = row.querySelector('.row-total');
            if (totalTd) {
                totalTd.textContent = formatKz(lineTotal);
            }
        });

        document.getElementById('summarySubtotal').textContent = formatKz(totalGeneral);
        document.getElementById('summaryTotal').textContent = formatKz(totalGeneral);
    }

    document.addEventListener('DOMContentLoaded', function() {
        calculateTotals();
    });
</script>
@endpush
@endsection

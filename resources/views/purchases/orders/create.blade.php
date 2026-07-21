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
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('compras.encomendas.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="fas fa-arrow-left me-1"></i> Voltar à Listagem
        </a>
        <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-file-invoice text-primary me-2"></i>Nova Nota de Encomenda</h2>
        @if($sourceRequest)
            <p class="text-success mt-1"><i class="fas fa-info-circle me-1"></i> A gerar encomenda com base no Pedido Interno <strong>#REQ-{{ str_pad($sourceRequest->id, 4, '0', STR_PAD_LEFT) }}</strong></p>
        @endif
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

    <form action="{{ route('compras.encomendas.store') }}" method="POST">
        @csrf
        @if($sourceRequest)
            <input type="hidden" name="source_request_id" value="{{ $sourceRequest->id }}">
        @endif
        
        <div class="card-premium p-4 mb-4">
            <h5 class="fw-bold border-bottom pb-2 mb-4">Cabeçalho da Encomenda</h5>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Data de Emissão <span class="text-danger">*</span></label>
                    <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fornecedor <span class="text-danger">*</span></label>
                    <select name="supplier_id" class="form-select select2" required>
                        <option value="">Selecione o fornecedor...</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }} (NIF: {{ $supplier->tax_id }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Condições / Notas / Local de Entrega</label>
                    <textarea name="notes" class="form-control" rows="2">{{ old('notes', $sourceRequest ? "Referente ao Pedido Interno #" . str_pad($sourceRequest->id, 4, '0', STR_PAD_LEFT) . " para o Departamento de " . ($sourceRequest->department->name ?? '') : '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="card-premium p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-4">
                <h5 class="fw-bold m-0">Linhas de Artigos</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add-item-btn"><i class="fas fa-plus me-1"></i> Adicionar Artigo</button>
            </div>
            
            <div class="table-responsive">
                <table class="table" id="items-table">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 35%;">Artigo</th>
                            <th style="width: 15%;">Quantidade</th>
                            <th style="width: 15%;">Preço Unitário</th>
                            <th style="width: 10%;">IVA (%)</th>
                            <th style="width: 20%;">Total Linha (S/ IVA)</th>
                            <th style="width: 5%;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($sourceRequest && old('items') === null)
                            @foreach($sourceRequest->items as $index => $item)
                            <tr>
                                <td>
                                    <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                                    <input type="text" class="form-control bg-light" value="[{{ $item->product->code }}] {{ $item->product->name }}" readonly>
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0.01" name="items[{{ $index }}][quantity]" class="form-control calc-trigger" value="{{ $item->quantity }}" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" name="items[{{ $index }}][unit_price]" class="form-control calc-trigger" value="{{ $item->product->unit_price ?? 0 }}" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" name="items[{{ $index }}][tax_rate]" class="form-control" value="{{ $item->product->tax_rate ?? 14 }}" required>
                                </td>
                                <td>
                                    <input type="text" class="form-control bg-light total-line" value="{{ number_format($item->quantity * ($item->product->unit_price ?? 0), 2, '.', '') }}" readonly>
                                </td>
                                <td class="text-center align-middle">
                                    <button type="button" class="btn btn-sm text-danger remove-line"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <!-- Blank Line if not from request -->
                            <tr>
                                <td>
                                    <select name="items[0][product_id]" class="form-select" required>
                                        <option value="">Selecione...</option>
                                        @foreach($products as $prod)
                                            <option value="{{ $prod->id }}">[{{ $prod->code }}] {{ $prod->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0.01" name="items[0][quantity]" class="form-control calc-trigger" required placeholder="0.00">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" name="items[0][unit_price]" class="form-control calc-trigger" required placeholder="0.00">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" name="items[0][tax_rate]" class="form-control" value="14" required>
                                </td>
                                <td>
                                    <input type="text" class="form-control bg-light total-line" value="0.00" readonly>
                                </td>
                                <td class="text-center align-middle">
                                    <button type="button" class="btn btn-sm text-danger remove-line" disabled><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end fw-bold">TOTAL DA ENCOMENDA (Sem IVA):</td>
                            <td colspan="2" class="fw-bold fs-5 text-primary"><span id="grand-total">0.00</span> AOA</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="text-end mt-4">
            <a href="{{ route('compras.encomendas.index') }}" class="btn btn-light border fw-bold me-2" style="border-radius:10px; padding:0.6rem 2rem;">Cancelar</a>
            <button type="submit" class="btn btn-save"><i class="fas fa-save me-2"></i> Gravar Nota de Encomenda</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let lineIndex = 100; // secure index
        const tbody = document.querySelector('#items-table tbody');
        
        function calculateTotal() {
            let grandTotal = 0;
            const rows = tbody.querySelectorAll('tr');
            rows.forEach(row => {
                const qtyInput = row.querySelector('input[name*="[quantity]"]');
                const priceInput = row.querySelector('input[name*="[unit_price]"]');
                const totalInput = row.querySelector('.total-line');
                
                if(qtyInput && priceInput && totalInput) {
                    const qty = parseFloat(qtyInput.value) || 0;
                    const price = parseFloat(priceInput.value) || 0;
                    const lineTotal = qty * price;
                    totalInput.value = lineTotal.toFixed(2);
                    grandTotal += lineTotal;
                }
            });
            document.getElementById('grand-total').textContent = grandTotal.toFixed(2);
        }

        tbody.addEventListener('input', function(e) {
            if(e.target.classList.contains('calc-trigger')) {
                calculateTotal();
            }
        });

        document.getElementById('add-item-btn').addEventListener('click', function() {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <select name="items[${lineIndex}][product_id]" class="form-select" required>
                        <option value="">Selecione...</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}">[{{ $prod->code }}] {{ $prod->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" step="0.01" min="0.01" name="items[${lineIndex}][quantity]" class="form-control calc-trigger" required placeholder="0.00">
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="items[${lineIndex}][unit_price]" class="form-control calc-trigger" required placeholder="0.00">
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="items[${lineIndex}][tax_rate]" class="form-control" value="14" required>
                </td>
                <td>
                    <input type="text" class="form-control bg-light total-line" value="0.00" readonly>
                </td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-sm text-danger remove-line"><i class="fas fa-trash"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
            lineIndex++;
            updateRemoveButtons();
        });

        tbody.addEventListener('click', function(e) {
            if (e.target.closest('.remove-line')) {
                e.target.closest('tr').remove();
                updateRemoveButtons();
                calculateTotal();
            }
        });

        function updateRemoveButtons() {
            const rows = tbody.querySelectorAll('tr');
            const btns = tbody.querySelectorAll('.remove-line');
            btns.forEach(btn => btn.disabled = rows.length === 1);
        }

        calculateTotal(); // Initial calc
    });
</script>
@endpush
@endsection

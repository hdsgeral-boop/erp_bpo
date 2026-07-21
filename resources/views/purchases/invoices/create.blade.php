@extends('layouts.app')

@push('styles')
<style>
    .card-premium { background: #ffffff; border: none; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-file-invoice-dollar text-primary me-2"></i>Registar Fatura de Fornecedor</h2>
            <p class="text-muted mb-0">Lançamento manual de despesas ou faturas de compra.</p>
        </div>
        <div>
            <a href="{{ route('compras.faturas.index') }}" class="btn btn-secondary shadow-sm" style="border-radius: 8px;">
                <i class="fas fa-arrow-left me-2"></i>Voltar
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger shadow-sm" style="border-radius: 10px;">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-premium">
        <div class="card-body p-4">
            <form action="{{ route('compras.faturas.store') }}" method="POST">
                @csrf
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label class="form-label fw-bold">Fornecedor</label>
                        <select name="supplier_id" class="form-control" required>
                            <option value="">-- Selecione o Fornecedor --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }} (NIF: {{ $supplier->nif }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Nº da Fatura (Documento Original)</label>
                        <input type="text" name="invoice_number" class="form-control" value="{{ old('invoice_number') }}" required placeholder="Ex: FT 2026/123">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Data de Emissão</label>
                        <input type="date" name="date" class="form-control" value="{{ old('date') ?? date('Y-m-d') }}" required>
                    </div>
                </div>

                <hr class="my-4 border-light">
                <h5 class="fw-bold mb-3"><i class="fas fa-list text-secondary me-2"></i>Linhas da Fatura</h5>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="itemsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Artigo / Produto / Despesa</th>
                                <th width="15%">Quantidade</th>
                                <th width="20%">Preço Unitário (AKZ)</th>
                                <th width="5%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select name="items[0][product_id]" class="form-control" required>
                                        <option value="">-- Selecione o Artigo --</option>
                                        @foreach($products as $prod)
                                            <option value="{{ $prod->id }}">{{ $prod->code }} - {{ $prod->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" step="0.01" name="items[0][quantity]" class="form-control" required value="1"></td>
                                <td><input type="number" step="0.01" name="items[0][unit_price]" class="form-control" required></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="mb-4">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addRow()"><i class="fas fa-plus me-1"></i>Adicionar Linha</button>
                </div>

                <div class="d-flex justify-content-end border-top pt-3">
                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold" style="border-radius: 8px;"><i class="fas fa-save me-2"></i>Lançar Fatura</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let rowIdx = 1;
    function addRow() {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <select name="items[${rowIdx}][product_id]" class="form-control" required>
                    <option value="">-- Selecione o Artigo --</option>
                    @foreach($products as $prod)
                        <option value="{{ $prod->id }}">{{ $prod->code }} - {{ $prod->name }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" step="0.01" name="items[${rowIdx}][quantity]" class="form-control" required value="1"></td>
            <td><input type="number" step="0.01" name="items[${rowIdx}][unit_price]" class="form-control" required></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()"><i class="fas fa-trash"></i></button></td>
        `;
        document.querySelector('#itemsTable tbody').appendChild(tr);
        rowIdx++;
    }
</script>
@endpush
@endsection

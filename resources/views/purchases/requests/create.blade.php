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
        <a href="{{ route('compras.pedidos.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="fas fa-arrow-left me-1"></i> Voltar à Listagem
        </a>
        <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-plus text-primary me-2"></i>Novo Pedido Interno</h2>
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

    <form action="{{ route('compras.pedidos.store') }}" method="POST">
        @csrf
        <div class="card-premium p-4 mb-4">
            <h5 class="fw-bold border-bottom pb-2 mb-4">Dados do Requerente</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Data do Pedido <span class="text-danger">*</span></label>
                    <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nome do Requerente <span class="text-danger">*</span></label>
                    <input type="text" name="requester_name" class="form-control" value="{{ old('requester_name', auth()->check() ? auth()->user()->name : 'Utilizador Sistema') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Departamento <span class="text-danger">*</span></label>
                    <select name="department_id" class="form-select" required>
                        <option value="">Selecione...</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Justificação / Motivo</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Qual o motivo para este pedido?">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="card-premium p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-4">
                <h5 class="fw-bold m-0">Artigos Necessários</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add-item-btn"><i class="fas fa-plus me-1"></i> Adicionar Linha</button>
            </div>
            
            <div class="table-responsive">
                <table class="table" id="items-table">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50%;">Artigo</th>
                            <th style="width: 15%;">Quantidade</th>
                            <th style="width: 30%;">Notas da Linha</th>
                            <th style="width: 5%;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Linha 1 Predefinida -->
                        <tr>
                            <td>
                                <select name="items[0][product_id]" class="form-select" required>
                                    <option value="">Selecione o artigo...</option>
                                    @foreach($products as $prod)
                                        <option value="{{ $prod->id }}">[{{ $prod->code }}] {{ $prod->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0.01" name="items[0][quantity]" class="form-control" required placeholder="0.00">
                            </td>
                            <td>
                                <input type="text" name="items[0][notes]" class="form-control" placeholder="Tamanho, cor, etc...">
                            </td>
                            <td class="text-center align-middle">
                                <button type="button" class="btn btn-sm text-danger remove-line" disabled><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="text-end mt-4">
            <a href="{{ route('compras.pedidos.index') }}" class="btn btn-light border fw-bold me-2" style="border-radius:10px; padding:0.6rem 2rem;">Cancelar</a>
            <button type="submit" class="btn btn-save"><i class="fas fa-paper-plane me-2"></i> Submeter Pedido para Aprovação</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let lineIndex = 1;
        const tbody = document.querySelector('#items-table tbody');
        
        document.getElementById('add-item-btn').addEventListener('click', function() {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <select name="items[${lineIndex}][product_id]" class="form-select" required>
                        <option value="">Selecione o artigo...</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}">[{{ $prod->code }}] {{ $prod->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" step="0.01" min="0.01" name="items[${lineIndex}][quantity]" class="form-control" required placeholder="0.00">
                </td>
                <td>
                    <input type="text" name="items[${lineIndex}][notes]" class="form-control" placeholder="Tamanho, cor, etc...">
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
            }
        });

        function updateRemoveButtons() {
            const rows = tbody.querySelectorAll('tr');
            const btns = tbody.querySelectorAll('.remove-line');
            btns.forEach(btn => btn.disabled = rows.length === 1);
        }
    });
</script>
@endpush
@endsection

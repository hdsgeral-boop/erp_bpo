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
    .form-label {
        font-weight: 600;
        color: #475569;
        font-size: 0.9rem;
    }
    .btn-save {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border-radius: 10px;
        padding: 0.6rem 2rem;
        font-weight: 600;
        border: none;
        box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
    }
    .btn-cancel {
        background: #f1f5f9;
        color: #475569;
        border-radius: 10px;
        padding: 0.6rem 2rem;
        font-weight: 600;
        border: none;
    }
    .section-title {
        font-weight: 700;
        color: #1e293b;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 0.5rem;
        margin-bottom: 1.5rem;
    }
    .dynamic-field { display: none; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('inventario.movimentos.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="fas fa-arrow-left me-1"></i> Voltar ao Histórico
        </a>
        <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-exchange-alt text-primary me-2"></i>Registar Movimento Manual</h2>
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

    <div class="row">
        <div class="col-md-8">
            <div class="card-premium p-4 p-md-5">
                <form action="{{ route('inventario.movimentos.store') }}" method="POST">
                    @csrf
                    <h5 class="section-title">Dados do Movimento</h5>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Artigo / Produto <span class="text-danger">*</span></label>
                            <select name="product_id" class="form-select select2" required>
                                <option value="">Selecione o Artigo...</option>
                                @foreach($products as $prod)
                                    <option value="{{ $prod->id }}" {{ old('product_id') == $prod->id ? 'selected' : '' }}>[{{ $prod->code }}] {{ $prod->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tipo de Operação <span class="text-danger">*</span></label>
                            <select name="type" id="movement_type" class="form-select" required>
                                <option value="">Selecione...</option>
                                <option value="in" {{ old('type') == 'in' ? 'selected' : '' }}>Entrada de Stock</option>
                                <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>Saída de Stock</option>
                                <option value="transfer" {{ old('type') == 'transfer' ? 'selected' : '' }}>Transferência</option>
                                <option value="adjustment" {{ old('type') == 'adjustment' ? 'selected' : '' }}>Ajuste/Correção</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Quantidade <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" name="quantity" class="form-control" value="{{ old('quantity') }}" required>
                        </div>
                    </div>

                    <h5 class="section-title mt-4">Locais</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6 dynamic-field" id="field_from">
                            <label class="form-label text-danger">Armazém de Origem (Sai de) <span class="text-danger">*</span></label>
                            <select name="from_warehouse_id" class="form-select">
                                <option value="">Selecione o armazém...</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}" {{ old('from_warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 dynamic-field" id="field_to">
                            <label class="form-label text-success">Armazém de Destino (Entra em) <span class="text-danger">*</span></label>
                            <select name="to_warehouse_id" class="form-select">
                                <option value="">Selecione o armazém...</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}" {{ old('to_warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Observações / Justificação</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Ex: Ajuste devido a rutura, Transferência para a loja principal, etc.">{{ old('notes') }}</textarea>
                    </div>

                    <div class="text-end mt-4 pt-3 border-top">
                        <a href="{{ route('inventario.movimentos.index') }}" class="btn btn-cancel me-2">Cancelar</a>
                        <button type="submit" class="btn btn-save"><i class="fas fa-save me-2"></i> Confirmar Movimento</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="alert alert-info border-info" style="border-radius: 16px;">
                <h5 class="fw-bold mb-3"><i class="fas fa-info-circle me-2"></i>Guia de Movimentos</h5>
                <p class="small mb-2"><strong>Entrada:</strong> Aumenta o stock no armazém de destino. (Ex: Ofertas, sobras não encomendadas).</p>
                <p class="small mb-2"><strong>Saída:</strong> Diminui o stock do armazém de origem. (Ex: Quebras, perdas, amostras).</p>
                <p class="small mb-2"><strong>Transferência:</strong> Move quantidades de um armazém de origem para um de destino.</p>
                <p class="small mb-0"><strong>Ajuste:</strong> Para corrigir o inventário após contagem física. (Entra como positivo no destino selecionado).</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('movement_type');
        const fieldFrom = document.getElementById('field_from');
        const fieldTo = document.getElementById('field_to');
        
        function updateFields() {
            const val = typeSelect.value;
            fieldFrom.style.display = 'none';
            fieldTo.style.display = 'none';
            
            if (val === 'in' || val === 'adjustment') {
                fieldTo.style.display = 'block';
            } else if (val === 'out') {
                fieldFrom.style.display = 'block';
            } else if (val === 'transfer') {
                fieldFrom.style.display = 'block';
                fieldTo.style.display = 'block';
            }
        }
        
        typeSelect.addEventListener('change', updateFields);
        updateFields(); // run on load for old() values
    });
</script>
@endpush
@endsection

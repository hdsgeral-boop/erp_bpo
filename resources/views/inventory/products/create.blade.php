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
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('inventario.artigos.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="fas fa-arrow-left me-1"></i> Voltar à Listagem
        </a>
        <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-box text-primary me-2"></i>Novo Artigo</h2>
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

    <form action="{{ route('inventario.artigos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row g-4">
            <div class="col-md-8">
                <div class="card-premium p-4 mb-4">
                    <h5 class="section-title">Dados Gerais</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Código Interno/Referência <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control font-monospace text-uppercase" value="{{ old('code') }}" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Nome do Artigo <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Categoria <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Selecione...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <h5 class="section-title mt-4">Preços e Impostos</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Preço Base de Venda <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" name="unit_price" class="form-control" value="{{ old('unit_price', '0.00') }}" required>
                                <span class="input-group-text">AOA</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Taxa de IVA (%) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" max="100" name="tax_rate" class="form-control" value="{{ old('tax_rate', '14.00') }}" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="section-title mt-4">Códigos Contabilísticos (Opcional)</h5>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Conta Base</label>
                            <input type="text" name="account_code" class="form-control" value="{{ old('account_code') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Conta Compras</label>
                            <input type="text" name="account_purchase" class="form-control" value="{{ old('account_purchase') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Conta Custos</label>
                            <input type="text" name="account_cost" class="form-control" value="{{ old('account_cost') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Conta Inventário</label>
                            <input type="text" name="account_inventory" class="form-control" value="{{ old('account_inventory') }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-premium p-4 mb-4">
                    <h5 class="section-title">Comportamento</h5>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_inventory" id="is_inventory" value="1" {{ old('is_inventory', true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="is_inventory">Controla Stock</label>
                        <div class="form-text">Se ativo, o sistema exigirá stock para efetuar vendas.</div>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_asset" id="is_asset" value="1" {{ old('is_asset') ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="is_asset">É Imobilizado (Ativo)</label>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_blocked" id="is_blocked" value="1" {{ old('is_blocked') ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold text-danger" for="is_blocked">Artigo Bloqueado</label>
                        <div class="form-text">Impede a venda ou movimentação deste artigo.</div>
                    </div>
                </div>

                <div class="card-premium p-4">
                    <h5 class="section-title">Anexos / Imagens</h5>
                    <div class="mb-3">
                        <label class="form-label">Fotos ou Fichas Técnicas</label>
                        <input class="form-control" type="file" name="attachments[]" multiple>
                        <div class="form-text">Máximo 10MB por ficheiro.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-premium p-4 mt-4 text-end">
            <a href="{{ route('inventario.artigos.index') }}" class="btn btn-cancel me-2">Cancelar</a>
            <button type="submit" class="btn btn-save"><i class="fas fa-save me-2"></i> Registar Artigo</button>
        </div>
    </form>
</div>
@endsection

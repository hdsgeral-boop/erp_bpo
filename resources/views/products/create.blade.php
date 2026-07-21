@extends('layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    .card-premium {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        padding: 2rem;
    }
    .form-label {
        font-weight: 600;
        color: #475569;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }
    .form-control, .form-select {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        padding: 0.6rem 1rem;
        font-size: 0.95rem;
        transition: all 0.2s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }
    .btn-save {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border: none;
        border-radius: 10px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
        transition: all 0.2s;
        color: white;
    }
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4);
        color: white;
    }
    .section-title {
        color: #1e293b;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 0.5rem;
        margin-bottom: 1.5rem;
        margin-top: 1.5rem;
        font-weight: 700;
        font-size: 1.1rem;
    }
    .form-check-input:checked {
        background-color: #3b82f6;
        border-color: #3b82f6;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-box text-primary me-2"></i>Novo Artigo</h2>
            <p class="text-muted mb-0">Adicionar um novo produto ou serviço ao catálogo.</p>
        </div>
        <a href="{{ route('logistica.products.index') }}" class="btn btn-light shadow-sm fw-bold"><i class="fas fa-arrow-left"></i> Voltar</a>
    </div>

    <div class="card-premium">
        @if($errors->any())
            <div class="alert alert-danger" style="border-radius: 10px;">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('logistica.products.store') }}" method="POST">
            @csrf
            
            <div class="section-title"><i class="fas fa-info-circle text-primary me-2"></i> Informações Principais</div>
            
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <label class="form-label">Código <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control" required value="{{ old('code') }}" placeholder="Ex: ART001">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nome do Artigo <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Categoria</label>
                    <select name="category_id" class="form-select">
                        <option value="">Sem Categoria</option>
                        @foreach(\App\Models\ProductCategory::all() as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <label class="form-label">Preço Unitário (Kz) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="unit_price" class="form-control" required value="{{ old('unit_price', '0.00') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Taxa de IVA (%) <span class="text-danger">*</span></label>
                    <select name="tax_rate" class="form-select" required>
                        <option value="14" {{ old('tax_rate') == '14' ? 'selected' : '' }}>Taxa Normal (14%)</option>
                        <option value="0" {{ old('tax_rate') == '0' ? 'selected' : '' }}>Isento (0%)</option>
                        <option value="7" {{ old('tax_rate') == '7' ? 'selected' : '' }}>Reduzida (7%)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tipo de Artigo <span class="text-danger">*</span></label>
                    <select name="is_inventory" class="form-select" required>
                        <option value="1" {{ old('is_inventory') == '1' ? 'selected' : '' }}>Produto Físico (Com Stock)</option>
                        <option value="0" {{ old('is_inventory') == '0' ? 'selected' : '' }}>Serviço (Sem Stock)</option>
                    </select>
                </div>
            </div>

            <div class="section-title"><i class="fas fa-file-invoice-dollar text-primary me-2"></i> Configurações Contabilísticas e Estado</div>

            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <label class="form-label">Conta de Vendas (61)</label>
                    <input type="text" name="account_code" class="form-control" value="{{ old('account_code') }}" placeholder="Ex: 61.1.1">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Conta de Custo (71)</label>
                    <input type="text" name="account_cost" class="form-control" value="{{ old('account_cost') }}" placeholder="Ex: 71.1.1">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Conta de Compras (21)</label>
                    <input type="text" name="account_purchase" class="form-control" value="{{ old('account_purchase') }}" placeholder="Ex: 21.1.1">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Conta de Stock (22)</label>
                    <input type="text" name="account_inventory" class="form-control" value="{{ old('account_inventory') }}" placeholder="Ex: 22.1.1">
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_asset" name="is_asset" value="1" {{ old('is_asset') ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold text-muted" for="is_asset">É Imobilizado? (Ativo Fixo)</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_blocked" name="is_blocked" value="1" {{ old('is_blocked') ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold text-danger" for="is_blocked">Bloquear Venda/Compra</label>
                    </div>
                </div>
            </div>
            
            <div class="text-end mt-4 pt-3" style="border-top: 1px solid #f1f5f9;">
                <button type="submit" class="btn btn-save"><i class="fas fa-save me-2"></i> Guardar Produto</button>
            </div>
        </form>
    </div>
</div>
@endsection

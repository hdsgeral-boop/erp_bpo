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
        transition: all 0.2s;
    }
    .btn-save:hover { color: white; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.4); }
    .btn-cancel {
        background: #f1f5f9;
        color: #475569;
        border-radius: 10px;
        padding: 0.6rem 2rem;
        font-weight: 600;
        border: none;
        transition: all 0.2s;
    }
    .btn-cancel:hover { background: #e2e8f0; color: #1e293b; }
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
        <a href="{{ route('ativos.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="fas fa-arrow-left me-1"></i> Voltar à Listagem
        </a>
        <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-box text-primary me-2"></i>Registar Novo Ativo</h2>
        <p class="text-muted mb-0">Introduza os dados de aquisição e alocação do imobilizado.</p>
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

    <form action="{{ route('ativos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row g-4">
            <!-- Left Column -->
            <div class="col-md-8">
                <div class="card-premium p-4 p-md-5 mb-4">
                    <h5 class="section-title">Dados do Equipamento</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Código Interno <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control font-monospace text-uppercase" value="{{ old('code') }}" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Nome / Descrição <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Categoria de Ativo <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Selecione...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado Inicial</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Ativo</option>
                                <option value="sold" {{ old('status') == 'sold' ? 'selected' : '' }}>Vendido</option>
                                <option value="written_off" {{ old('status') == 'written_off' ? 'selected' : '' }}>Abatido</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Observações</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <h5 class="section-title mt-5">Aquisição e Finanças</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Fornecedor</label>
                            <select name="vendor_id" class="form-select">
                                <option value="">Selecione um fornecedor...</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Data de Aquisição <span class="text-danger">*</span></label>
                            <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Valor de Compra <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" name="purchase_value" class="form-control" value="{{ old('purchase_value') }}" required>
                                <span class="input-group-text">AOA</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Valor Residual (Atual)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" name="residual_value" class="form-control" value="{{ old('residual_value') }}">
                                <span class="input-group-text">AOA</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Vida Útil (Anos)</label>
                            <div class="input-group">
                                <input type="number" min="1" name="useful_life_years" class="form-control" value="{{ old('useful_life_years') }}">
                                <span class="input-group-text">Anos</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-4">
                <div class="card-premium p-4 mb-4">
                    <h5 class="section-title">Alocação e Localização</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">Departamento</label>
                        <select name="department_id" class="form-select">
                            <option value="">Não associar a Departamento</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Funcionário Responsável</label>
                        <select name="employee_id" class="form-select">
                            <option value="">Não associar a Funcionário</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Localização Física</label>
                        <input type="text" name="location" class="form-control" value="{{ old('location') }}" placeholder="Ex: Edifício A, Piso 2, Sala 12">
                    </div>
                </div>

                <div class="card-premium p-4">
                    <h5 class="section-title">Anexos</h5>
                    <div class="mb-3">
                        <label class="form-label">Faturas, Manuais, Garantias</label>
                        <input class="form-control" type="file" name="attachments[]" multiple>
                        <div class="form-text">Pode selecionar múltiplos ficheiros (Máx 10MB cada).</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-premium p-4 mt-4 text-end">
            <a href="{{ route('ativos.index') }}" class="btn btn-cancel me-2">Cancelar</a>
            <button type="submit" class="btn btn-save"><i class="fas fa-save me-2"></i> Registar Ativo</button>
        </div>
    </form>
</div>
@endsection

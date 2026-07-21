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
    .form-control:disabled, .form-control[readonly] { background-color: #f1f5f9; opacity: 1; }
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
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('config.document-series.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="fas fa-arrow-left me-1"></i> Voltar à Listagem
        </a>
        <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-edit text-primary me-2"></i>Editar Série: {{ $documentSeries->identifier }}</h2>
        <p class="text-muted mb-0">Atualizar configurações da série de documentos.</p>
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
            {{ session('error') }}
        </div>
    @endif

    <div class="card-premium p-4 p-md-5">
        <form action="{{ route('config.document-series.update', $documentSeries->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row g-4">
                <div class="col-md-6">
                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-4">Informação da Série</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
                        <select name="document_type" class="form-select" required {{ $documentSeries->current_number > 0 ? 'disabled' : '' }}>
                            <option value="">Selecione o Tipo...</option>
                            @foreach($documentTypes as $code => $name)
                                <option value="{{ $code }}" {{ old('document_type', $documentSeries->document_type) == $code ? 'selected' : '' }}>
                                    [{{ $code }}] - {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @if($documentSeries->current_number > 0)
                            <input type="hidden" name="document_type" value="{{ $documentSeries->document_type }}">
                            <div class="form-text text-danger">Não editável pois a série já tem documentos emitidos.</div>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Identificador da Série <span class="text-danger">*</span></label>
                        <input type="text" name="identifier" class="form-control" value="{{ old('identifier', $documentSeries->identifier) }}" required {{ $documentSeries->current_number > 0 ? 'readonly' : '' }}>
                        @if($documentSeries->current_number > 0)
                            <div class="form-text text-danger">Não editável pois a série já tem documentos emitidos.</div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descrição Interna</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $documentSeries->description) }}</textarea>
                    </div>
                </div>

                <div class="col-md-6">
                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-4">Regras e Atribuição</h5>

                    <div class="mb-3">
                        <label class="form-label">Empresa <span class="text-danger">*</span></label>
                        <select name="company_id" class="form-select" required {{ $documentSeries->current_number > 0 ? 'disabled' : '' }}>
                            <option value="">Selecione a Empresa...</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id', $documentSeries->company_id) == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                        @if($documentSeries->current_number > 0)
                            <input type="hidden" name="company_id" value="{{ $documentSeries->company_id }}">
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Numerador Atual (Último emitido) <span class="text-danger">*</span></label>
                        <input type="number" name="current_number" class="form-control" value="{{ old('current_number', $documentSeries->current_number) }}" min="{{ $documentSeries->current_number }}" required>
                        <div class="form-text text-warning"><i class="fas fa-exclamation-triangle"></i> Atenção: Alterar este valor forçará o próximo documento a assumir (Valor + 1). Não pode diminuir.</div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="is_active" {{ old('is_active', $documentSeries->is_active) ? 'checked' : '' }} value="1">
                                <label class="form-check-label text-dark" for="is_active">
                                    Série Ativa
                                </label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="is_default" id="is_default" {{ old('is_default', $documentSeries->is_default) ? 'checked' : '' }} value="1">
                                <label class="form-check-label text-dark" for="is_default">
                                    Série Padrão
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3 mt-5 border-top pt-4">
                <a href="{{ route('config.document-series.index') }}" class="btn btn-cancel">Cancelar</a>
                <button type="submit" class="btn btn-save"><i class="fas fa-save me-2"></i> Guardar Alterações</button>
            </div>
        </form>
    </div>
</div>
@endsection

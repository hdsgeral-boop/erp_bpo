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
        <a href="{{ route('entidades.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="fas fa-arrow-left me-1"></i> Voltar à Listagem
        </a>
        <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-plus-square text-primary me-2"></i>Nova Entidade</h2>
        <p class="text-muted mb-0">Criar um novo registo de cliente ou fornecedor no sistema.</p>
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

    <form action="{{ route('entidades.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row g-4">
            <!-- Left Column -->
            <div class="col-md-8">
                <div class="card-premium p-4 p-md-5 h-100">
                    <h5 class="section-title">Identificação da Entidade</h5>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label">Nome Completo / Designação Social <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Ex: Consulvolt Lda">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">NIF (Opcional)</label>
                            <input type="text" name="nif" class="form-control" value="{{ old('nif') }}" placeholder="Ex: 500123456">
                        </div>
                    </div>

                    <h5 class="section-title mt-5">Contactos e Morada</h5>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Email Principal</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="contacto@empresa.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefone / Telemóvel</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+244 9xx xxx xxx">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Website</label>
                            <input type="url" name="website" class="form-control" value="{{ old('website') }}" placeholder="https://www.empresa.com">
                        </div>
                        
                        <div class="col-12 mt-3">
                            <label class="form-label">Morada Completa</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address') }}" placeholder="Rua, Número, Andar...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Código Postal</label>
                            <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code') }}" placeholder="Ex: 1000-001">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Cidade / Município</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city') }}" placeholder="Ex: Luanda">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">País</label>
                            <input type="text" name="country" class="form-control" value="{{ old('country', 'Angola') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-4">
                <div class="card-premium p-4 mb-4">
                    <h5 class="section-title">Papéis e Classificação</h5>
                    
                    <div class="p-3 bg-light rounded border mb-3">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" name="is_customer" id="is_customer" {{ old('is_customer') ? 'checked' : '' }} value="1">
                            <label class="form-check-label text-dark fw-bold" for="is_customer">É Cliente</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" name="is_supplier" id="is_supplier" {{ old('is_supplier') ? 'checked' : '' }} value="1">
                            <label class="form-check-label text-dark fw-bold" for="is_supplier">É Fornecedor</label>
                        </div>
                        <div class="form-check form-switch mt-3 pt-3 border-top">
                            <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="is_active" {{ old('is_active', true) ? 'checked' : '' }} value="1">
                            <label class="form-check-label text-success fw-bold" for="is_active">Entidade Ativa</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Conta SNC (Deixar vazio para auto)</label>
                        <input type="text" name="account_code" class="form-control font-monospace" value="{{ old('account_code') }}" placeholder="Ex: 21.1.1.0001">
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Observações Internas</label>
                        <textarea name="observations" class="form-control" rows="3">{{ old('observations') }}</textarea>
                    </div>
                </div>

                <div class="card-premium p-4">
                    <h5 class="section-title">Documentos Anexos</h5>
                    <div class="mb-3">
                        <label class="form-label">Carregar Ficheiros</label>
                        <input class="form-control" type="file" name="attachments[]" multiple>
                        <div class="form-text">Pode selecionar múltiplos ficheiros (Máx 10MB cada).</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Footer for Save -->
        <div class="card-premium p-4 mt-4 text-end">
            <a href="{{ route('entidades.index') }}" class="btn btn-cancel me-2">Cancelar</a>
            <button type="submit" class="btn btn-save"><i class="fas fa-save me-2"></i> Guardar Entidade</button>
        </div>
    </form>
</div>
@endsection

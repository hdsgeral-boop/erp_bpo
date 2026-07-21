@extends('layouts.app')

@push('styles')
<style>
    .card-premium { background: #ffffff; border: none; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); }
    .form-control, .form-select { border-radius: 8px; padding: 0.75rem 1rem; }
    .form-control:focus, .form-select:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('config.taxes.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="fas fa-arrow-left me-1"></i> Voltar aos Impostos
        </a>
        <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-plus-circle text-primary me-2"></i>Nova Regra Fiscal</h2>
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

    <div class="card-premium p-4">
        <form action="{{ route('config.taxes.store') }}" method="POST">
            @csrf
            
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Empresa <span class="text-danger">*</span></label>
                    <select name="company_id" class="form-select" required>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-bold">Nome da Taxa <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Ex: IVA 14% Normal" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Código Interno / Legal <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control" value="{{ old('code') }}" placeholder="Ex: NOR, ISE, OUT" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Tipo de Imposto <span class="text-danger">*</span></label>
                    <select name="type" class="form-select" required>
                        <option value="VAT" {{ old('type') == 'VAT' ? 'selected' : '' }}>IVA (Imposto Valor Acrescentado)</option>
                        <option value="RETENTION" {{ old('type') == 'RETENTION' ? 'selected' : '' }}>Retenção na Fonte</option>
                        <option value="STAMP" {{ old('type') == 'STAMP' ? 'selected' : '' }}>Imposto do Selo</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Taxa (%) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="rate" class="form-control text-primary fw-bold fs-5" value="{{ old('rate', '14.00') }}" required>
                </div>

                <div class="col-12 mt-4 pt-3 border-top">
                    <div class="alert alert-warning border-warning bg-warning bg-opacity-10 d-flex align-items-center">
                        <i class="fas fa-info-circle fa-2x me-3 text-warning"></i>
                        <div>
                            <strong>Atenção às Isenções (0%):</strong> Se a taxa for 0%, é obrigatório por lei (ex: AGT) indicar o motivo da isenção que será impresso nas faturas.
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">Motivo de Isenção Legal (Obrigatório se taxa = 0%)</label>
                    <input type="text" name="exemption_reason" class="form-control" value="{{ old('exemption_reason') }}" placeholder="Ex: M04 - Isento Artigo 9º do CIVA">
                </div>
                
                <div class="col-12">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="isActive" checked>
                        <label class="form-check-label fw-bold" for="isActive">Imposto Ativo (Disponível para uso imediato)</label>
                    </div>
                </div>
            </div>

            <div class="text-end mt-4 pt-3 border-top">
                <a href="{{ route('config.taxes.index') }}" class="btn btn-light border fw-bold me-2 px-4" style="border-radius:10px;">Cancelar</a>
                <button type="submit" class="btn btn-primary fw-bold px-4" style="border-radius:10px;"><i class="fas fa-save me-2"></i> Gravar Regra Fiscal</button>
            </div>
        </form>
    </div>
</div>
@endsection

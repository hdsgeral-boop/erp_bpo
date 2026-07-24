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
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('rh.contratos.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="fas fa-arrow-left me-1"></i> Voltar aos Contratos
        </a>
        <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-file-contract text-primary me-2"></i>Novo Contrato / Vínculo Salarial</h2>
        <p class="text-muted mb-0">Configurar termo contratual, vencimento base e subsídios por infotipo.</p>
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

    <div class="card-premium p-4 p-md-5" style="max-width: 900px;">
        <form action="{{ route('rh.contratos.store') }}" method="POST">
            @csrf
            
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Colaborador <span class="text-danger">*</span></label>
                    <select name="employee_id" class="form-select" required>
                        <option value="">Selecione o Colaborador...</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Infotipo (Remuneração / Subsídio) <span class="text-danger">*</span></label>
                    <select name="infotype_id" class="form-select" required>
                        <option value="">Selecione o Infotipo...</option>
                        @foreach($infotypes as $info)
                            <option value="{{ $info->id }}" {{ old('infotype_id') == $info->id ? 'selected' : '' }}>{{ $info->name }} ({{ strtoupper($info->type) }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Valor Remuneratório <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" step="0.01" min="0" name="value" class="form-control font-monospace" value="{{ old('value') }}" placeholder="0.00" required>
                        <span class="input-group-text fw-bold">AOA</span>
                    </div>
                    <div class="form-text">Vencimento mensal em kwanzas ou montante do benefício associado.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Data de Início de Vigor <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Data de Cessação <span class="text-muted">(Opcional)</span></label>
                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                    <div class="form-text">Manter em branco para contratos por tempo indeterminado.</div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3 mt-5 border-top pt-4">
                <a href="{{ route('rh.contratos.index') }}" class="btn btn-cancel">Cancelar</a>
                <button type="submit" class="btn btn-save"><i class="fas fa-save me-2"></i> Celebrar Contrato</button>
            </div>
        </form>
    </div>
</div>
@endsection

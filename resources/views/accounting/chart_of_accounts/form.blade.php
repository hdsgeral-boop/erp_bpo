@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-auto">
            <a href="{{ route('contabilidade.chart_of_accounts.index') }}" class="btn btn-light border shadow-sm">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
        <div class="col">
            <h2 class="mb-0 fw-bold">{{ isset($account) ? 'Editar Conta: ' . $account->code : 'Nova Conta' }}</h2>
            <p class="text-muted mb-0">Adicione ou modifique uma rubrica do plano de contas</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="{{ isset($account) ? route('contabilidade.chart_of_accounts.update', $account->id) : route('contabilidade.chart_of_accounts.store') }}" method="POST">
                @csrf
                @if(isset($account))
                    @method('PUT')
                @endif

                <div class="row g-4">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Código da Conta <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control form-control-lg @error('code') is-invalid @enderror" value="{{ old('code', $account->code ?? '') }}" required placeholder="Ex: 31.1.2">
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-9">
                        <label class="form-label fw-bold">Descrição / Nome <span class="text-danger">*</span></label>
                        <input type="text" name="description" class="form-control form-control-lg @error('description') is-invalid @enderror" value="{{ old('description', $account->description ?? '') }}" required placeholder="Ex: Clientes Nacionais">
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tipo de Conta <span class="text-danger">*</span></label>
                        <select name="type" class="form-select form-select-lg" required>
                            <option value="I" {{ old('type', $account->type ?? '') == 'I' ? 'selected' : '' }}>Integradora (Agrupadora)</option>
                            <option value="M" {{ old('type', $account->type ?? '') == 'M' ? 'selected' : '' }}>Movimento (Regista Valores)</option>
                        </select>
                        <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle"></i> Apenas as contas de Movimento (M) podem receber lançamentos manuais.</small>
                    </div>

                    <div class="col-md-6 d-flex align-items-center mt-5">
                        <div class="form-check form-switch form-check-lg">
                            <input class="form-check-input" type="checkbox" role="switch" id="isMasterData" name="is_master_data" value="1" {{ old('is_master_data', $account->is_master_data ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label ms-2 fw-bold" for="isMasterData">Conta de Sistema (Não permite eliminação)</label>
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                
                <div class="d-flex justify-content-end gap-3">
                    <a href="{{ route('contabilidade.chart_of_accounts.index') }}" class="btn btn-light border px-4 py-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold">
                        <i class="fas fa-save me-2"></i> {{ isset($account) ? 'Atualizar Conta' : 'Gravar Conta' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        background: #ffffff;
    }
    .form-control:focus, .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('tesouraria.accounts.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="fas fa-arrow-left me-1"></i> Voltar às Contas
        </a>
        <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-university text-primary me-2"></i>Nova Conta de Tesouraria</h2>
        <p class="text-muted mt-1">Crie um banco ou caixa para gerir os recebimentos e pagamentos.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger shadow-sm" style="border-radius: 10px;">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-premium p-4">
                <form action="{{ route('tesouraria.accounts.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Nome da Conta (Banco / Caixa) <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Ex: Banco BAI (Principal), Caixa do Escritório..." value="{{ old('name') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Moeda <span class="text-danger">*</span></label>
                            <select name="currency" class="form-select" required>
                                <option value="AOA" {{ old('currency') == 'AOA' ? 'selected' : '' }}>AOA - Kwanza Angolano</option>
                                <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD - Dólar Americano</option>
                                <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Saldo Inicial <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="initial_balance" class="form-control" placeholder="0.00" value="{{ old('initial_balance', '0.00') }}" required>
                                <span class="input-group-text bg-light">AOA</span>
                            </div>
                            <small class="text-muted">Este valor será o saldo de abertura desta conta.</small>
                        </div>

                        <div class="col-12 mt-4">
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked>
                                <label class="form-check-label fs-6 mt-1 ms-2" for="isActive">Conta Ativa (Pode receber e fazer pagamentos)</label>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-5 pt-3 border-top">
                        <a href="{{ route('tesouraria.accounts.index') }}" class="btn btn-light border fw-bold me-2 px-4" style="border-radius:10px;">Cancelar</a>
                        <button type="submit" class="btn btn-primary fw-bold px-4" style="border-radius:10px; font-size: 1.1rem;">
                            <i class="fas fa-save me-2"></i> Gravar Conta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

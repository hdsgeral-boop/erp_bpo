@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff; border: none; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); padding: 2rem;
    }
    .form-label-custom { font-weight: 600; color: #475569; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .form-control-custom { border-radius: 10px; border: 1px solid #cbd5e1; padding: 0.75rem 1rem; background-color: #f8fafc; }
    .form-control-custom:focus { background-color: #ffffff; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0 text-dark">Adicionar Movimento Bancário Manual</h2>
        <a href="{{ route('tesouraria.bank_statements.index') }}" class="btn btn-light border">Voltar</a>
    </div>

    <form action="{{ route('tesouraria.bank_statements.store') }}" method="POST">
        @csrf
        <div class="card-premium max-w-4xl mx-auto">
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label class="form-label-custom">Conta Bancária <span class="text-danger">*</span></label>
                    <select name="account_code" class="form-select form-control-custom" required>
                        <option value="">Selecione...</option>
                        @foreach(\App\Models\TreasuryAccount::where('type', 'BANK')->get() as $acc)
                            <option value="{{ $acc->code }}">{{ $acc->name }} ({{ $acc->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Data do Movimento <span class="text-danger">*</span></label>
                    <input type="date" name="date" class="form-control form-control-custom" required value="{{ date('Y-m-d') }}">
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-8">
                    <label class="form-label-custom">Descrição (Pelo Banco) <span class="text-danger">*</span></label>
                    <input type="text" name="description" class="form-control form-control-custom" required placeholder="Ex: TRF. LIQUIDACAO FATURA 001">
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom">Referência (Opcional)</label>
                    <input type="text" name="reference" class="form-control form-control-custom" placeholder="ID da Transação">
                </div>
            </div>

            <div class="row g-4 mb-5 p-4 rounded-3" style="background-color: #f1f5f9;">
                <div class="col-md-6">
                    <label class="form-label-custom">Tipo de Movimento <span class="text-danger">*</span></label>
                    <select name="type_dc" class="form-select form-control-custom fw-bold" required>
                        <option value="C" class="text-success">Entrada / Depósito (+ Crédito no Banco)</option>
                        <option value="D" class="text-danger">Saída / Pagamento (- Débito no Banco)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Valor do Movimento (Kz) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="value" class="form-control form-control-custom text-end fw-bold" required value="0">
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary px-5 py-3 fw-bold" style="border-radius: 10px;">
                    <i class="fas fa-save me-2"></i> Gravar Movimento
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

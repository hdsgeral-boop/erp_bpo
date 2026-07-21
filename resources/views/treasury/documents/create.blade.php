@extends('layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    .card-premium {
        background: #ffffff; border: none; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); padding: 2rem; margin-bottom: 2rem;
    }
    .form-label-custom { font-weight: 600; color: #475569; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .form-control-custom { border-radius: 10px; border: 1px solid #cbd5e1; padding: 0.75rem 1rem; background-color: #f8fafc; }
    .form-control-custom:focus { background-color: #ffffff; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
    .btn-save { background: linear-gradient(135deg, #10b981, #059669); color: white; border-radius: 10px; padding: 0.75rem 2rem; font-weight: 600; border: none; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0 text-dark">Novo Documento de Tesouraria</h2>
        <a href="{{ route('tesouraria.documents.index') }}" class="btn btn-light border">Voltar</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger shadow-sm">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('tesouraria.documents.store') }}" method="POST">
        @csrf
        <div class="card-premium">
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <label class="form-label-custom">Tipo de Documento <span class="text-danger">*</span></label>
                    <select name="type" class="form-select form-control-custom" required>
                        <option value="RC">Recebimento (Entrada)</option>
                        <option value="PG">Pagamento (Saída)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom">Data <span class="text-danger">*</span></label>
                    <input type="date" name="doc_date" class="form-control form-control-custom" required value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom">Conta Bancária / Caixa <span class="text-danger">*</span></label>
                    <input type="text" name="account_fin" class="form-control form-control-custom" required placeholder="Ex: CX01, BANC01">
                </div>
            </div>
            
            <div class="row g-4 mb-4">
                <div class="col-md-8">
                    <label class="form-label-custom">Descrição <span class="text-danger">*</span></label>
                    <input type="text" name="description" class="form-control form-control-custom" required placeholder="Motivo do movimento">
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom">Referência do Documento Origem</label>
                    <input type="text" name="reference" class="form-control form-control-custom" placeholder="Ex: FT 2026/123">
                </div>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-4 offset-md-8">
                    <label class="form-label-custom">Valor Total (Kz) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="total_value" class="form-control form-control-custom fs-5 text-end text-primary" required>
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-save"><i class="fas fa-save me-2"></i> Registar Movimento</button>
            </div>
        </div>
    </form>
</div>
@endsection

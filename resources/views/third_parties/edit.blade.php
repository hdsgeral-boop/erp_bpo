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
    .btn-delete {
        background: white;
        border: 1px solid #fca5a5;
        color: #ef4444;
        border-radius: 10px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-delete:hover {
        background: #fef2f2;
        border-color: #ef4444;
        color: #dc2626;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-edit text-primary me-2"></i>Editar Entidade</h2>
            <p class="text-muted mb-0">Atualizar os dados de {{ $entidade->name }}.</p>
        </div>
        <a href="{{ route('entidades.index') }}" class="btn btn-light shadow-sm fw-bold"><i class="fas fa-arrow-left"></i> Voltar</a>
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

        <form action="{{ route('entidades.update', $entidade) }}" method="POST">
            @csrf
            @method('PUT')
            
            <h5 class="fw-bold mb-4" style="color: #1e293b; border-bottom: 2px solid #f1f5f9; padding-bottom: 0.5rem;"><i class="fas fa-info-circle text-primary me-2"></i> Dados Principais</h5>
            
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nome ou Designação Social <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name', $entidade->name) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">NIF</label>
                    <input type="text" name="nif" class="form-control" value="{{ old('nif', $entidade->nif) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tipo de Entidade <span class="text-danger">*</span></label>
                    <select name="type" class="form-select" required>
                        <option value="CUSTOMER" {{ old('type', $entidade->type) == 'CUSTOMER' ? 'selected' : '' }}>Cliente</option>
                        <option value="SUPPLIER" {{ old('type', $entidade->type) == 'SUPPLIER' ? 'selected' : '' }}>Fornecedor</option>
                    </select>
                </div>
            </div>

            <h5 class="fw-bold mb-4" style="color: #1e293b; border-bottom: 2px solid #f1f5f9; padding-bottom: 0.5rem;"><i class="fas fa-address-book text-primary me-2"></i> Contactos e Contabilidade</h5>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Endereço</label>
                    <input type="text" name="address" class="form-control" placeholder="Morada completa" value="{{ old('address', $entidade->address) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="contacto@empresa.com" value="{{ old('email', $entidade->email) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Telefone</label>
                    <input type="text" name="phone" class="form-control" placeholder="+244 ..." value="{{ old('phone', $entidade->phone) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Conta Contabilística</label>
                    <input type="text" name="account_code" class="form-control" placeholder="Ex: 31.1.2.1" value="{{ old('account_code', $entidade->account_code) }}">
                </div>
            </div>
            
            <div class="text-end mt-4 pt-3" style="border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between;">
                <button type="button" onclick="if(confirm('Tem certeza absoluta que quer eliminar esta entidade?')) document.getElementById('delete-form').submit();" class="btn btn-delete"><i class="fas fa-trash me-2"></i> Eliminar</button>
                <button type="submit" class="btn btn-save"><i class="fas fa-save me-2"></i> Atualizar Entidade</button>
            </div>
        </form>

        <form id="delete-form" action="{{ route('entidades.destroy', $entidade) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection

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
    }
    .btn-cancel {
        background: #f1f5f9;
        color: #475569;
        border-radius: 10px;
        padding: 0.6rem 2rem;
        font-weight: 600;
        border: none;
    }
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
        <a href="{{ route('inventario.armazens.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="fas fa-arrow-left me-1"></i> Voltar à Listagem
        </a>
        <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-edit text-primary me-2"></i>Editar Armazém: {{ $warehouse->name }}</h2>
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
    
    @if(session('success'))
        <div class="alert alert-success shadow-sm" style="border-radius: 10px;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card-premium p-4 p-md-5">
                <form action="{{ route('inventario.armazens.update', $warehouse->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <h5 class="section-title">Detalhes do Local</h5>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Código Interno <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control font-monospace text-uppercase" value="{{ old('code', $warehouse->code) }}" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Nome do Armazém <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $warehouse->name) }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Localização Físisca / Morada</label>
                            <input type="text" name="location" class="form-control" value="{{ old('location', $warehouse->location) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Estado <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="Ativo" {{ old('status', $warehouse->status ?? 'Ativo') == 'Ativo' ? 'selected' : '' }}>Ativo</option>
                                <option value="Inativo" {{ old('status', $warehouse->status ?? 'Ativo') == 'Inativo' ? 'selected' : '' }}>Inativo</option>
                            </select>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <a href="{{ route('inventario.armazens.index') }}" class="btn btn-cancel me-2">Cancelar</a>
                        <button type="submit" class="btn btn-save"><i class="fas fa-save me-2"></i> Atualizar Armazém</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

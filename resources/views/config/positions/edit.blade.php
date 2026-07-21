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
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('config.positions.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="fas fa-arrow-left me-1"></i> Voltar à Listagem
        </a>
        <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-edit text-primary me-2"></i>Editar Cargo</h2>
        <p class="text-muted mb-0">Atualize as definições de {{ $position->title }}.</p>
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

    <div class="card-premium p-4 p-md-5">
        <form action="{{ route('config.positions.update', $position->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row g-4">
                <div class="col-md-6">
                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-4">Informação Principal</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">Código Único <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" value="{{ old('code', $position->code) }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Título do Cargo <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $position->title) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descrição das Funções</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $position->description) }}</textarea>
                    </div>
                </div>

                <div class="col-md-6">
                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-4">Enquadramento</h5>

                    <div class="mb-3">
                        <label class="form-label">Departamento</label>
                        <select name="department_id" class="form-select">
                            <option value="">Transversal (Nenhum específico)</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $position->department_id) == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }} ({{ $dept->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" role="switch" name="is_management" id="is_management" {{ old('is_management', $position->is_management) ? 'checked' : '' }} value="1">
                            <label class="form-check-label ms-2 text-dark" for="is_management">
                                <strong>Cargo de Chefia/Direção</strong><br>
                                <small class="text-muted">Marque se este cargo tem responsabilidades de gestão sobre outros utilizadores.</small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3 mt-5 border-top pt-4">
                <a href="{{ route('config.positions.index') }}" class="btn btn-cancel">Cancelar</a>
                <button type="submit" class="btn btn-save"><i class="fas fa-save me-2"></i> Guardar Alterações</button>
            </div>
        </form>
    </div>
</div>
@endsection

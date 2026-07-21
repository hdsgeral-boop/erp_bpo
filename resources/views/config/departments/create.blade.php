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
        <a href="{{ route('config.departments.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="fas fa-arrow-left me-1"></i> Voltar à Listagem
        </a>
        <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-plus-square text-primary me-2"></i>Novo Departamento</h2>
        <p class="text-muted mb-0">Adicionar um novo nó à estrutura organizacional.</p>
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
        <form action="{{ route('config.departments.store') }}" method="POST">
            @csrf
            
            <div class="row g-4">
                <div class="col-md-6">
                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-4">Informação Principal</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">Código Único <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" value="{{ old('code') }}" required placeholder="Ex: DEP-RH">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nome do Departamento <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Ex: Recursos Humanos">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Descrição ou missão do departamento...">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="col-md-6">
                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-4">Estrutura e Responsabilidade</h5>

                    <div class="mb-3">
                        <label class="form-label">Empresa <span class="text-danger">*</span></label>
                        <select name="company_id" class="form-select" required>
                            <option value="">Selecione a Empresa...</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Departamento Superior (Hierarquia)</label>
                        <select name="parent_id" class="form-select">
                            <option value="">Nenhum (Direção Geral)</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('parent_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }} ({{ $dept->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Responsável (Gestor / Diretor)</label>
                        <select name="manager_id" class="form-select">
                            <option value="">Sem Responsável Definido</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('manager_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3 mt-5 border-top pt-4">
                <a href="{{ route('config.departments.index') }}" class="btn btn-cancel">Cancelar</a>
                <button type="submit" class="btn btn-save"><i class="fas fa-save me-2"></i> Criar Departamento</button>
            </div>
        </form>
    </div>
</div>
@endsection

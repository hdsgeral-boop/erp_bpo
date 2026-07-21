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
        margin-bottom: 2rem;
    }
    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #f1f5f9;
        display: flex;
        align-items: center;
    }
    .form-label-custom {
        font-weight: 600;
        color: #475569;
        font-size: 0.85rem;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .form-control-custom {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        padding: 0.75rem 1rem;
        transition: all 0.2s;
        background-color: #f8fafc;
    }
    .form-control-custom:focus {
        background-color: #ffffff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .btn-save {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border-radius: 10px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
        transition: all 0.2s;
        border: none;
    }
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4);
    }
    .btn-cancel {
        background: #f1f5f9;
        color: #475569;
        border-radius: 10px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.2s;
        border: none;
    }
    .btn-cancel:hover {
        background: #e2e8f0;
        color: #1e293b;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-user-plus text-primary me-2"></i>Novo Colaborador</h2>
            <p class="text-muted mb-0">Registo de um novo funcionário na empresa.</p>
        </div>
        <a href="{{ route('rh.employees.index') }}" class="btn btn-cancel"><i class="fas fa-arrow-left me-2"></i> Voltar</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger shadow-sm" style="border-radius: 10px; border-left: 4px solid #ef4444;">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('rh.employees.store') }}" method="POST">
        @csrf
        
        <!-- Identificação e Contactos -->
        <div class="card-premium">
            <h4 class="section-title"><i class="fas fa-id-card text-primary me-2"></i>Identificação e Contactos</h4>
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label-custom">Nome Completo <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control form-control-custom" required value="{{ old('name') }}" placeholder="Ex: João Miguel Silva">
                </div>
                <div class="col-md-3">
                    <label class="form-label-custom">NIF</label>
                    <input type="text" name="nif" class="form-control form-control-custom" value="{{ old('nif') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label-custom">Nº INSS</label>
                    <input type="text" name="inss" class="form-control form-control-custom" value="{{ old('inss') }}">
                </div>
                
                <div class="col-md-4">
                    <label class="form-label-custom">Email Pessoal / Profissional</label>
                    <input type="email" name="email" class="form-control form-control-custom" value="{{ old('email') }}" placeholder="email@exemplo.com">
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom">Telefone</label>
                    <input type="text" name="phone" class="form-control form-control-custom" value="{{ old('phone') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom">Morada</label>
                    <input type="text" name="address" class="form-control form-control-custom" value="{{ old('address') }}">
                </div>
            </div>
        </div>

        <!-- Dados Profissionais e Bancários -->
        <div class="card-premium">
            <h4 class="section-title"><i class="fas fa-briefcase text-primary me-2"></i>Dados Profissionais e Salariais</h4>
            <div class="row g-4">
                <div class="col-md-4">
                    <label class="form-label-custom">Departamento</label>
                    <input type="text" name="department" class="form-control form-control-custom" value="{{ old('department') }}" placeholder="Ex: Comercial">
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom">Cargo / Função</label>
                    <input type="text" name="position" class="form-control form-control-custom" value="{{ old('position') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom">Data de Admissão</label>
                    <input type="date" name="admission_date" class="form-control form-control-custom" value="{{ old('admission_date') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label-custom">Salário Base (Kz)</label>
                    <input type="number" step="0.01" name="base_salary" class="form-control form-control-custom" value="{{ old('base_salary', 0) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom">Subsídio de Alimentação (Kz)</label>
                    <input type="number" step="0.01" name="subsidy_meal" class="form-control form-control-custom" value="{{ old('subsidy_meal', 0) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom">Subsídio de Transporte (Kz)</label>
                    <input type="number" step="0.01" name="subsidy_transport" class="form-control form-control-custom" value="{{ old('subsidy_transport', 0) }}">
                </div>
                
                <div class="col-md-4">
                    <label class="form-label-custom">Banco</label>
                    <input type="text" name="bank_name" class="form-control form-control-custom" value="{{ old('bank_name') }}" placeholder="Ex: BAI">
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom">IBAN</label>
                    <input type="text" name="iban" class="form-control form-control-custom" value="{{ old('iban') }}" placeholder="AO06.">
                </div>
                
                <div class="col-md-4">
                    <label class="form-label-custom">Estado <span class="text-danger">*</span></label>
                    <select name="status" class="form-select form-control-custom" required>
                        <option value="Ativo" {{ old('status') == 'Ativo' ? 'selected' : '' }}>Ativo</option>
                        <option value="Inativo" {{ old('status') == 'Inativo' ? 'selected' : '' }}>Inativo</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="text-end mb-5">
            <button type="submit" class="btn btn-save"><i class="fas fa-save me-2"></i> Gravar Colaborador</button>
        </div>
    </form>
</div>
@endsection

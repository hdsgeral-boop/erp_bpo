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
    .section-title {
        font-weight: 700;
        color: #1e293b;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 0.5rem;
        margin-bottom: 1.5rem;
    }
    .attachment-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        background: #f8fafc;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('rh.funcionarios.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="fas fa-arrow-left me-1"></i> Voltar à Listagem
        </a>
        <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-user-edit text-primary me-2"></i>Editar: {{ $employee->name }}</h2>
        <p class="text-muted mb-0">Atualizar dados do colaborador no sistema.</p>
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
    @if(session('error'))
        <div class="alert alert-danger shadow-sm" style="border-radius: 10px;">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('rh.funcionarios.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row g-4">
            <!-- Left Column -->
            <div class="col-md-8">
                <div class="card-premium p-4 p-md-5 mb-4">
                    <h5 class="section-title">Dados Pessoais</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label">Nome Completo <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $employee->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIF</label>
                            <input type="text" name="nif" class="form-control" value="{{ old('nif', $employee->nif) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nº Segurança Social (INSS)</label>
                            <input type="text" name="inss" class="form-control" value="{{ old('inss', $employee->inss) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Principal</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $employee->email) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefone / Telemóvel</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $employee->phone) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Morada Completa</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address', $employee->address) }}</textarea>
                        </div>
                    </div>

                    <h5 class="section-title mt-5">Dados Financeiros e Bancários</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Salário Base Mensal</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" name="base_salary" class="form-control" value="{{ old('base_salary', $employee->base_salary) }}">
                                <span class="input-group-text">AOA</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Subsídio Alimentação</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" name="subsidy_meal" class="form-control" value="{{ old('subsidy_meal', $employee->subsidy_meal) }}">
                                <span class="input-group-text">AOA</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Subsídio Transporte</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" name="subsidy_transport" class="form-control" value="{{ old('subsidy_transport', $employee->subsidy_transport) }}">
                                <span class="input-group-text">AOA</span>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mt-3">
                            <label class="form-label">Nome do Banco</label>
                            <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $employee->bank_name) }}">
                        </div>
                        <div class="col-md-6 mt-3">
                            <label class="form-label">IBAN</label>
                            <input type="text" name="iban" class="form-control font-monospace" value="{{ old('iban', $employee->iban) }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-4">
                <div class="card-premium p-4 mb-4">
                    <h5 class="section-title">Enquadramento Profissional</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">Departamento</label>
                        <select name="department_id" class="form-select">
                            <option value="">Selecione um Departamento...</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Cargo / Função</label>
                        <select name="position_id" class="form-select">
                            <option value="">Selecione um Cargo...</option>
                            @foreach($positions as $pos)
                                <option value="{{ $pos->id }}" {{ old('position_id', $employee->position_id) == $pos->id ? 'selected' : '' }}>{{ $pos->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Data de Admissão</label>
                        <input type="date" name="admission_date" class="form-control" value="{{ old('admission_date', $employee->admission_date ? $employee->admission_date->format('Y-m-d') : '') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dias de Trabalho por Mês</label>
                        <input type="number" min="0" max="31" name="work_days" class="form-control" value="{{ old('work_days', $employee->work_days) }}">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Perfil de Acesso (Login)</label>
                        <select name="role_id" class="form-select">
                            <option value="">Sem acesso ao sistema</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id', $employee->role_id) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="p-3 bg-light rounded border">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="is_active" {{ old('is_active', $employee->is_active) ? 'checked' : '' }} value="1">
                            <label class="form-check-label text-success fw-bold" for="is_active">Colaborador Ativo</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" name="is_retired" id="is_retired" {{ old('is_retired', $employee->is_retired) ? 'checked' : '' }} value="1">
                            <label class="form-check-label fw-bold" for="is_retired">Reformado / Aposentado</label>
                        </div>
                    </div>
                </div>

                <div class="card-premium p-4">
                    <h5 class="section-title">Documentos Anexos</h5>
                    
                    @if($employee->attachments->count() > 0)
                        <div class="mb-4">
                            <label class="form-label d-block text-muted">Ficheiros Guardados</label>
                            @foreach($employee->attachments as $attachment)
                                <div class="attachment-item">
                                    <div class="d-flex align-items-center overflow-hidden">
                                        <i class="fas fa-file-alt text-primary fa-2x me-3"></i>
                                        <div>
                                            <div class="text-truncate fw-bold" style="max-width: 180px;" title="{{ $attachment->file_name }}">
                                                <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="text-decoration-none text-dark">{{ $attachment->file_name }}</a>
                                            </div>
                                            <small class="text-muted">{{ number_format($attachment->file_size / 1024, 2) }} KB</small>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm text-danger" onclick="document.getElementById('form-del-att-{{ $attachment->id }}').submit();" title="Remover Ficheiro">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="mb-0">
                        <label class="form-label">Adicionar Novos Ficheiros</label>
                        <input class="form-control" type="file" name="attachments[]" multiple>
                        <div class="form-text">Máximo de 10MB por ficheiro.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-premium p-4 mt-4 text-end">
            <a href="{{ route('rh.funcionarios.index') }}" class="btn btn-cancel me-2">Cancelar</a>
            <button type="submit" class="btn btn-save"><i class="fas fa-save me-2"></i> Atualizar Colaborador</button>
        </div>
    </form>
    
    <!-- Hidden forms for attachment deletion -->
    @foreach($employee->attachments as $attachment)
    <form id="form-del-att-{{ $attachment->id }}" action="{{ route('rh.funcionarios.attachments.destroy', [$employee->id, $attachment->id]) }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
    @endforeach
</div>
@endsection

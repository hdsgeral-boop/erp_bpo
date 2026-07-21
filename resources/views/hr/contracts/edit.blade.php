@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h2 class="view-title mb-0"><i class="fas fa-edit text-primary"></i> Editar Contrato / Vínculo #{{ $contract->id }}</h2>
            <p class="text-muted mb-0">Atualizar remuneração ou benefício contínuo.</p>
        </div>
        <div>
            <a href="{{ route('rh.contratos.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0" style="max-width: 800px;">
        <div class="card-body p-4">
            <form action="{{ route('rh.contratos.update', $contract->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Colaborador</label>
                        <select name="employee_id" class="form-control" required>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ (old('employee_id') ?? $contract->employee_id) == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Infotipo (Natureza da Remuneração)</label>
                        <select name="infotype_id" class="form-control" required>
                            @foreach($infotypes as $info)
                                <option value="{{ $info->id }}" {{ (old('infotype_id') ?? $contract->infotype_id) == $info->id ? 'selected' : '' }}>{{ $info->name }} ({{ $info->type }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Valor (AKZ)</label>
                        <input type="number" step="0.01" name="value" class="form-control" value="{{ old('value') ?? $contract->value }}" required>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Data de Início</label>
                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date') ?? \Carbon\Carbon::parse($contract->start_date)->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Data de Fim <span class="text-muted fw-normal">(Opcional)</span></label>
                        <input type="date" name="end_date" class="form-control" value="{{ old('end_date') ?? ($contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('Y-m-d') : '') }}">
                        <div class="form-text">Deixe em branco se for um contrato sem termo.</div>
                    </div>
                </div>

                <hr>
                
                <div class="text-end">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

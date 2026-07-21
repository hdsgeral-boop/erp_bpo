@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <h2><i class="fas fa-plus-circle"></i> Criar Conta Contabilística</h2>

    @if($errors->any())
        <div class="alert alert-danger">Verifique os erros no formulário.</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('config.accounts.store') }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Código da Conta (Ex: 11, 21.1)</label>
                        <input type="text" name="code" class="form-control" required>
                    </div>
                    <div class="col-md-8">
                        <label>Descrição</label>
                        <input type="text" name="description" class="form-control" required>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label>Tipo de Conta</label>
                        <select name="type" class="form-select" required>
                            <option value="M">Movimento (Permite lançamentos diretos)</option>
                            <option value="I">Integrador (Conta agregadora / Pai)</option>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" name="is_master_data" class="form-check-input" id="masterData" value="1">
                            <label class="form-check-label" for="masterData">Conta Controlada por Entidades (Clientes/Fornecedores)</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Conta</button>
                <a href="{{ route('config.accounts.index') }}" class="btn btn-light">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <h2><i class="fas fa-edit"></i> Editar Conta: {{ $account->code }}</h2>

    @if($errors->any())
        <div class="alert alert-danger">Verifique os erros no formulário.</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('config.accounts.update', $account->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Código da Conta</label>
                        <input type="text" name="code" class="form-control" value="{{ $account->code }}" required>
                    </div>
                    <div class="col-md-8">
                        <label>Descrição</label>
                        <input type="text" name="description" class="form-control" value="{{ $account->description }}" required>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label>Tipo de Conta</label>
                        <select name="type" class="form-select" required>
                            <option value="M" {{ $account->type == 'M' ? 'selected' : '' }}>Movimento</option>
                            <option value="I" {{ $account->type == 'I' ? 'selected' : '' }}>Integrador</option>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" name="is_master_data" class="form-check-input" id="masterData" value="1" {{ $account->is_master_data ? 'checked' : '' }}>
                            <label class="form-check-label" for="masterData">Conta Controlada por Entidades</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Atualizar Conta</button>
                <a href="{{ route('config.accounts.index') }}" class="btn btn-light">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection

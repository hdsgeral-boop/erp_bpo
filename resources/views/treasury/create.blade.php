@extends('layouts.app')

@section('content')
<div class="header-actions" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <h2 class="view-title">Registar Movimento Bancário</h2>
    <a href="{{ route('tesouraria.bancos.index') }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Voltar</a>
</div>

<div class="card">
    @if($errors->any())
        <div style="background: var(--danger-light); color: var(--danger); padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('tesouraria.bancos.store') }}" method="POST">
        @csrf
        <div class="aux-grid">
            <div class="form-group">
                <label>Data</label>
                <input type="date" name="date" class="form-control" required value="{{ old('date', date('Y-m-d')) }}">
            </div>
            <div class="form-group">
                <label>Conta Bancária</label>
                <select name="account_code" class="form-control" required>
                    <option value="B001 (BAI)" {{ old('account_code') == 'B001 (BAI)' ? 'selected' : '' }}>BAI - 00010000000001</option>
                    <option value="B002 (BFA)" {{ old('account_code') == 'B002 (BFA)' ? 'selected' : '' }}>BFA - 00060000000002</option>
                    <option value="CAIXA_GERAL" {{ old('account_code') == 'CAIXA_GERAL' ? 'selected' : '' }}>Caixa Geral</option>
                </select>
            </div>
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Descrição do Movimento</label>
                <input type="text" name="description" class="form-control" required value="{{ old('description') }}">
            </div>
            <div class="form-group">
                <label>Tipo de Movimento</label>
                <select name="type_dc" class="form-control" required>
                    <option value="D" {{ old('type_dc') == 'D' ? 'selected' : '' }}>Débito (Entrada de Dinheiro)</option>
                    <option value="C" {{ old('type_dc') == 'C' ? 'selected' : '' }}>Crédito (Saída de Dinheiro)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Valor (Kz)</label>
                <input type="number" step="0.01" name="value" class="form-control" required value="{{ old('value') }}">
            </div>
            <div class="form-group">
                <label>Referência (Opcional)</label>
                <input type="text" name="reference" class="form-control" value="{{ old('reference') }}">
            </div>
        </div>
        
        <div style="margin-top: 2rem; text-align: right;">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Movimento</button>
        </div>
    </form>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="header-actions" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <h2 class="view-title">Novo Lançamento Manual (Partida Dobrada)</h2>
    <a href="{{ route('contabilidade.diarios.index') }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Voltar</a>
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

    <form action="{{ route('contabilidade.diarios.store') }}" method="POST">
        @csrf
        <div class="aux-grid" style="margin-bottom: 2rem;">
            <div class="form-group">
                <label>Data do Documento</label>
                <input type="date" name="doc_date" class="form-control" required value="{{ old('doc_date', date('Y-m-d')) }}">
            </div>
            <div class="form-group">
                <label>Data de Entrada (Lançamento)</label>
                <input type="date" name="entry_date" class="form-control" required value="{{ old('entry_date', date('Y-m-d')) }}">
            </div>
            <div class="form-group">
                <label>Número do Documento</label>
                <input type="text" name="doc_number" class="form-control" required value="{{ old('doc_number') }}">
            </div>
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Descrição Base do Lançamento</label>
                <input type="text" name="description" class="form-control" required value="{{ old('description') }}">
            </div>
        </div>

        <h4 style="margin-bottom: 1rem;">Linhas (Deve e Haver)</h4>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <!-- Débito -->
            <div style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <h5 style="margin-top: 0; color: var(--primary-color);">DÉBITO</h5>
                <div class="form-group">
                    <label>Conta Contabilística</label>
                    <input type="text" name="debit_account" class="form-control" placeholder="Ex: 31.1" required>
                </div>
                <div class="form-group">
                    <label>Valor</label>
                    <input type="number" step="0.01" name="amount" class="form-control" required>
                </div>
            </div>

            <!-- Crédito -->
            <div style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <h5 style="margin-top: 0; color: var(--primary-color);">CRÉDITO</h5>
                <div class="form-group">
                    <label>Conta Contabilística</label>
                    <input type="text" name="credit_account" class="form-control" placeholder="Ex: 11.1" required>
                </div>
                <div class="form-group">
                    <label>O valor é automaticamente o mesmo (Princípio das Partidas Dobradas)</label>
                    <input type="text" class="form-control" disabled value="-- Igual ao Débito --">
                </div>
            </div>
        </div>
        
        <div style="margin-top: 2rem; text-align: right;">
            <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Processar Lançamento</button>
        </div>
    </form>
</div>
@endsection

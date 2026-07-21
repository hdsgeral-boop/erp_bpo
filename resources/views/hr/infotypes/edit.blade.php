@extends('layouts.app')

@section('content')
<div class="header-actions" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <h2 class="view-title">Editar Infotipo</h2>
    <a href="{{ route('rh.infotypes.index') }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Voltar</a>
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

    <form action="{{ route('rh.infotypes.update', $infotype) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="aux-grid">
            <div class="form-group">
                <label>Nome / Descrição</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name', $infotype->name) }}">
            </div>
            <div class="form-group">
                <label>Tipo (Vencimento ou Desconto)</label>
                <select name="type" class="form-control" required>
                    <option value="VENCIMENTO" {{ old('type', $infotype->type) == 'VENCIMENTO' ? 'selected' : '' }}>Vencimento (Abono)</option>
                    <option value="DESCONTO" {{ old('type', $infotype->type) == 'DESCONTO' ? 'selected' : '' }}>Desconto (Dedução)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Sujeito a INSS?</label>
                <select name="is_inss_base" class="form-control" required>
                    <option value="1" {{ old('is_inss_base', $infotype->is_inss_base) == '1' ? 'selected' : '' }}>Sim</option>
                    <option value="0" {{ old('is_inss_base', $infotype->is_inss_base) == '0' ? 'selected' : '' }}>Não</option>
                </select>
            </div>
            <div class="form-group">
                <label>Tributação IRT</label>
                <select name="irt_type" class="form-control" required>
                    <option value="FULL" {{ old('irt_type', $infotype->irt_type) == 'FULL' ? 'selected' : '' }}>Integralmente Tributável</option>
                    <option value="EXEMPT" {{ old('irt_type', $infotype->irt_type) == 'EXEMPT' ? 'selected' : '' }}>Isento</option>
                    <option value="CONDITIONAL_30K" {{ old('irt_type', $infotype->irt_type) == 'CONDITIONAL_30K' ? 'selected' : '' }}>Tributável acima de 30.000 Kz</option>
                </select>
            </div>
        </div>
        
        <div style="margin-top: 2rem; display: flex; justify-content: space-between;">
            <button type="button" onclick="if(confirm('Tem certeza?')) document.getElementById('delete-form').submit();" class="btn btn-danger"><i class="fas fa-trash"></i> Eliminar</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Atualizar Infotipo</button>
        </div>
    </form>

    <form id="delete-form" action="{{ route('rh.infotypes.destroy', $infotype) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection

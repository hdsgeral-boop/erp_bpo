@extends('layouts.app')

@section('content')
<div class="header-actions" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <h2 class="view-title">Nova Categoria</h2>
    <a href="{{ route('logistica.categories.index') }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Voltar</a>
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

    <form action="{{ route('logistica.categories.store') }}" method="POST">
        @csrf
        <div class="aux-grid">
            <div class="form-group">
                <label>Código da Categoria</label>
                <input type="text" name="code" class="form-control" required value="{{ old('code') }}">
            </div>
            <div class="form-group">
                <label>Nome da Categoria</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
            </div>
        </div>
        
        <div style="margin-top: 2rem; text-align: right;">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Categoria</button>
        </div>
    </form>
</div>
@endsection

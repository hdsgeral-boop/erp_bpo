@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <h2><i class="fas fa-plus-circle"></i> Nova Sessão de Inventário</h2>
    <p class="text-muted">Abertura de contagem cega para um armazém específico.</p>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('logistica.inventario.store') }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Armazém a Contar</label>
                        <select name="warehouse_id" class="form-select" required>
                            @foreach($warehouses as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Data de Contagem</label>
                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Nome do Responsável</label>
                        <input type="text" name="responsible_name" class="form-control" placeholder="Quem vai contar?" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-play"></i> Gerar Folha de Contagem</button>
                <a href="{{ route('logistica.inventario.index') }}" class="btn btn-light">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection

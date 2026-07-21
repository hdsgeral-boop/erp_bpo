@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-clipboard-list"></i> Sessões de Inventário</h2>
        <a href="{{ route('logistica.inventario.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Iniciar Contagem</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#ID</th>
                            <th>Data</th>
                            <th>Armazém</th>
                            <th>Responsável</th>
                            <th>Estado</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessions as $sess)
                        <tr>
                            <td>{{ $sess->id }}</td>
                            <td>{{ \Carbon\Carbon::parse($sess->date)->format('d/m/Y') }}</td>
                            <td>{{ $sess->warehouse->name ?? 'N/A' }}</td>
                            <td>{{ $sess->responsible_name }}</td>
                            <td>
                                @if($sess->status == 'OPEN')
                                    <span class="badge bg-primary">Em Contagem</span>
                                @elseif($sess->status == 'REVIEW')
                                    <span class="badge bg-warning text-dark">Revisão</span>
                                @else
                                    <span class="badge bg-success">Fechado</span>
                                @endif
                            </td>
                            <td>
                                @if($sess->status == 'OPEN')
                                    <a href="{{ route('logistica.inventario.show', $sess->id) }}" class="btn btn-sm btn-primary">Continuar Contagem</a>
                                @elseif($sess->status == 'REVIEW')
                                    <a href="{{ route('logistica.inventario.review', $sess->id) }}" class="btn btn-sm btn-warning">Revisar e Fechar</a>
                                @else
                                    <a href="{{ route('logistica.inventario.show', $sess->id) }}" class="btn btn-sm btn-outline-secondary">Ver Relatório</a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

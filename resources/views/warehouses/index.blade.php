@extends('layouts.app')

@section('content')
<div class="header-actions" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <h2 class="view-title">Armazéns</h2>
    <a href="{{ route('logistica.warehouses.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Novo Armazém</a>
</div>

<div class="card">
    <table class="table" style="width: 100%">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nome</th>
                <th>Localização</th>
                <th>Estado</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($warehouses as $warehouse)
            <tr>
                <td>{{ $warehouse->code }}</td>
                <td>{{ $warehouse->name }}</td>
                <td>{{ $warehouse->location ?? '-' }}</td>
                <td>
                    <span class="badge {{ $warehouse->status === 'Ativo' ? 'badge-success' : 'badge-secondary' }}">{{ $warehouse->status }}</span>
                </td>
                <td>
                    <a href="{{ route('logistica.warehouses.edit', $warehouse) }}" class="btn btn-outline" style="padding: 4px 8px;"><i class="fas fa-edit"></i></a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 20px;">Nenhum armazém registado.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

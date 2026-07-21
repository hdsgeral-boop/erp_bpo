@extends('layouts.app')

@section('content')
<div class="header-actions" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <h2 class="view-title">Categorias de Produtos</h2>
    <a href="{{ route('logistica.categories.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nova Categoria</a>
</div>

<div class="card">
    <table class="table" style="width: 100%">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nome</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
            <tr>
                <td>{{ $category->code }}</td>
                <td>{{ $category->name }}</td>
                <td>
                    <a href="{{ route('logistica.categories.edit', $category) }}" class="btn btn-outline" style="padding: 4px 8px;"><i class="fas fa-edit"></i></a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align: center; padding: 20px;">Nenhuma categoria registada.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

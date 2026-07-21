@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-boxes"></i> Níveis de Stock</h2>
        <a href="{{ route('logistica.guias.create') }}" class="btn btn-primary"><i class="fas fa-truck-loading"></i> Emitir Guia de Saída</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Artigo</th>
                            <th>Stock Global</th>
                            <th>P.V.P</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $prod)
                        <tr>
                            <td>{{ $prod->code }}</td>
                            <td>{{ $prod->name }}</td>
                            <td>
                                @if($prod->stock_qty > 10)
                                    <span class="badge bg-success">{{ $prod->stock_qty }} {{ $prod->unit ?? 'UN' }}</span>
                                @elseif($prod->stock_qty > 0)
                                    <span class="badge bg-warning">{{ $prod->stock_qty }} {{ $prod->unit ?? 'UN' }}</span>
                                @else
                                    <span class="badge bg-danger">Ruptura</span>
                                @endif
                            </td>
                            <td>{{ number_format($prod->unit_price, 2, ',', '.') }} Kz</td>
                            <td>
                                <button class="btn btn-sm btn-outline-info" title="Ver Movimentos"><i class="fas fa-history"></i></button>
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

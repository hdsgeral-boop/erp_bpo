@extends('layouts.app')

@section('content')
<div class="header-actions" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <h2 class="view-title">Movimentos de Stock</h2>
    <a href="{{ route('logistica.movements.create') }}" class="btn btn-primary"><i class="fas fa-exchange-alt"></i> Registar Movimento</a>
</div>

<div class="card">
    <table class="table" style="width: 100%">
        <thead>
            <tr>
                <th>Data</th>
                <th>Produto</th>
                <th>Armazém</th>
                <th>Tipo</th>
                <th>Quantidade</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movements as $movement)
            <tr>
                <td>{{ \Carbon\Carbon::parse($movement->date)->format('d/m/Y') }}</td>
                <td>{{ $movement->product->name ?? '-' }}</td>
                <td>{{ $movement->warehouse->name ?? '-' }}</td>
                <td>
                    @if($movement->type === 'IN')
                        <span class="badge badge-success"><i class="fas fa-arrow-down"></i> Entrada</span>
                    @else
                        <span class="badge badge-danger"><i class="fas fa-arrow-up"></i> Saída</span>
                    @endif
                </td>
                <td style="font-weight: 600;">{{ number_format($movement->quantity, 2, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 20px;">Nenhum movimento registado.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

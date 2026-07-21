@extends('layouts.app')

@section('content')
<div class="header-actions" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <h2 class="view-title">Contas Bancárias / Tesouraria</h2>
    <a href="{{ route('tesouraria.bancos.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Novo Movimento Bancário</a>
</div>

<div class="card">
    <table class="table" style="width: 100%">
        <thead>
            <tr>
                <th>Data</th>
                <th>Conta Bancária</th>
                <th>Descrição</th>
                <th>Referência</th>
                <th>Débito (Entrada)</th>
                <th>Crédito (Saída)</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lines as $line)
            <tr>
                <td>{{ \Carbon\Carbon::parse($line->date)->format('d/m/Y') }}</td>
                <td>{{ $line->account_code }}</td>
                <td>{{ $line->description }}</td>
                <td>{{ $line->reference ?? '-' }}</td>
                @if($line->type_dc === 'D')
                    <td style="color: var(--success); font-weight: bold;">{{ number_format($line->value, 2, ',', '.') }}</td>
                    <td>-</td>
                @else
                    <td>-</td>
                    <td style="color: var(--danger); font-weight: bold;">{{ number_format($line->value, 2, ',', '.') }}</td>
                @endif
                <td>
                    <span class="badge {{ $line->status === 'RECONCILED' ? 'badge-success' : 'badge-secondary' }}">{{ $line->status }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px;">Nenhum movimento bancário registado.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

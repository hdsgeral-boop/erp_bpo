@extends('layouts.app')

@section('content')
<div class="header-actions" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <h2 class="view-title">Lançamentos Contabilísticos / Diários</h2>
    <a href="{{ route('contabilidade.diarios.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Novo Lançamento Manual</a>
</div>

<div class="card">
    <table class="table" style="width: 100%">
        <thead>
            <tr>
                <th>Data Entry</th>
                <th>Diário</th>
                <th>Conta</th>
                <th>Descrição</th>
                <th>Doc Nº</th>
                <th>Débito</th>
                <th>Crédito</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lines as $line)
            <tr>
                <td>{{ \Carbon\Carbon::parse($line->entry_date)->format('d/m/Y') }}</td>
                <td>{{ $line->journal->code ?? 'GERAL' }}</td>
                <td style="font-weight: 600;">{{ $line->account_code }}</td>
                <td>{{ $line->description }}</td>
                <td>{{ $line->doc_number }}</td>
                @if($line->type_dc === 'D')
                    <td style="color: var(--primary-color); font-weight: bold;">{{ number_format($line->value, 2, ',', '.') }}</td>
                    <td>-</td>
                @else
                    <td>-</td>
                    <td style="color: var(--primary-color); font-weight: bold;">{{ number_format($line->value, 2, ',', '.') }}</td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px;">Nenhum lançamento registado.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

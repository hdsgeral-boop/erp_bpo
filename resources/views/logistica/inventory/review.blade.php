@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <h2><i class="fas fa-balance-scale"></i> Revisão de Inventário (#{{ $session->id }})</h2>
    <p class="text-muted">Analise as diferenças antes de efetivar o acerto automático.</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Artigo</th>
                            <th>Stock Sistema</th>
                            <th>Qtd Contada</th>
                            <th>Diferença (Sobras/Quebras)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($session->lines as $line)
                            @if($line->counted_qty !== null)
                                <tr>
                                    <td>{{ $line->product->code }} - {{ $line->product->name }}</td>
                                    <td>{{ $line->system_qty }}</td>
                                    <td><strong>{{ $line->counted_qty }}</strong></td>
                                    <td>
                                        @if($line->difference > 0)
                                            <span class="text-success"><i class="fas fa-arrow-up"></i> +{{ $line->difference }} (Sobra)</span>
                                        @elseif($line->difference < 0)
                                            <span class="text-danger"><i class="fas fa-arrow-down"></i> {{ $line->difference }} (Quebra)</span>
                                        @else
                                            <span class="text-muted"><i class="fas fa-equals"></i> Certo</span>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($session->status == 'REVIEW')
    <form action="{{ route('logistica.inventario.close', $session->id) }}" method="POST" onsubmit="return confirm('ATENÇÃO: Isto irá alterar o stock permanente no armazém. Tem certeza?')">
        @csrf
        <button type="submit" class="btn btn-danger btn-lg"><i class="fas fa-check-double"></i> Efetivar Ajuste de Stock e Fechar</button>
        <a href="{{ route('logistica.inventario.show', $session->id) }}" class="btn btn-outline-secondary btn-lg">Voltar à Contagem</a>
    </form>
    @endif
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('logistica.inventario.index') }}" class="btn btn-outline-secondary mb-3"><i class="fas fa-arrow-left"></i> Voltar</a>
        <h2><i class="fas fa-balance-scale"></i> Revisão e Regularização de Inventário</h2>
        <p class="text-muted">Sessão #{{ $session->id }} - {{ $session->warehouse->name ?? 'N/D' }}</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Diferenças Apuradas (Quebras e Sobras)</h5>
            @if($session->status == 'REVIEW')
            <form action="{{ route('logistica.inventario.close', $session->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary" onclick="return confirm('ATENÇÃO: Fechar o inventário irá regularizar todos os stocks e gerar movimentos contabilísticos. Deseja avançar?')">
                    <i class="fas fa-lock"></i> Aprovar e Regularizar Stocks
                </button>
            </form>
            @elseif($session->status == 'CLOSED')
                <span class="badge bg-success fs-6"><i class="fas fa-check-circle"></i> SESSÃO FECHADA E REGULARIZADA</span>
            @endif
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Produto</th>
                            <th class="text-center">Stock Sistema</th>
                            <th class="text-center">Stock Contado</th>
                            <th class="text-center">Diferença (Qtd)</th>
                            <th class="text-end">Custo Total Diferença</th>
                            <th>Classificação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalQuebras = 0;
                            $totalSobras = 0;
                        @endphp
                        @foreach($session->lines as $line)
                            @if($line->difference != 0 && $line->counted_qty !== null)
                                @php
                                    $custo = abs($line->difference) * ($line->product->unit_price ?? 0);
                                    if($line->difference < 0) $totalQuebras += $custo;
                                    else $totalSobras += $custo;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $line->product->name }}</strong><br>
                                        <small class="text-muted">{{ $line->product->code }}</small>
                                    </td>
                                    <td class="text-center text-muted">{{ $line->system_qty }}</td>
                                    <td class="text-center fw-bold">{{ $line->counted_qty }}</td>
                                    <td class="text-center">
                                        @if($line->difference > 0)
                                            <span class="text-success fw-bold">+{{ $line->difference }}</span>
                                        @else
                                            <span class="text-danger fw-bold">{{ $line->difference }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end font-monospace">
                                        {{ number_format($custo, 2) }} €
                                    </td>
                                    <td>
                                        @if($line->difference > 0)
                                            <span class="badge bg-success">SOBRA</span>
                                        @else
                                            <span class="badge bg-danger">QUEBRA</span>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Total Sobras:</td>
                            <td class="text-end fw-bold text-success font-monospace">+{{ number_format($totalSobras, 2) }} €</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Total Quebras:</td>
                            <td class="text-end fw-bold text-danger font-monospace">-{{ number_format($totalQuebras, 2) }} €</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @if($session->status == 'REVIEW')
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i> A regularização de stock gerará movimentos do tipo <strong>QUEBRA_INVENTARIO</strong> e <strong>SOBRA_INVENTARIO</strong> para as diferenças apuradas. Os produtos sem diferença não sofrerão alterações de stock.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

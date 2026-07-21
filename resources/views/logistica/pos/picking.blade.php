@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-clipboard-list"></i> Fila de Picking</h2>
            <p class="text-muted">Lista de pedidos de clientes aguardando recolha física nas prateleiras.</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('logistica.pos.balcao') }}" class="btn btn-outline-primary"><i class="fas fa-desktop"></i> Atendimento ao Balcão</a>
            <a href="{{ route('logistica.pos.picking') }}" class="btn btn-primary"><i class="fas fa-clipboard-list"></i> Fila de Picking</a>
        </div>
    </div>

    <div class="card shadow-sm border-top border-4 border-warning">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Data Pedido</th>
                            <th>Nº Encomenda</th>
                            <th>Cliente</th>
                            <th>Estado</th>
                            <th class="text-end">Operação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingSales as $sale)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</td>
                            <td><strong>{{ $sale->doc_number }}</strong></td>
                            <td>{{ $customers->get($sale->customer_id)->name ?? 'Cliente Geral' }}</td>
                            <td>
                                <span class="badge bg-warning text-dark">{{ strtoupper($sale->status) }}</span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-primary btn-sm" onclick="alert('Funcionalidade de transformar Encomenda em Picking via Modal a implementar no detalhe.')">
                                    <i class="fas fa-dolly"></i> Iniciar Picking
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-box-open fa-3x mb-3"></i>
                                <p>Nenhuma encomenda na fila de picking hoje.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

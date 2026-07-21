@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-boxes"></i> Histórico e Validação de Entradas</h2>
            <p class="text-muted">Gestão de receções de fornecedores e validação física de stock.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Data Guia</th>
                            <th>Nº Guia Fornecedor</th>
                            <th>Encomenda Ref.</th>
                            <th>Armazém Destino</th>
                            <th>Status</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deliveries as $del)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($del->date)->format('d/m/Y') }}</td>
                            <td><strong>{{ $del->delivery_number }}</strong></td>
                            <td>
                                @php
                                    $order = \App\Models\PurchaseOrder::find($del->order_id);
                                @endphp
                                {{ $order ? '#' . $order->order_number : 'N/D' }}
                            </td>
                            <td>
                                @if($del->is_validated)
                                    <span class="badge bg-light text-dark border">
                                        {{ \App\Models\Warehouse::find($del->warehouse_id)->name ?? 'N/D' }}
                                    </span>
                                @else
                                    <span class="text-muted">Pendente</span>
                                @endif
                            </td>
                            <td>
                                @if($del->is_validated)
                                    <span class="badge bg-success">VALIDADO</span>
                                @else
                                    <span class="badge bg-warning text-dark">PENDENTE</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if(!$del->is_validated)
                                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#validarModal{{ $del->id }}" title="Validar Entrada">
                                    <i class="fas fa-check"></i> Validar
                                </button>
                                @endif
                            </td>
                        </tr>

                        <!-- Modal Validar Entrada -->
                        <div class="modal fade" id="validarModal{{ $del->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="{{ route('logistica.rececoes.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="delivery_id" value="{{ $del->id }}">
                                    <div class="modal-content">
                                        <div class="modal-header bg-success text-white">
                                            <h5 class="modal-title">Validar Entrada: {{ $del->delivery_number }}</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="text-muted mb-4">Confirme o armazém de destino para a mercadoria física recebida.</p>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Armazém de Destino</label>
                                                <select name="warehouse_id" class="form-select" required>
                                                    <option value="">-- Selecione o Armazém --</option>
                                                    @foreach($warehouses as $wh)
                                                        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Confirmar Entrada em Stock</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Nenhuma receção registada.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $deliveries->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

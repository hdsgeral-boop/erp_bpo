@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    .form-control, .form-select {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border: 1px solid #cbd5e1;
    }
    .form-control:focus, .form-select:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }
    .btn-save {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border-radius: 10px;
        padding: 0.6rem 2rem;
        font-weight: 600;
        border: none;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('compras.encomendas.show', $order->id) }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="fas fa-arrow-left me-1"></i> Voltar à Encomenda
        </a>
        <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-truck-loading text-success me-2"></i>Rececionar Mercadoria</h2>
        <p class="text-muted mt-1">Registe a quantidade de artigos que efetivamente chegou ao armazém para a encomenda <strong>{{ $order->order_number }}</strong>.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm" style="border-radius: 10px;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('compras.rececoes.store') }}" method="POST">
        @csrf
        <input type="hidden" name="order_id" value="{{ $order->id }}">
        
        <div class="card-premium p-4 mb-4">
            <h5 class="fw-bold border-bottom pb-2 mb-4">Informação de Receção</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Armazém de Destino (Onde a mercadoria vai entrar fisicamente) <span class="text-danger">*</span></label>
                    <select name="warehouse_id" class="form-select" required>
                        <option value="">Selecione o armazém...</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nº Guia Remessa do Fornecedor (Opcional)</label>
                    <input type="text" name="delivery_note_number" class="form-control" value="{{ old('delivery_note_number') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Notas do Operador / Registo de Danos</label>
                    <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="card-premium p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-4">
                <h5 class="fw-bold m-0 text-dark">Artigos a Receber</h5>
                <span class="badge bg-light text-dark border">Indique a quantidade recebida</span>
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 45%;">Artigo</th>
                            <th style="width: 15%; text-align: center;">Qtd. Encomendada</th>
                            <th style="width: 15%; text-align: center;">Já Recebida (Anterior)</th>
                            <th style="width: 25%; text-align: center;" class="bg-success text-white">Qtd. a Entrar AGORA no Stock <span class="text-danger">*</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $index => $item)
                        @php
                            $remaining = $item->quantity - $item->received_qty;
                        @endphp
                        <tr class="{{ $remaining <= 0 ? 'table-secondary opacity-50' : '' }}">
                            <td>
                                <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                                <div class="fw-bold">{{ $item->product->name }}</div>
                                <small class="text-muted">[{{ $item->product->code }}]</small>
                            </td>
                            <td class="text-center fw-bold">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                            <td class="text-center text-muted">{{ number_format($item->received_qty, 2, ',', '.') }}</td>
                            <td>
                                @if($remaining > 0)
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" max="{{ $remaining }}" name="items[{{ $index }}][quantity]" class="form-control border-success text-center fw-bold" value="{{ old('items.'.$index.'.quantity', $remaining) }}" required>
                                        <span class="input-group-text bg-light text-muted">/ {{ $remaining }}</span>
                                    </div>
                                @else
                                    <div class="text-center text-success"><i class="fas fa-check-circle"></i> Linha Concluída</div>
                                    <input type="hidden" name="items[{{ $index }}][quantity]" value="0">
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="alert alert-info shadow-sm d-flex align-items-center" style="border-radius: 10px;">
            <i class="fas fa-info-circle fa-2x me-3 text-info"></i>
            <div>
                <strong>Auditoria Automática de Stock:</strong> Ao gravar, os artigos selecionados darão entrada imediata no armazém escolhido. Será criado um Movimento de Stock do tipo "Entrada".
            </div>
        </div>

        <div class="text-end mt-4">
            <a href="{{ route('compras.encomendas.show', $order->id) }}" class="btn btn-light border fw-bold me-2" style="border-radius:10px; padding:0.6rem 2rem;">Cancelar</a>
            <button type="submit" class="btn btn-save"><i class="fas fa-box-open me-2"></i> Rececionar e Injetar Stock</button>
        </div>
    </form>
</div>
@endsection

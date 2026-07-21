@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <h2><i class="fas fa-file-invoice"></i> Emitir Guia de Saída</h2>
    <p class="text-muted">A emissão da guia irá descontar o stock fisicamente do armazém.</p>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('logistica.guias.store') }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Cliente Destino</label>
                        <select name="customer_id" class="form-select" required>
                            <option value="">Selecione...</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} (NIF: {{ $c->nif }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Armazém de Origem</label>
                        <select name="warehouse_id" class="form-select" required>
                            @foreach($warehouses as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Data de Carga</label>
                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Nome do Motorista</label>
                        <input type="text" name="driver_name" class="form-control" placeholder="Opcional">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Matrícula (Viatura)</label>
                        <input type="text" name="vehicle_plate" class="form-control" placeholder="Ex: LD-00-00">
                    </div>
                </div>

                <h5 class="mb-3 border-bottom pb-2">Artigos a Transportar</h5>
                <div id="items-container">
                    <div class="row mb-2 item-row">
                        <div class="col-md-8">
                            <select name="items[0][product_id]" class="form-select" required>
                                <option value="">Escolha um Produto...</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }} (Stock: {{ $p->stock_qty }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="number" step="0.01" name="items[0][quantity]" class="form-control" placeholder="Qtd" required>
                        </div>
                        <div class="col-md-1">
                            <!-- spacer -->
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addItem()"><i class="fas fa-plus"></i> Adicionar Linha</button>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Emitir Guia (Final)</button>
                <a href="{{ route('logistica.guias.index') }}" class="btn btn-light">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<script>
    let itemIndex = 1;
    function addItem() {
        const container = document.getElementById('items-container');
        const row = document.createElement('div');
        row.className = 'row mb-2 item-row';
        row.innerHTML = `
            <div class="col-md-8">
                <select name="items[${itemIndex}][product_id]" class="form-select" required>
                    <option value="">Escolha um Produto...</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }} (Stock: {{ $p->stock_qty }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="number" step="0.01" name="items[${itemIndex}][quantity]" class="form-control" placeholder="Qtd" required>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger" onclick="this.closest('.item-row').remove()"><i class="fas fa-trash"></i></button>
            </div>
        `;
        container.appendChild(row);
        itemIndex++;
    }
</script>
@endsection

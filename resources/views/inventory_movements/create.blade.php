@extends('layouts.app')

@section('content')
<div class="header-actions" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <h2 class="view-title">Registar Movimento</h2>
    <a href="{{ route('logistica.movements.index') }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Voltar</a>
</div>

<div class="card">
    @if($errors->any())
        <div style="background: var(--danger-light); color: var(--danger); padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('logistica.movements.store') }}" method="POST">
        @csrf
        <div class="aux-grid">
            <div class="form-group">
                <label>Data do Movimento</label>
                <input type="date" name="date" class="form-control" required value="{{ old('date', date('Y-m-d')) }}">
            </div>
            <div class="form-group">
                <label>Tipo de Movimento</label>
                <select name="type" class="form-control" required>
                    <option value="IN" {{ old('type') == 'IN' ? 'selected' : '' }}>Entrada (IN)</option>
                    <option value="OUT" {{ old('type') == 'OUT' ? 'selected' : '' }}>Saída (OUT)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Armazém</label>
                <select name="warehouse_id" class="form-control" required>
                    <option value="">Selecione o Armazém</option>
                    @foreach(\App\Models\Warehouse::where('status', 'Ativo')->get() as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Produto</label>
                <select name="product_id" class="form-control" required>
                    <option value="">Selecione o Produto Físico</option>
                    @foreach(\App\Models\Product::where('is_stockable', 1)->where('status', 'Ativo')->get() as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->code }} - {{ $product->name }} (Atual: {{ $product->stock }} {{ $product->unit }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Quantidade</label>
                <input type="number" step="0.01" name="quantity" class="form-control" required value="{{ old('quantity') }}">
            </div>
        </div>
        
        <div style="margin-top: 2rem; text-align: right;">
            <button type="submit" class="btn btn-primary"><i class="fas fa-exchange-alt"></i> Processar Movimento</button>
        </div>
    </form>
</div>
@endsection

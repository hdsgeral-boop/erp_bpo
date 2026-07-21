@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <h2><i class="fas fa-list-ol"></i> Inserção de Contagem (#{{ $session->id }})</h2>
    <p class="text-muted">Insira as quantidades reais contadas fisicamente no <strong>{{ $session->warehouse->name }}</strong>.</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            @if($session->status == 'CLOSED')
                <div class="alert alert-info">Esta sessão já está fechada. Apenas para leitura.</div>
            @else
                <form action="{{ route('logistica.inventario.update', $session->id) }}" method="POST">
                    @csrf
                    @method('PUT')
            @endif
            
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Código</th>
                            <th>Artigo</th>
                            @if($session->status != 'OPEN')
                            <th>Stock Sistema (Antes)</th>
                            @endif
                            <th>Qtd Física Contada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($session->lines as $line)
                        <tr>
                            <td>{{ $line->product->code }}</td>
                            <td>{{ $line->product->name }}</td>
                            @if($session->status != 'OPEN')
                            <td>{{ $line->system_qty }}</td>
                            @endif
                            <td>
                                @if($session->status == 'CLOSED')
                                    {{ $line->counted_qty ?? 'Não contado' }}
                                @else
                                    <input type="number" step="0.01" name="lines[{{ $line->id }}][counted_qty]" class="form-control" value="{{ $line->counted_qty }}">
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($session->status != 'CLOSED')
                <div class="mt-3">
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Guardar e Passar à Revisão</button>
                    <a href="{{ route('logistica.inventario.index') }}" class="btn btn-light">Voltar</a>
                </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('logistica.inventario.index') }}" class="btn btn-outline-secondary mb-3"><i class="fas fa-arrow-left"></i> Voltar</a>
        <h2><i class="fas fa-list-ol"></i> Efetuar Contagem Física</h2>
        <p class="text-muted">Sessão #{{ $session->id }} - {{ $session->warehouse->name ?? 'N/D' }} ({{ \Carbon\Carbon::parse($session->date)->format('d/m/Y') }})</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-top border-4 border-primary">
                <div class="card-body">
                    <h5 class="card-title fw-bold"><i class="fas fa-barcode"></i> Leitura Rápida</h5>
                    <p class="small text-muted mb-3">Utilize o scanner de código de barras para adicionar à contagem.</p>
                    
                    <div class="input-group mb-3">
                        <input type="text" id="barcodeScanner" class="form-control form-control-lg" placeholder="Código do Produto..." autofocus>
                        <button class="btn btn-primary" onclick="scanBarcode()"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('logistica.inventario.saveContagem', $session->id) }}" method="POST" id="contagemForm">
                        @csrf
                        <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                            <table class="table table-hover align-middle" id="contagemTable">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>Código</th>
                                        <th>Produto</th>
                                        <th style="width: 150px;">Quantidade Física</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($session->lines as $line)
                                    @php $prod = $line->product; @endphp
                                    <tr data-barcode="{{ strtolower($prod->code) }}">
                                        <td class="font-monospace text-muted">{{ $prod->code }}</td>
                                        <td><strong>{{ $prod->name }}</strong></td>
                                        <td>
                                            <input type="number" name="lines[{{ $line->id }}][counted_qty]" class="form-control form-control-lg text-center fw-bold qty-input" value="{{ $line->counted_qty }}" min="0" step="0.01">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <span class="text-muted"><i class="fas fa-info-circle"></i> Os valores em branco não alterarão o stock atual.</span>
                            <button type="button" class="btn btn-success btn-lg px-5" onclick="if(confirm('Tem certeza que deseja submeter a contagem para revisão?')) document.getElementById('contagemForm').submit();">
                                <i class="fas fa-check"></i> Finalizar Contagem
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('barcodeScanner').addEventListener('keypress', function(e) {
        if(e.key === 'Enter') {
            e.preventDefault();
            scanBarcode();
        }
    });

    function scanBarcode() {
        const barcode = document.getElementById('barcodeScanner').value.toLowerCase().trim();
        if(!barcode) return;

        let found = false;
        document.querySelectorAll('#contagemTable tbody tr').forEach(row => {
            if(row.dataset.barcode === barcode) {
                found = true;
                const input = row.querySelector('.qty-input');
                let currentVal = parseFloat(input.value) || 0;
                input.value = currentVal + 1;
                
                // Highlight row briefly
                row.style.backgroundColor = '#e8f5e9';
                setTimeout(() => row.style.backgroundColor = '', 1000);
            }
        });

        if(!found) {
            alert('Produto não encontrado nesta sessão.');
        }

        document.getElementById('barcodeScanner').value = '';
        document.getElementById('barcodeScanner').focus();
    }
</script>
@endsection

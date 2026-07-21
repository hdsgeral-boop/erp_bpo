<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Documento {{ $sale->doc_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; line-height: 1.6; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .company-info h1 { margin: 0; font-size: 24px; color: #1e40af; }
        .doc-info { text-align: right; }
        .doc-info h2 { margin: 0; color: #475569; }
        .customer-card { background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background: #f1f5f9; padding: 12px; text-align: left; border-bottom: 2px solid #cbd5e1; }
        td { padding: 12px; border-bottom: 1px solid #e2e8f0; }
        .totals { float: right; width: 300px; }
        .totals-row { display: flex; justify-content: space-between; padding: 8px 0; }
        .totals-row.grand-total { font-weight: bold; font-size: 1.2em; border-top: 2px solid #333; margin-top: 10px; padding-top: 10px; }
        .footer { clear: both; text-align: center; margin-top: 50px; font-size: 0.85em; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 20px; }
        .watermark { position: absolute; top: 40%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 100px; color: rgba(255,0,0,0.1); z-index: -1; }
    </style>
</head>
<body onload="window.print()">
    @if($sale->status === 'CANCELADO')
        <div class="watermark">ANULADO</div>
    @endif

    <div class="header">
        <div class="company-info">
            <h1>Consulvolt ERP</h1>
            <p>Luanda, Angola<br>NIF: 5000000000<br>geral@consulvolt.ao</p>
        </div>
        <div class="doc-info">
            <h2>{{ $sale->doc_number }}</h2>
            <p>Data: {{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}<br>Estado: <strong>{{ $sale->status }}</strong></p>
        </div>
    </div>

    <div class="customer-card">
        <strong>Cliente:</strong> {{ $sale->customer->name ?? 'Consumidor Final' }}<br>
        <strong>NIF:</strong> {{ $sale->customer->nif ?? '999999999' }}<br>
        <strong>Morada:</strong> {{ $sale->customer->address ?? 'Desconhecida' }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Descrição</th>
                <th style="text-align: right;">Qtd</th>
                <th style="text-align: right;">Preço Unit. (Kz)</th>
                <th style="text-align: right;">Subtotal (Kz)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
            <tr>
                <td>{{ $item->product->code ?? '-' }}</td>
                <td>{{ $item->product->name ?? 'Artigo Removido' }}</td>
                <td style="text-align: right;">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                <td style="text-align: right;">{{ number_format($item->unit_price, 2, ',', '.') }}</td>
                <td style="text-align: right;">{{ number_format($item->subtotal, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="totals-row">
            <span>Subtotal:</span>
            <span>{{ number_format($sale->total_amount, 2, ',', '.') }} Kz</span>
        </div>
        <div class="totals-row">
            <span>Imposto:</span>
            <span>0,00 Kz</span>
        </div>
        <div class="totals-row grand-total">
            <span>Total a Pagar:</span>
            <span>{{ number_format($sale->total_amount, 2, ',', '.') }} Kz</span>
        </div>
    </div>

    <div class="footer">
        <p>Documento processado por computador - Consulvolt ERP v2.0</p>
    </div>
</body>
</html>

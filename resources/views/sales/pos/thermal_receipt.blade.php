<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Talão POS — {{ $sale->doc_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 78mm;
            margin: 0 auto;
            padding: 5px;
            font-size: 11px;
            color: #000;
            background: #fff;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .line { border-bottom: 1px dashed #000; margin: 6px 0; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        td, th { padding: 2px 0; }
        .agt-footer { margin-top: 10px; font-size: 9px; text-align: center; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 10px; text-align: center;">
        <button onclick="window.print()" style="padding: 6px 12px; background: #000; color: #fff; border: none; border-radius: 4px; font-family: sans-serif; font-weight: bold; cursor: pointer;">
            🖨️ Imprimir Talão (80mm)
        </button>
    </div>

    <!-- Cabeçalho POS -->
    <div class="text-center bold" style="font-size: 14px;">{{ $company->name }}</div>
    <div class="text-center">NIF: {{ $company->nif ?? '5001440276' }}</div>
    <div class="text-center">{{ $company->address ?? 'Luanda, Angola' }}</div>
    <div class="text-center">Tel: {{ $company->phone ?? '923 000 000' }}</div>

    <div class="line"></div>

    <div class="bold text-center" style="font-size: 12px;">FATURA-RECIBO (POS)</div>
    <div class="text-center">{{ $sale->doc_number }}</div>
    <div>Data: {{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y H:i') }}</div>
    <div>Cliente: {{ $sale->customer->name ?? 'Consumidor Final' }}</div>
    <div>NIF: {{ $sale->customer->nif ?? '999999999' }}</div>

    <div class="line"></div>

    <!-- Artigos -->
    <table>
        <thead>
            <tr>
                <th style="text-align: left;">Artigo</th>
                <th class="text-center">Qtd</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
            <tr>
                <td style="text-align: left;">{{ $item->product->name ?? 'Artigo' }}</td>
                <td class="text-center">{{ number_format($item->quantity, 0) }}</td>
                <td class="text-right">{{ number_format($item->quantity * $item->unit_price, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="line"></div>

    <!-- Totais -->
    <table>
        <tr>
            <td>Incidência IVA:</td>
            <td class="text-right">{{ number_format($sale->total_amount - $sale->total_tax, 2, ',', '.') }} Kz</td>
        </tr>
        <tr>
            <td>Total IVA (14%):</td>
            <td class="text-right">{{ number_format($sale->total_tax, 2, ',', '.') }} Kz</td>
        </tr>
        <tr class="bold" style="font-size: 13px;">
            <td>TOTAL:</td>
            <td class="text-right">{{ number_format($sale->total_amount, 2, ',', '.') }} Kz</td>
        </tr>
    </table>

    <div class="line"></div>

    <!-- Rodapé AGT -->
    <div class="agt-footer">
        <div class="bold">{{ $printMention }}</div>
        <div>Hash: {{ $sale->hash ? substr($sale->hash, 0, 30) . '...' : '' }}</div>
        <div style="margin-top: 5px;">Obrigado e volte sempre!</div>
    </div>

</body>
</html>

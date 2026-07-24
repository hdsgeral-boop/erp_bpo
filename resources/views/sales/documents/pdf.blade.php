<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>{{ $sale->doc_number }} - ERP Consulvolt</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #1e293b;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        .header-table, .details-table, .items-table, .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .header-table td {
            vertical-align: top;
        }
        .company-title {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
        }
        .doc-title {
            font-size: 18px;
            font-weight: bold;
            text-align: right;
            color: #2563eb;
            text-transform: uppercase;
        }
        .doc-number {
            font-size: 14px;
            text-align: right;
            color: #475569;
        }
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
            background: #f8fafc;
        }
        .items-table th {
            background: #1e293b;
            color: #fff;
            padding: 8px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }
        .items-table td {
            border-bottom: 1px solid #e2e8f0;
            padding: 8px;
            font-size: 11px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals-table td {
            padding: 4px 8px;
        }
        .total-row {
            font-size: 14px;
            font-weight: bold;
            background: #eff6ff;
            color: #1e40af;
        }
        .agt-mention {
            margin-top: 30px;
            border-top: 2px solid #cbd5e1;
            padding-top: 10px;
            font-size: 10px;
            color: #64748b;
            text-align: center;
        }
        .hash-code {
            font-family: monospace;
            font-weight: bold;
            color: #0f172a;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #2563eb; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            🖨️ Imprimir / Guardar PDF
        </button>
    </div>

    <!-- Cabeçalho -->
    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                <div class="company-title">{{ $company->name }}</div>
                <div>NIF: <strong>{{ $company->nif ?? '5001440276' }}</strong></div>
                <div>{{ $company->address ?? 'Luanda, Angola' }}</div>
                <div>Tel: {{ $company->phone ?? '+244 923 000 000' }} | Email: {{ $company->email ?? 'geral@consulvolt.com' }}</div>
            </td>
            <td style="width: 45%;" class="text-right">
                <div class="doc-title">
                    @switch($sale->doc_type)
                        @case('FT') FATURA @break
                        @case('FR') FATURA-RECIBO @break
                        @case('NC') NOTA DE CRÉDITO @break
                        @case('ND') NOTA DE DÉBITO @break
                        @case('PP') PROFORMA @break
                        @default {{ $sale->doc_type }}
                    @endswitch
                </div>
                <div class="doc-number">{{ $sale->doc_number }}</div>
                <div>Data: <strong>{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</strong></div>
                <div>Moeda: <strong>AOA (Kz)</strong></div>
            </td>
        </tr>
    </table>

    <!-- Dados do Cliente -->
    <table class="details-table">
        <tr>
            <td style="width: 100%;">
                <div class="card">
                    <strong>EXMO.(A) SENHOR(A):</strong> {{ $sale->customer->name ?? 'Consumidor Final' }}<br>
                    <strong>NIF:</strong> {{ $sale->customer->nif ?? '999999999' }} | <strong>Endereço:</strong> {{ $sale->customer->address ?? 'Luanda' }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabela de Itens -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 8%;">#</th>
                <th style="width: 45%;">Descrição do Artigo / Serviço</th>
                <th class="text-center" style="width: 12%;">Qtd</th>
                <th class="text-right" style="width: 15%;">Preço Unit.</th>
                <th class="text-center" style="width: 8%;">Taxa IVA</th>
                <th class="text-right" style="width: 12%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->product->name ?? 'Artigo' }}</td>
                <td class="text-center">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 2, ',', '.') }} Kz</td>
                <td class="text-center">{{ number_format($item->tax_rate ?? 14, 0) }}%</td>
                <td class="text-right">{{ number_format($item->quantity * $item->unit_price, 2, ',', '.') }} Kz</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totais e Resumo de IVA -->
    <table class="header-table">
        <tr>
            <td style="width: 50%;">
                <div class="card" style="font-size: 10px;">
                    <strong>Observações / Condições de Pagamento:</strong><br>
                    {{ $sale->notes ?? 'Obrigado pela sua preferência.' }}
                </div>
            </td>
            <td style="width: 50%;">
                <table class="totals-table">
                    <tr>
                        <td>Incidência (Base Tributável):</td>
                        <td class="text-right">{{ number_format($sale->total_amount - $sale->total_tax, 2, ',', '.') }} Kz</td>
                    </tr>
                    <tr>
                        <td>Total Imposto IVA (14%):</td>
                        <td class="text-right">{{ number_format($sale->total_tax, 2, ',', '.') }} Kz</td>
                    </tr>
                    <tr class="total-row">
                        <td>TOTAL A PAGAR:</td>
                        <td class="text-right">{{ number_format($sale->total_amount, 2, ',', '.') }} Kz</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Menção Legal AGT -->
    <div class="agt-mention">
        <div>{{ $printMention }}</div>
        <div>Hash Assinatura: <span class="hash-code">{{ $sale->hash ? substr($sale->hash, 0, 40) . '...' : 'Sem Hash' }}</span></div>
        <div style="margin-top: 4px; font-weight: bold;">Software Certificado pela AGT - ERP Consulvolt</div>
    </div>

</body>
</html>

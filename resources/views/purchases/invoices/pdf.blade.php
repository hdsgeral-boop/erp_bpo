<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Fatura de Fornecedor {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            margin: 25px 35px 40px 35px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9.5pt;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .header-table td {
            vertical-align: top;
        }
        .company-title {
            font-size: 15pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }
        .company-meta {
            font-size: 8.5pt;
            color: #475569;
        }
        .doc-box {
            text-align: right;
        }
        .doc-title {
            font-size: 14pt;
            font-weight: bold;
            color: #1e40af;
            text-transform: uppercase;
            margin: 0 0 4px 0;
        }
        .doc-number {
            font-size: 11.5pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 6px 0;
        }
        .doc-meta {
            font-size: 8.5pt;
            color: #475569;
        }
        .watermark {
            position: absolute;
            top: 35%;
            left: 20%;
            font-size: 70pt;
            font-weight: bold;
            color: rgba(220, 38, 38, 0.12);
            transform: rotate(-30deg);
            text-transform: uppercase;
            z-index: 0;
        }
        .info-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            vertical-align: top;
        }
        .label {
            font-size: 7.5pt;
            text-transform: uppercase;
            font-weight: bold;
            color: #64748b;
        }
        .value {
            font-size: 10.5pt;
            font-weight: bold;
            color: #0f172a;
        }
        .table-items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .table-items th {
            background-color: #1e293b;
            color: #ffffff;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px 10px;
            border: 1px solid #1e293b;
        }
        .table-items td {
            font-size: 8.5pt;
            padding: 7px 10px;
            border-bottom: 1px solid #e2e8f0;
            border-left: 1px solid #f1f5f9;
            border-right: 1px solid #f1f5f9;
        }
        .table-items tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }
        .total-box {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 25px;
        }
        .total-card {
            background-color: #eff6ff;
            border: 2px solid #2563eb;
            border-radius: 6px;
            padding: 10px 16px;
            text-align: right;
        }
        .total-label {
            font-size: 9pt;
            font-weight: bold;
            color: #1e40af;
            text-transform: uppercase;
        }
        .total-amount {
            font-size: 15pt;
            font-weight: bold;
            color: #1e3a8a;
        }
        .footer {
            position: fixed;
            bottom: -20px;
            left: 0px;
            right: 0px;
            height: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
            font-size: 8pt;
            color: #64748b;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
    </style>
</head>
<body>

    @if($invoice->status === 'CANCELLED')
        <div class="watermark">ANULADA</div>
    @endif

    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                <div class="company-title">{{ $invoice->company->name ?? 'CONSULVOLT SOLUÇÕES - ERP' }}</div>
                <div class="company-meta">NIF: {{ $invoice->company->nif ?? '5417000000' }}</div>
                <div class="company-meta">{{ $invoice->company->address ?? 'Luanda, Angola' }}</div>
            </td>
            <td style="width: 45%;" class="doc-box">
                <div class="doc-title">FATURA DE FORNECEDOR</div>
                <div class="doc-number">{{ $invoice->invoice_number }}</div>
                <div class="doc-meta"><strong>Data de Emissão:</strong> {{ \Carbon\Carbon::parse($invoice->date)->format('d/m/Y') }}</div>
                <div class="doc-meta"><strong>Estado:</strong> {{ $invoice->status === 'CANCELLED' ? 'ANULADA' : 'REGISTADA' }}</div>
            </td>
        </tr>
    </table>

    <div class="info-box">
        <table class="info-table">
            <tr>
                <td style="width: 60%;">
                    <div class="label">FORNECEDOR / EMITENTE DE ORIGEM</div>
                    <div class="value">{{ $invoice->supplier->name ?? 'Fornecedor Desconhecido' }}</div>
                    <div style="font-size: 8.5pt; color: #475569; margin-top: 2px;">
                        NIF: {{ $invoice->supplier->nif ?? 'N/D' }}<br>
                        {{ $invoice->supplier->address ?? '' }}
                    </div>
                </td>
                <td style="width: 40%;">
                    <div class="label">DETALHES DA COMPRA</div>
                    <div class="value" style="font-size: 9.5pt; color: #1e293b;">
                        Estado Pagamento: <strong>{{ $invoice->payment_status ?? 'PENDING' }}</strong><br>
                        Valor Pago: <strong>{{ number_format($invoice->amount_paid ?? 0, 2, ',', '.') }} Kz</strong>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <table class="table-items">
        <thead>
            <tr>
                <th style="width: 6%;" class="text-center">#</th>
                <th style="width: 44%;">ARTIGO / DESCRIÇÃO</th>
                <th style="width: 15%;" class="text-center">QTD.</th>
                <th style="width: 17%;" class="text-right">PREÇO UNIT.</th>
                <th style="width: 18%;" class="text-right">TOTAL LINHA</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoice->items as $idx => $item)
                <tr>
                    <td class="text-center" style="color: #64748b;">{{ $idx + 1 }}</td>
                    <td><strong>{{ $item->product->name ?? 'Artigo/Despesa' }}</strong></td>
                    <td class="text-center font-mono">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                    <td class="text-right font-mono">{{ number_format($item->unit_price, 2, ',', '.') }} Kz</td>
                    <td class="text-right font-mono fw-bold">{{ number_format($item->total_price ?? ($item->quantity * $item->unit_price), 2, ',', '.') }} Kz</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Sem linhas discriminadas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="total-box">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%;">
                <div class="total-card">
                    <div class="total-label">TOTAL DA FATURA DE COMPRA</div>
                    <div class="total-amount">{{ number_format($invoice->total_amount, 2, ',', '.') }} AKZ</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td style="width: 70%;">Processado por programa certificado n.º 142/AGT/2019 - CONSULVOLT ERP v2.2</td>
                <td style="width: 30%; text-align: right;">Página 1 de 1</td>
            </tr>
        </table>
    </div>

</body>
</html>

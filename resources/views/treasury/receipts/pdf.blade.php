<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>{{ $receipt->doc_type }} {{ $receipt->doc_number }}</title>
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

        /* Header Layout */
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
            font-size: 15pt;
            font-weight: bold;
            color: #1e40af;
            text-transform: uppercase;
            margin: 0 0 4px 0;
        }
        .doc-number {
            font-size: 12pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 6px 0;
        }
        .doc-meta {
            font-size: 8.5pt;
            color: #475569;
        }

        /* Watermark for Cancelled */
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

        /* Entity & Payment Details Box */
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
            padding: 4px 0;
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

        /* Table of Liquidated Items */
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

        /* Total Box */
        .total-box {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 25px;
        }
        .total-box td {
            padding: 6px 10px;
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
            font-size: 16pt;
            font-weight: bold;
            color: #1e3a8a;
        }

        /* Signatures */
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
        }
        .signatures-table td {
            width: 45%;
            text-align: center;
            vertical-align: bottom;
            padding: 0 10px;
        }
        .signature-line {
            border-top: 1px solid #94a3b8;
            margin-top: 45px;
            padding-top: 6px;
            font-size: 8.5pt;
            font-weight: bold;
            color: #334155;
        }

        /* Footer */
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

    @if($receipt->status === 'CANCELLED')
        <div class="watermark">ANULADO</div>
    @endif

    @php
        $isReceipt = $receipt->doc_type === 'RC';
        $docTitleName = $isReceipt ? 'RECIBO DE LIQUIDAÇÃO' : 'COMPROVATIVO DE PAGAMENTO';
        $entityLabel = $isReceipt ? 'RECEBIDO DE (CLIENTE)' : 'PAGO A (BENEFICIÁRIO / FORNECEDOR)';
    @endphp

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                <div class="company-title">{{ $receipt->company->name ?? 'CONSULVOLT SOLUÇÕES - ERP' }}</div>
                <div class="company-meta">NIF: {{ $receipt->company->nif ?? '5417000000' }}</div>
                <div class="company-meta">{{ $receipt->company->address ?? 'Luanda, Angola' }}</div>
                <div class="company-meta">Email: {{ $receipt->company->email ?? 'contacto@consulvolt.co.ao' }} | Tel: {{ $receipt->company->phone ?? '+244 923 000 000' }}</div>
            </td>
            <td style="width: 45%;" class="doc-box">
                <div class="doc-title">{{ $docTitleName }}</div>
                <div class="doc-number">{{ $receipt->doc_type }} {{ $receipt->doc_number }}</div>
                <div class="doc-meta"><strong>Data de Emissão:</strong> {{ $receipt->date ? $receipt->date->format('d/m/Y') : date('d/m/Y') }}</div>
                <div class="doc-meta"><strong>Estado:</strong> {{ $receipt->status === 'ISSUED' ? 'EMITIDO (VÁLIDO)' : 'ANULADO' }}</div>
            </td>
        </tr>
    </table>

    <!-- Info Box -->
    <div class="info-box">
        <table class="info-table">
            <tr>
                <td style="width: 55%;">
                    <div class="label">{{ $entityLabel }}</div>
                    <div class="value">{{ $receipt->thirdParty->name ?? 'Consumidor Final / Geral' }}</div>
                    <div style="font-size: 8.5pt; color: #475569; margin-top: 2px;">
                        NIF: {{ $receipt->thirdParty->nif ?? '999999999' }}<br>
                        {{ $receipt->thirdParty->address ?? '' }}
                    </div>
                </td>
                <td style="width: 45%;">
                    <div class="label">DETALHES DE PAGAMENTO / CONTA</div>
                    <div class="value" style="font-size: 9.5pt; color: #1e293b;">
                        Conta: <strong>{{ $receipt->treasuryAccount->name ?? 'Caixa Geral / Banco' }}</strong><br>
                        Método: <strong>{{ $receipt->payment_method ?? 'TRANSFERÊNCIA' }}</strong>
                        @if($receipt->payment_reference)
                            <br>Ref: <strong>{{ $receipt->payment_reference }}</strong>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Items Table -->
    <table class="table-items">
        <thead>
            <tr>
                <th style="width: 8%;" class="text-center">#</th>
                <th style="width: 35%;">DOCUMENTO REFERÊNCIA</th>
                <th style="width: 22%;" class="text-center">DATA DOC.</th>
                <th style="width: 35%;" class="text-right">VALOR LIQUIDADO</th>
            </tr>
        </thead>
        <tbody>
            @forelse($receipt->items as $idx => $item)
                <tr>
                    <td class="text-center" style="color: #64748b;">{{ $idx + 1 }}</td>
                    <td>
                        @if($item->sale_id && $item->sale)
                            <strong>{{ $item->sale->doc_type }} {{ $item->sale->doc_number }}</strong>
                        @elseif($item->purchase_invoice_id && $item->purchaseInvoice)
                            <strong>COMPRA {{ $item->purchaseInvoice->doc_number }}</strong>
                        @else
                            <strong>{{ $receipt->payment_reference ?? 'Liquidação Financeira Directa' }}</strong>
                        @endif
                    </td>
                    <td class="text-center font-mono">
                        @if($item->sale_id && $item->sale && $item->sale->date)
                            {{ $item->sale->date->format('d/m/Y') }}
                        @elseif($item->purchase_invoice_id && $item->purchaseInvoice && $item->purchaseInvoice->date)
                            {{ $item->purchaseInvoice->date->format('d/m/Y') }}
                        @else
                            {{ $receipt->date ? $receipt->date->format('d/m/Y') : date('d/m/Y') }}
                        @endif
                    </td>
                    <td class="text-right font-mono fw-bold">
                        {{ number_format($item->amount_paid, 2, ',', '.') }} Kz
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="text-center">1</td>
                    <td><strong>{{ $receipt->payment_reference ?? 'Liquidação Geral de Conta' }}</strong></td>
                    <td class="text-center font-mono">{{ $receipt->date ? $receipt->date->format('d/m/Y') : date('d/m/Y') }}</td>
                    <td class="text-right font-mono fw-bold">{{ number_format($receipt->total_amount, 2, ',', '.') }} Kz</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Total Box -->
    <table class="total-box">
        <tr>
            <td style="width: 50%; vertical-align: middle;">
                <div style="font-size: 8pt; color: #64748b;">
                    Este documento serve de recibo oficial e comprovativo de pagamento/liquidação das obrigações discriminadas.
                </div>
            </td>
            <td style="width: 50%;">
                <div class="total-card">
                    <div class="total-label">TOTAL {{ $isReceipt ? 'RECEBIDO' : 'PAGO' }}</div>
                    <div class="total-amount">{{ number_format($receipt->total_amount, 2, ',', '.') }} {{ $receipt->treasuryAccount->currency ?? 'AOA' }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Signatures -->
    <table class="signatures-table">
        <tr>
            <td>
                <div class="signature-line">Assinatura / Carimbo do Emissor</div>
            </td>
            <td style="width: 10%;"></td>
            <td>
                <div class="signature-line">Assinatura do Recipiente / Beneficiário</div>
            </td>
        </tr>
    </table>

    <!-- Footer -->
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

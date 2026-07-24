<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sale->doc_number }} - ERP Consulvolt</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 12mm 18mm 15mm;
        }
        
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #000000;
            background: #e2e8f0;
            margin: 0;
            padding: 20px 0;
        }

        .a4-page {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 15mm 15mm 20mm 18mm;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Top Action Bar for Browser Viewing */
        .action-bar {
            width: 210mm;
            margin: 0 auto 15px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-action {
            padding: 8px 18px;
            font-size: 0.85rem;
            font-weight: 700;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        .btn-print { background: #0284c7; color: #ffffff; }
        .btn-print:hover { background: #0369a1; }
        .btn-back { background: #cbd5e1; color: #1e293b; }

        /* Left Vertical Watermark / Margin Text */
        .side-watermark {
            position: absolute;
            left: 3mm;
            bottom: 25mm;
            transform: rotate(-90deg);
            transform-origin: left bottom;
            font-size: 7px;
            color: #64748b;
            white-space: nowrap;
            font-family: monospace;
        }

        /* Header Layout */
        .header-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
        }
        .company-header {
            width: 58%;
        }
        .company-logo {
            max-height: 55px;
            max-width: 180px;
            margin-bottom: 8px;
            object-fit: contain;
        }
        .logo-placeholder {
            width: 48px;
            height: 48px;
            background: #0284c7;
            color: #ffffff;
            font-size: 24px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            margin-bottom: 8px;
        }
        .company-title {
            font-size: 13px;
            font-weight: 800;
            color: #000000;
            margin-bottom: 3px;
        }
        .company-details {
            font-size: 9px;
            line-height: 1.35;
            color: #111111;
        }

        .customer-header {
            width: 38%;
            padding-top: 15px;
        }
        .customer-name {
            font-size: 12px;
            font-weight: 800;
            color: #000000;
            margin-bottom: 2px;
        }
        .customer-address {
            font-size: 9px;
            color: #111111;
        }

        /* Document Title Bar */
        .doc-title-bar {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 6px;
        }
        .doc-title {
            font-size: 14px;
            font-weight: 800;
            color: #000000;
        }
        .doc-copy {
            font-size: 9.5px;
            font-weight: 700;
            color: #000000;
        }

        /* Metadata Header Table with Underlines */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .meta-table th {
            font-size: 8.5px;
            font-weight: 700;
            color: #000000;
            text-align: left;
            padding-bottom: 3px;
            border-bottom: 1px solid #000000;
        }
        .meta-table td {
            font-size: 9.5px;
            padding-top: 4px;
            padding-bottom: 8px;
            color: #000000;
            border-bottom: 1px solid #e2e8f0;
        }

        /* Observacoes Block */
        .obs-container {
            font-size: 8.5px;
            line-height: 1.35;
            color: #000000;
            margin-bottom: 15px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .items-table th {
            font-size: 9px;
            font-weight: 800;
            color: #000000;
            padding: 5px 4px;
            border-bottom: 1px solid #000000;
            border-top: 1px solid #000000;
            text-align: left;
        }
        .items-table td {
            padding: 6px 4px;
            font-size: 9px;
            color: #000000;
            vertical-align: top;
        }
        .item-code {
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            font-size: 8.5px;
        }
        .item-desc-sub {
            font-size: 8px;
            color: #475569;
            margin-top: 2px;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Bottom Summary Section */
        .summary-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        .summary-left {
            width: 58%;
        }
        .summary-right {
            width: 38%;
        }

        /* Tax Breakdown Table */
        .tax-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .tax-table th {
            font-size: 8.5px;
            font-weight: 800;
            color: #000000;
            border-bottom: 1px solid #000000;
            padding-bottom: 2px;
            text-align: left;
        }
        .tax-table td {
            font-size: 8.5px;
            padding: 3px 0;
            color: #000000;
        }
        .tax-note {
            font-size: 7.5px;
            color: #334155;
            margin-top: 2px;
            margin-bottom: 12px;
        }

        /* Payment & Bank Details */
        .payment-block-title {
            font-size: 8.5px;
            font-weight: 800;
            color: #000000;
            margin-bottom: 2px;
        }
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            margin-bottom: 10px;
        }
        .payment-table td {
            padding: 2px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        /* Grand Totals Table */
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-table th {
            font-size: 8.5px;
            font-weight: 800;
            color: #000000;
            border-bottom: 1px solid #000000;
            padding-bottom: 2px;
            text-align: left;
        }
        .totals-table td {
            font-size: 9px;
            padding: 4px 0;
            color: #000000;
        }
        .total-final-row td {
            font-size: 13px;
            font-weight: 800;
            color: #000000;
            border-top: 2px solid #000000;
            padding-top: 6px !important;
        }

        /* Page Footer Fixed at Bottom */
        .document-footer {
            position: absolute;
            bottom: 8mm;
            left: 18mm;
            right: 15mm;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 8px;
            color: #111111;
            border-top: 1px solid #cbd5e1;
            padding-top: 5px;
            background: #ffffff;
        }
        .footer-left {
            font-weight: 700;
        }
        .footer-right {
            font-family: 'JetBrains Mono', monospace;
            font-size: 7.5px;
            font-weight: 600;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .a4-page {
                box-shadow: none;
                width: 100%;
                min-height: auto;
                padding: 0;
                margin: 0;
            }
            .document-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                padding-bottom: 4mm;
            }
        }
    </style>
</head>
<body>

    <!-- Action Bar for Screen View -->
    <div class="action-bar no-print">
        <button onclick="window.history.back()" class="btn-action btn-back">
            ← Voltar
        </button>
        <div style="font-weight: 700; color: #475569;">Pré-visualização de Documento Comercial (A4)</div>
        <button onclick="window.print()" class="btn-action btn-print">
            🖨️ Imprimir / Guardar PDF
        </button>
    </div>

    <!-- Main A4 Page Canvas -->
    <div class="a4-page">
        <!-- Vertical Left Margin Text -->
        <div class="side-watermark">
            Documento Eletrónico Vendas - {{ date('Y/m/d H:i') }} UTC | {{ $sale->hash ? (substr($sale->hash, 0, 45) . '...') : 'k1HsJsZRhHMu5BSrks3ZByzdRR8=000000000000' }}
        </div>

        <div>
            <!-- Company & Customer Header Section -->
            <div class="header-section">
                <!-- Left: Company Info -->
                <div class="company-header">
                    @if($company->logo_path)
                        <img src="{{ Storage::url($company->logo_path) }}" alt="{{ $company->name }}" class="company-logo">
                    @else
                        <div class="logo-placeholder">
                            {{ strtoupper(substr($company->name ?? 'D', 0, 1)) }}
                        </div>
                    @endif
                    <div class="company-title">{{ $company->name ?? 'D Designer Interiores, Lda' }}</div>
                    <div class="company-details">
                        {{ $company->address ?? 'Luanda, Município de Talatona, Bairro Talatona, Rua Dolce Vita, casa n.º Condomínio Dolce Vita, 4D R/C - Luanda' }}<br>
                        <strong>Contribuinte:</strong> {{ $company->nif ?? '5417370762' }}<br>
                        <strong>E-mail:</strong> {{ $company->email ?? 'geral@ddesigner.ao' }}<br>
                        <strong>Tel:</strong> {{ $company->phone ?? '940 514 986' }}
                    </div>
                </div>

                <!-- Right: Customer Info -->
                <div class="customer-header">
                    <div class="customer-name">{{ $sale->customer->name ?? 'Aníbal Martinho Txesseca Tunga' }}</div>
                    <div class="customer-address">
                        {{ $sale->customer->address ?? 'Angola' }}
                    </div>
                </div>
            </div>

            <!-- Document Title Bar -->
            <div class="doc-title-bar">
                <div class="doc-title">
                    @switch($sale->doc_type)
                        @case('FT') Factura n.º {{ $sale->doc_number }} @break
                        @case('FR') Factura-Recibo n.º {{ $sale->doc_number }} @break
                        @case('FS') Factura Simplificada n.º {{ $sale->doc_number }} @break
                        @case('NC') Nota de Crédito n.º {{ $sale->doc_number }} @break
                        @case('ND') Nota de Débito n.º {{ $sale->doc_number }} @break
                        @case('PP') Factura Pró-Forma n.º {{ $sale->doc_number }} @break
                        @case('OR') Orçamento n.º {{ $sale->doc_number }} @break
                        @case('GT') Guia de Transporte n.º {{ $sale->doc_number }} @break
                        @default Documento n.º {{ $sale->doc_number }}
                    @endswitch
                </div>
                <div class="doc-copy">Original</div>
            </div>

            <!-- Underlined Metadata Bar -->
            <table class="meta-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Data de Emissão</th>
                        <th style="width: 25%;">Vencimento</th>
                        <th style="width: 25%;">Contribuinte</th>
                        <th style="width: 25%;">V/ Ref.</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($sale->date)->format('Y-m-d') }}</td>
                        <td>{{ \Carbon\Carbon::parse($sale->due_date ?? $sale->date)->format('Y-m-d') }}</td>
                        <td style="font-family: 'JetBrains Mono', monospace; font-weight: 600;">{{ $sale->customer->nif ?? '000159923LN011' }}</td>
                        <td>{{ $sale->doc_number }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Observacoes Block -->
            <div class="obs-container">
                <strong>Observações:</strong> {{ $sale->notes ?? 'Total: os bens/serviços foram colocados à disposição do adquirente na data do documento.' }}
            </div>

            <!-- Items Table -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 16%;">Código</th>
                        <th style="width: 44%;">Descrição</th>
                        <th class="text-right" style="width: 14%;">Pr. Unitário</th>
                        <th class="text-center" style="width: 6%;">Uni.</th>
                        <th class="text-center" style="width: 6%;">Qtd.</th>
                        <th class="text-center" style="width: 7%;">IVA</th>
                        <th class="text-right" style="width: 14%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $item)
                    @php
                        $taxRate = (float)($item->tax_rate ?? 14);
                        $hasTax = $taxRate > 0;
                    @endphp
                    <tr>
                        <td class="item-code">{{ $item->product->code ?? 'VCON3-26072352' }}</td>
                        <td>
                            <div style="font-weight: 700;">{{ $item->product->name ?? 'Confecção e Instalação de Cortinados' }}</div>
                            @if(!empty($item->description))
                                <div class="item-desc-sub">{{ $item->description }}</div>
                            @endif
                        </td>
                        <td class="text-right">{{ number_format($item->unit_price, 2, ',', '.') }} Kz</td>
                        <td class="text-center">Uni</td>
                        <td class="text-center" style="font-weight: 700;">{{ (int)$item->quantity }}</td>
                        <td class="text-center">{{ (int)$taxRate }}% @if(!$hasTax)<sup>(1)</sup>@endif</td>
                        <td class="text-right" style="font-weight: 700;">{{ number_format($item->subtotal ?? ($item->quantity * $item->unit_price), 2, ',', '.') }} Kz</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Bottom Summary & Bank Details Section -->
            <div class="summary-section">
                <!-- Left: Tax Breakdown & Payment Details -->
                <div class="summary-left">
                    <!-- Tax Table -->
                    <table class="tax-table">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Taxa</th>
                                <th style="width: 50%;">Incidência</th>
                                <th class="text-right" style="width: 25%;">IVA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $taxRateDisplay = (int)($sale->items->first()->tax_rate ?? 14);
                            @endphp
                            <tr>
                                <td>{{ $taxRateDisplay }}%</td>
                                <td>{{ number_format($sale->total_amount - $sale->total_tax, 2, ',', '.') }} Kz</td>
                                <td class="text-right">{{ number_format($sale->total_tax, 2, ',', '.') }} Kz</td>
                            </tr>
                        </tbody>
                    </table>
                    @if($sale->total_tax == 0)
                    <div class="tax-note">
                        (1) IVA – Regime de Exclusão (ou Isenção ao abrigo do artigo 12.º do CIVA)
                    </div>
                    @endif

                    <!-- Payment Method -->
                    <div class="payment-block-title" style="margin-top: 10px;">Meio de Pagamento</div>
                    <table class="payment-table">
                        <tr>
                            <td>{{ $sale->payment_method ?? 'Transferência Bancária' }}</td>
                            <td class="text-right" style="font-weight: 700;">{{ number_format($sale->total_amount, 2, ',', '.') }} Kz</td>
                        </tr>
                    </table>

                    <!-- Bank Details -->
                    <div class="payment-block-title">Dados Bancários</div>
                    <table class="payment-table">
                        <tr>
                            <td style="width: 30%;">IBAN</td>
                            <td style="font-family: 'JetBrains Mono', monospace; font-weight: 700;">
                                {{ $company->iban ?? 'AO06.0040.0000.6978.2382.1012.1' }}
                            </td>
                        </tr>
                        @if($company->bank_name)
                        <tr>
                            <td>Banco</td>
                            <td>{{ $company->bank_name }}</td>
                        </tr>
                        @endif
                    </table>
                </div>

                <!-- Right: Grand Totals -->
                <div class="summary-right">
                    <table class="totals-table">
                        <thead>
                            <tr>
                                <th colspan="2">Sumário</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>S/IVA</td>
                                <td class="text-right fw-bold">{{ number_format($sale->total_amount - $sale->total_tax, 2, ',', '.') }} Kz</td>
                            </tr>
                            <tr>
                                <td>IVA</td>
                                <td class="text-right fw-bold">{{ number_format($sale->total_tax, 2, ',', '.') }} Kz</td>
                            </tr>
                            <tr class="total-final-row">
                                <td>Total</td>
                                <td class="text-right">{{ number_format($sale->total_amount, 2, ',', '.') }} Kz</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Page Footer Fixed at Bottom -->
        <div class="document-footer">
            <div class="footer-left">
                Página 1/1 &nbsp;|&nbsp; {{ $company->name ?? 'D DESIGNER INTERIORES, LDA' }}
            </div>
            <div class="footer-right">
                {{ $printMention }}
            </div>
        </div>
    </div>

</body>
</html>

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
            margin: 8mm 10mm 15mm 10mm;
        }
        
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9.5px;
            color: #0f172a;
            background: #e2e8f0;
            margin: 0;
            padding: 20px 0;
        }

        .a4-page {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 15mm 15mm 20mm 26mm;
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
            left: 5mm;
            top: 210mm;
            transform: rotate(-90deg);
            transform-origin: 0 0;
            font-size: 6.8px;
            color: #64748b;
            white-space: nowrap;
            font-family: 'JetBrains Mono', monospace;
            letter-spacing: -0.2px;
            opacity: 0.8;
        }

        /* Header Layout */
        .header-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 22px;
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
            color: #0f172a;
            margin-bottom: 3px;
        }
        .company-details {
            font-size: 9px;
            line-height: 1.35;
            color: #334155;
        }

        .customer-header {
            width: 38%;
            padding-top: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 14px;
        }
        .customer-title-tag {
            font-size: 7.5px;
            font-weight: 800;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }
        .customer-name {
            font-size: 11.5px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 3px;
        }
        .customer-address {
            font-size: 8.5px;
            color: #475569;
            line-height: 1.3;
        }

        /* Document Title Bar */
        .doc-title-bar {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 6px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 4px;
        }
        .doc-title {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
        }
        .doc-copy {
            font-size: 9px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Related Document Notice Box for Credit Notes */
        .rectification-box {
            background: #fefce8;
            border: 1px solid #fef08a;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 12px;
            font-size: 8.5px;
            color: #854d0e;
        }

        /* Metadata Header Table with Underlines */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .meta-table th {
            font-size: 8px;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            text-align: left;
            padding-bottom: 3px;
            border-bottom: 1px solid #cbd5e1;
        }
        .meta-table td {
            font-size: 9px;
            padding-top: 4px;
            padding-bottom: 6px;
            color: #0f172a;
            border-bottom: 1px solid #f1f5f9;
        }

        /* Observacoes Block */
        .obs-container {
            font-size: 8.5px;
            line-height: 1.35;
            color: #334155;
            margin-bottom: 15px;
            background: #f8fafc;
            padding: 6px 10px;
            border-radius: 6px;
            border-left: 3px solid #0284c7;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            font-size: 8.5px;
            font-weight: 800;
            color: #0f172a;
            padding: 6px 4px;
            border-bottom: 1.5px solid #0f172a;
            border-top: 1.5px solid #0f172a;
            text-align: left;
            text-transform: uppercase;
        }
        .items-table td {
            padding: 7px 4px;
            font-size: 8.5px;
            color: #0f172a;
            vertical-align: top;
            border-bottom: 1px solid #f1f5f9;
        }
        .item-code {
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            font-size: 8px;
            color: #475569;
        }
        .item-desc-sub {
            font-size: 7.5px;
            color: #64748b;
            margin-top: 2px;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Bottom Summary Section */
        .summary-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        .summary-left {
            width: 56%;
        }
        .summary-right {
            width: 40%;
        }

        /* Tax Breakdown Table */
        .tax-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .tax-table th {
            font-size: 8px;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 3px;
            text-align: left;
        }
        .tax-table td {
            font-size: 8px;
            padding: 3px 0;
            color: #0f172a;
            border-bottom: 1px solid #f1f5f9;
        }
        .tax-note {
            font-size: 7.5px;
            color: #64748b;
            margin-top: 2px;
            margin-bottom: 10px;
        }

        /* Payment & Bank Details */
        .payment-block-title {
            font-size: 8px;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
            margin-bottom: 8px;
        }
        .payment-table td {
            padding: 2px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        /* Grand Totals Table */
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            background: #f8fafc;
            border-radius: 8px;
            padding: 10px;
        }
        .totals-table th {
            font-size: 8.5px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
            text-align: left;
        }
        .totals-table td {
            font-size: 8.5px;
            padding: 4px 6px;
            color: #0f172a;
        }
        .total-final-row td {
            font-size: 12px;
            font-weight: 800;
            color: #0284c7;
            border-top: 2px solid #0f172a;
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
            color: #475569;
            border-top: 1px solid #cbd5e1;
            padding-top: 5px;
            background: #ffffff;
        }
        .footer-left {
            font-weight: 600;
        }
        .footer-right {
            font-family: 'JetBrains Mono', monospace;
            font-size: 7.5px;
            font-weight: 600;
            color: #0f172a;
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
                padding: 10mm 12mm 15mm 24mm !important;
                margin: 0;
            }
            .side-watermark {
                position: fixed;
                left: 4mm;
                top: 210mm;
                transform: rotate(-90deg);
                transform-origin: 0 0;
            }
            .document-footer {
                position: fixed;
                bottom: 0;
                left: 24mm;
                right: 12mm;
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
                            {{ strtoupper(substr($company->name ?? 'E', 0, 1)) }}
                        </div>
                    @endif
                    <div class="company-title">{{ $company->name ?? 'CONSULVOLT, LDA' }}</div>
                    <div class="company-details">
                        {{ $company->address ?? 'Luanda, Angola' }}<br>
                        <strong>Contribuinte (NIF):</strong> {{ $company->nif ?? '5417370762' }}<br>
                        <strong>E-mail:</strong> {{ $company->email ?? 'geral@consulvolt.ao' }}<br>
                        <strong>Tel:</strong> {{ $company->phone ?? '923 000 000' }}
                    </div>
                </div>

                <!-- Right: Customer Info -->
                <div class="customer-header">
                    <div class="customer-title-tag">Faturado a (Cliente):</div>
                    <div class="customer-name">{{ $sale->customer->name ?? 'Consumidor Final' }}</div>
                    <div class="customer-address">
                        <strong>NIF:</strong> {{ $sale->customer->nif ?? ($sale->customer->tax_id ?? 'Consumidor Final') }}<br>
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

            <!-- Rectification Box for Credit Notes -->
            @if($sale->doc_type === 'NC' || $sale->relatedDoc || $sale->cancellation_reason)
            <div class="rectification-box">
                <strong>DOCUMENTO DE ORIGEM RETIFICADO / ANULADO:</strong> 
                <span style="font-weight: 700; font-family: 'JetBrains Mono', monospace;">{{ $sale->relatedDoc->doc_number ?? 'Fatura de Origem' }}</span>
                @if($sale->relatedDoc)
                    ({{ \Carbon\Carbon::parse($sale->relatedDoc->date)->format('d/m/Y') }})
                @endif
                @if($sale->cancellation_reason)
                    <br><strong>MOTIVO FISCAL DA ANULAÇÃO (AGT):</strong> {{ $sale->cancellation_reason }}
                @endif
            </div>
            @endif

            <!-- Underlined Metadata Bar -->
            <table class="meta-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Data de Emissão</th>
                        <th style="width: 25%;">Vencimento</th>
                        <th style="width: 25%;">NIF Cliente</th>
                        <th style="width: 25%;">Referência</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($sale->due_date ?? $sale->date)->format('d/m/Y') }}</td>
                        <td style="font-family: 'JetBrains Mono', monospace; font-weight: 700;">{{ $sale->customer->nif ?? ($sale->customer->tax_id ?? 'Consumidor Final') }}</td>
                        <td style="font-weight: 700;">{{ $sale->doc_number }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Observacoes Block -->
            @if(!empty($sale->notes))
            <div class="obs-container">
                <strong>Observações:</strong> {{ $sale->notes }}
            </div>
            @endif

            <!-- Items Table -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 16%;">Código</th>
                        <th style="width: 44%;">Descrição / Artigo</th>
                        <th class="text-right" style="width: 14%;">Pr. Unitário</th>
                        <th class="text-center" style="width: 6%;">Qtd.</th>
                        <th class="text-center" style="width: 6%;">Desc.</th>
                        <th class="text-center" style="width: 7%;">IVA</th>
                        <th class="text-right" style="width: 14%;">Total Líquido</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $item)
                    @php
                        $taxRate = (float)($item->tax_rate ?? 14);
                        $hasTax = $taxRate > 0;
                    @endphp
                    <tr>
                        <td class="item-code">{{ $item->product->code ?? 'ART-00' . $item->id }}</td>
                        <td>
                            <div style="font-weight: 700;">{{ $item->product->name ?? 'Artigo / Serviço' }}</div>
                            @if(!empty($item->description))
                                <div class="item-desc-sub">{{ $item->description }}</div>
                            @endif
                        </td>
                        <td class="text-right">{{ number_format($item->unit_price, 2, ',', '.') }} Kz</td>
                        <td class="text-center" style="font-weight: 700;">{{ (int)$item->quantity }}</td>
                        <td class="text-center">{{ number_format($item->discount_amount ?? 0, 2, ',', '.') }}</td>
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
                                <th style="width: 25%;">Taxa IVA</th>
                                <th style="width: 45%;">Incidência (Sem IVA)</th>
                                <th class="text-right" style="width: 30%;">Valor Imposto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $taxRateDisplay = (int)($sale->items->first()->tax_rate ?? 14);
                            @endphp
                            <tr>
                                <td>{{ $taxRateDisplay }}%</td>
                                <td>{{ number_format($sale->total_amount, 2, ',', '.') }} Kz</td>
                                <td class="text-right">{{ number_format($sale->total_tax, 2, ',', '.') }} Kz</td>
                            </tr>
                        </tbody>
                    </table>
                    @if($sale->total_tax == 0)
                    <div class="tax-note">
                        (1) IVA – Regime de Exclusão ou Isenção ao abrigo do artigo 9.º do CIVA.
                    </div>
                    @endif

                    <!-- Payment Method -->
                    <div class="payment-block-title" style="margin-top: 6px;">Meio de Pagamento</div>
                    <table class="payment-table">
                        <tr>
                            <td>{{ $sale->payment_method ?? 'Pronto Pagamento / Transferência' }}</td>
                            <td class="text-right" style="font-weight: 700;">{{ number_format($sale->total_amount + $sale->total_tax, 2, ',', '.') }} Kz</td>
                        </tr>
                    </table>

                    <!-- Bank Details -->
                    @if(!empty($company->iban))
                    <div class="payment-block-title">Dados Bancários</div>
                    <table class="payment-table">
                        <tr>
                            <td style="width: 30%;">IBAN</td>
                            <td style="font-family: 'JetBrains Mono', monospace; font-weight: 700;">
                                {{ $company->iban }}
                            </td>
                        </tr>
                    </table>
                    @endif
                </div>

                <!-- Right: Grand Totals -->
                <div class="summary-right">
                    <table class="totals-table">
                        <thead>
                            <tr>
                                <th colspan="2">Resumo Financeiro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Subtotal (Sem IVA)</td>
                                <td class="text-right fw-bold">{{ number_format($sale->total_amount, 2, ',', '.') }} Kz</td>
                            </tr>
                            <tr>
                                <td>Desconto Total</td>
                                <td class="text-right text-danger fw-bold">{{ number_format($sale->total_discount ?? 0, 2, ',', '.') }} Kz</td>
                            </tr>
                            <tr>
                                <td>Total IVA</td>
                                <td class="text-right fw-bold">{{ number_format($sale->total_tax, 2, ',', '.') }} Kz</td>
                            </tr>
                            <tr class="total-final-row">
                                <td>Total a Pagar/Creditar</td>
                                <td class="text-right">{{ number_format($sale->total_amount + $sale->total_tax, 2, ',', '.') }} Kz</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Page Footer Fixed at Bottom -->
        <div class="document-footer">
            <div class="footer-left">
                Página 1/1 &nbsp;|&nbsp; {{ $company->name ?? 'CONSULVOLT, LDA' }}
            </div>
            <div class="footer-right">
                {{ $printMention }}
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('print')) {
                setTimeout(function() {
                    window.print();
                }, 300);
            }
        });
    </script>
</body>
</html>

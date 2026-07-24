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
            margin: 12mm 15mm 25mm 15mm;
        }
        
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: #f1f5f9;
            margin: 0;
            padding: 20px 0;
        }

        .a4-page {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 15mm 15mm 25mm 15mm;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border-radius: 4px;
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
        .btn-print { background: #2563eb; color: #ffffff; }
        .btn-print:hover { background: #1d4ed8; }
        .btn-back { background: #e2e8f0; color: #334155; }
        .btn-back:hover { background: #cbd5e1; }

        /* Header Table */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .header-table td {
            vertical-align: top;
        }
        .company-name {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }
        .company-info {
            font-size: 10px;
            color: #475569;
            line-height: 1.5;
        }
        
        .doc-badge {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 10px 15px;
            text-align: right;
            display: inline-block;
            min-width: 220px;
        }
        .doc-type-title {
            font-size: 16px;
            font-weight: 800;
            color: #1d4ed8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .doc-number-title {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            font-family: 'JetBrains Mono', monospace;
        }

        /* Customer Box */
        .customer-card {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            background: #f8fafc;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }
        .customer-title {
            font-size: 9px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .customer-name {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background: #0f172a;
            color: #ffffff;
            font-weight: 700;
            font-size: 9.5px;
            text-transform: uppercase;
            padding: 10px 12px;
            letter-spacing: 0.5px;
        }
        .items-table th:first-child { border-radius: 6px 0 0 0; }
        .items-table th:last-child { border-radius: 0 6px 0 0; }
        .items-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 10.5px;
        }
        .items-table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Totals Section */
        .totals-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            gap: 20px;
        }
        .obs-box {
            flex: 1;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px;
            background: #f8fafc;
            font-size: 10px;
            color: #475569;
        }
        .totals-table {
            width: 240px;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 6px 0;
            font-size: 11px;
        }
        .grand-total-row {
            font-size: 14px;
            font-weight: 800;
            color: #1e40af;
            border-top: 2px solid #2563eb;
            padding-top: 8px !important;
        }

        /* AGT Footer Fixed at Bottom */
        .agt-footer {
            position: absolute;
            bottom: 12mm;
            left: 15mm;
            right: 15mm;
            border-top: 1px solid #cbd5e1;
            padding-top: 10px;
            text-align: center;
            font-size: 9.5px;
            color: #475569;
            line-height: 1.5;
            background: #ffffff;
        }
        .hash-code {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 600;
            color: #0f172a;
            letter-spacing: -0.3px;
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
            .agt-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                padding-bottom: 5mm;
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
        <div style="font-weight: 700; color: #475569;">Pré-visualização de Fatura PDF (A4)</div>
        <button onclick="window.print()" class="btn-action btn-print">
            🖨️ Imprimir / Guardar PDF
        </button>
    </div>

    <!-- Main A4 Document Sheet -->
    <div class="a4-page">
        <div>
            <!-- Header Table -->
            <table class="header-table">
                <tr>
                    <td style="width: 55%;">
                        <div class="company-name">{{ $company->name }}</div>
                        <div class="company-info">
                            <strong>NIF:</strong> {{ $company->nif ?? '5001440276' }}<br>
                            <strong>Endereço:</strong> {{ $company->address ?? 'Luanda, Angola' }}<br>
                            <strong>Tel:</strong> {{ $company->phone ?? '+244 923 000 000' }} | <strong>Email:</strong> {{ $company->email ?? 'geral@consulvolt.com' }}
                        </div>
                    </td>
                    <td style="width: 45%;" class="text-right">
                        <div class="doc-badge">
                            <div class="doc-type-title">
                                @switch($sale->doc_type)
                                    @case('FT') FATURA @break
                                    @case('FR') FATURA-RECIBO @break
                                    @case('FS') FATURA SIMPLIFICADA @break
                                    @case('NC') NOTA DE CRÉDITO @break
                                    @case('ND') NOTA DE DÉBITO @break
                                    @case('PP') PROFORMA @break
                                    @case('OR') ORÇAMENTO @break
                                    @default {{ $sale->doc_type }}
                                @endswitch
                            </div>
                            <div class="doc-number-title">{{ $sale->doc_number }}</div>
                            <div style="font-size: 10px; color: #64748b; margin-top: 4px;">
                                Data de Emissão: <strong>{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</strong><br>
                                Moeda: <strong>AOA (Kwanzas)</strong>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- Customer Details Card -->
            <div class="customer-card">
                <div>
                    <div class="customer-title">DADOS DO CLIENTE</div>
                    <div class="customer-name">{{ $sale->customer->name ?? 'Consumidor Final (Anónimo)' }}</div>
                    <div style="color: #475569; font-size: 10px; margin-top: 2px;">
                        Endereço: {{ $sale->customer->address ?? 'Luanda, Angola' }}
                    </div>
                </div>
                <div class="text-right">
                    <div class="customer-title">NIF DO CLIENTE</div>
                    <div style="font-weight: 700; font-size: 12px; font-family: 'JetBrains Mono', monospace;">
                        {{ $sale->customer->nif ?? '999999999' }}
                    </div>
                </div>
            </div>

            <!-- Table of Items -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 6%;">#</th>
                        <th style="width: 46%;">Descrição do Artigo / Serviço</th>
                        <th class="text-center" style="width: 12%;">Qtd</th>
                        <th class="text-right" style="width: 14%;">Preço Unit.</th>
                        <th class="text-center" style="width: 8%;">Taxa</th>
                        <th class="text-right" style="width: 14%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $index => $item)
                    <tr>
                        <td class="text-center" style="color: #64748b;">{{ $index + 1 }}</td>
                        <td style="font-weight: 600;">{{ $item->product->name ?? 'Artigo / Serviço' }}</td>
                        <td class="text-center" style="font-weight: 700;">{{ number_format($item->quantity, 0) }}</td>
                        <td class="text-right">{{ number_format($item->unit_price, 2, ',', '.') }} Kz</td>
                        <td class="text-center">{{ number_format($item->tax_rate ?? 14, 0) }}%</td>
                        <td class="text-right" style="font-weight: 700;">{{ number_format($item->subtotal ?? ($item->quantity * $item->unit_price), 2, ',', '.') }} Kz</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Summary & Totals -->
            <div class="totals-container">
                <div class="obs-box">
                    <strong style="color: #0f172a;">Observações / Meio de Pagamento:</strong>
                    <div style="margin-top: 4px; line-height: 1.4;">
                        {{ $sale->notes ?? 'Obrigado pela sua preferência. Documento emitido nos termos da legislação angolana.' }}
                    </div>
                </div>
                <div>
                    <table class="totals-table">
                        <tr>
                            <td class="text-muted">Incidência (Base):</td>
                            <td class="text-right fw-bold">{{ number_format($sale->total_amount - $sale->total_tax, 2, ',', '.') }} Kz</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Total IVA (14%):</td>
                            <td class="text-right fw-bold">{{ number_format($sale->total_tax, 2, ',', '.') }} Kz</td>
                        </tr>
                        @if(($sale->total_discount ?? 0) > 0)
                        <tr>
                            <td class="text-muted">Desconto:</td>
                            <td class="text-right text-danger fw-bold">-{{ number_format($sale->total_discount, 2, ',', '.') }} Kz</td>
                        </tr>
                        @endif
                        <tr class="grand-total-row">
                            <td>TOTAL GERAL:</td>
                            <td class="text-right">{{ number_format($sale->total_amount, 2, ',', '.') }} Kz</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- AGT Certification Footer (Strictly at bottom of page) -->
        <div class="agt-footer">
            <div>{{ $printMention }}</div>
            <div>Hash Assinatura: <span class="hash-code">{{ $sale->hash ? (substr($sale->hash, 0, 48) . '...') : 'k1HsJsZRhHMu5BSrks3ZByzdRR8=000000000000...' }}</span></div>
            <div style="font-weight: 700; color: #0f172a; margin-top: 2px;">Software Certificado pela AGT - ERP Consulvolt</div>
        </div>
    </div>

</body>
</html>

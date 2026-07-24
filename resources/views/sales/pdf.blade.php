<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Documento {{ $sale->doc_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; line-height: 1.5; font-size: 13px; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 20px; }
        .company-info h1 { margin: 0; font-size: 22px; color: #1e40af; }
        .company-info p { margin: 2px 0; }
        .doc-info { text-align: right; }
        .doc-info h2 { margin: 0; color: #475569; font-size: 20px; }
        .doc-info p { margin: 2px 0; }
        .customer-card { background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e2e8f0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #f1f5f9; padding: 10px; text-align: left; border-bottom: 2px solid #cbd5e1; font-size: 12px; }
        td { padding: 10px; border-bottom: 1px solid #e2e8f0; font-size: 12px; }
        .totals-section { display: flex; justify-content: space-between; margin-top: 30px; }
        .tax-summary { width: 50%; }
        .tax-summary table th, .tax-summary table td { padding: 6px; font-size: 11px; }
        .totals { width: 40%; text-align: right; }
        .totals-row { display: flex; justify-content: space-between; padding: 5px 0; }
        .totals-row.grand-total { font-weight: bold; font-size: 1.2em; border-top: 2px solid #333; margin-top: 5px; padding-top: 10px; }
        .footer { clear: both; text-align: center; margin-top: 50px; font-size: 0.85em; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 20px; }
        .watermark { position: absolute; top: 40%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 100px; color: rgba(255,0,0,0.1); z-index: -1; }
        
        @php
            $docNames = [
                'FT' => 'FATURA',
                'FR' => 'FATURA/RECIBO',
                'NC' => 'NOTA DE CRÉDITO',
                'ND' => 'NOTA DE DÉBITO',
                'GR' => 'GUIA DE REMESSA',
                'GT' => 'GUIA DE TRANSPORTE',
                'PP' => 'FATURA PRÓ-FORMA',
                'OR' => 'ORÇAMENTO',
                'EN' => 'ENCOMENDA'
            ];
            $docName = $docNames[$sale->doc_type] ?? 'DOCUMENTO COMERCIAL';
            
            // Calculate Tax Summary
            $taxSummary = [];
            foreach($sale->items as $item) {
                $taxId = $item->tax_id;
                if(!isset($taxSummary[$taxId])) {
                    $taxSummary[$taxId] = [
                        'tax_rate' => $item->tax_rate,
                        'tax_name' => $item->tax->name ?? 'IVA',
                        'exemption_reason' => $item->exemption_reason,
                        'incidencia' => 0,
                        'imposto' => 0
                    ];
                }
                $taxSummary[$taxId]['incidencia'] += $item->subtotal;
                $taxSummary[$taxId]['imposto'] += $item->tax_amount;
            }
        @endphp
    </style>
</head>
<body onload="window.print()">
    @if($sale->status === 'CANCELLED' || $sale->status === 'CANCELADO')
        <div class="watermark">ANULADO</div>
    @endif

    <div class="header">
        <div class="company-info">
            <h1>{{ $sale->company->name ?? 'Consulvolt ERP' }}</h1>
            <p>NIF: {{ $sale->company->nif ?? '5000000000' }}</p>
            <p>{{ $sale->company->address ?? 'Luanda, Angola' }}</p>
            <p>{{ $sale->company->email ?? 'geral@consulvolt.ao' }} | {{ $sale->company->phone ?? '' }}</p>
        </div>
        <div class="doc-info">
            <h2>{{ $docName }}</h2>
            <p><strong>{{ $sale->doc_number }}</strong></p>
            <p>Data: {{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</p>
            <p>Estado: {{ $sale->status }}</p>
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
                <th style="text-align: right;">Preço Unit.</th>
                <th style="text-align: right;">Desc.</th>
                <th style="text-align: right;">Taxa</th>
                <th style="text-align: right;">Total (Kz)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
            <tr>
                <td>{{ $item->product->code ?? '-' }}</td>
                <td>
                    {{ $item->product->name ?? 'Artigo Removido' }}
                    @if($item->exemption_reason)
                        <br><small style="color: #64748b;">Isenção: {{ $item->exemption_reason }}</small>
                    @endif
                </td>
                <td style="text-align: right;">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                <td style="text-align: right;">{{ number_format($item->unit_price, 2, ',', '.') }}</td>
                <td style="text-align: right;">{{ number_format($item->discount_amount, 2, ',', '.') }}</td>
                <td style="text-align: right;">{{ number_format($item->tax_rate, 2, ',', '.') }}%</td>
                <td style="text-align: right;">{{ number_format($item->subtotal + $item->tax_amount, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-section">
        <div class="tax-summary">
            <strong>Quadro Resumo de Impostos</strong>
            <table>
                <thead>
                    <tr>
                        <th>Taxa/Motivo Isenção</th>
                        <th style="text-align: right;">Incidência</th>
                        <th style="text-align: right;">Imposto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($taxSummary as $tax)
                        <tr>
                            <td>
                                {{ number_format($tax['tax_rate'], 2, ',', '.') }}% 
                                @if($tax['tax_rate'] == 0 && $tax['exemption_reason'])
                                    <br><small>{{ $tax['exemption_reason'] }}</small>
                                @endif
                            </td>
                            <td style="text-align: right;">{{ number_format($tax['incidencia'], 2, ',', '.') }}</td>
                            <td style="text-align: right;">{{ number_format($tax['imposto'], 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="totals">
            <div class="totals-row">
                <span>Total Ilíquido:</span>
                <span>{{ number_format($sale->total_amount + $sale->total_discount, 2, ',', '.') }} AOA</span>
            </div>
            <div class="totals-row">
                <span>Desconto Comercial:</span>
                <span>{{ number_format($sale->total_discount, 2, ',', '.') }} AOA</span>
            </div>
            <div class="totals-row">
                <span>Total Impostos:</span>
                <span>{{ number_format($sale->total_tax, 2, ',', '.') }} AOA</span>
            </div>
            <div class="totals-row grand-total">
                <span>Total a Pagar:</span>
                <span>{{ number_format($sale->total_amount + $sale->total_tax, 2, ',', '.') }} AOA</span>
            </div>
        </div>
    </div>

    <div class="footer">
        <p><strong>{{ $sale->hash ?? 'ABCD-Processado por programa validado nº 000/AGT/2026' }}</strong></p>
        <p>Software licenciado a: {{ $sale->company->name ?? 'Consulvolt' }}</p>
    </div>
</body>
</html>

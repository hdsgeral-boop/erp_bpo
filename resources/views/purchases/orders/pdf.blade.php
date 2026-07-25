<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Nota de Encomenda {{ $order->order_number }}</title>
    <style>
        @page { margin: 25px 35px 40px 35px; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 9.5pt; color: #1e293b; line-height: 1.4; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .company-title { font-size: 15pt; font-weight: bold; color: #0f172a; text-transform: uppercase; }
        .doc-title { font-size: 14pt; font-weight: bold; color: #2563eb; text-transform: uppercase; text-align: right; }
        .doc-number { font-size: 11.5pt; font-weight: bold; color: #0f172a; text-align: right; }
        .info-box { background-color: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 12px 16px; margin-bottom: 20px; }
        .table-items { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 20px; }
        .table-items th { background-color: #1e293b; color: #ffffff; font-size: 8pt; font-weight: bold; text-transform: uppercase; padding: 8px 10px; }
        .table-items td { font-size: 8.5pt; padding: 7px 10px; border-bottom: 1px solid #e2e8f0; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }
        .total-box { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 25px; }
        .total-card { background-color: #eff6ff; border: 2px solid #2563eb; border-radius: 6px; padding: 10px 16px; text-align: right; }
        .total-label { font-size: 9pt; font-weight: bold; color: #1e40af; text-transform: uppercase; }
        .total-amount { font-size: 15pt; font-weight: bold; color: #1e3a8a; }
        .footer { position: fixed; bottom: -20px; left: 0; right: 0; border-top: 1px solid #e2e8f0; padding-top: 6px; font-size: 8pt; color: #64748b; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                <div class="company-title">{{ $order->company->name ?? 'CONSULVOLT SOLUÇÕES - ERP' }}</div>
                <div style="font-size: 8.5pt; color: #475569;">NIF: {{ $order->company->nif ?? '5417000000' }}</div>
            </td>
            <td style="width: 45%;">
                <div class="doc-title">NOTA DE ENCOMENDA</div>
                <div class="doc-number">{{ $order->order_number }}</div>
                <div style="text-align: right; font-size: 8.5pt; color: #475569;">
                    Data: {{ $order->date ? $order->date->format('d/m/Y') : date('d/m/Y') }}<br>
                    Estado: <strong>{{ $order->status }}</strong>
                </div>
            </td>
        </tr>
    </table>

    <div class="info-box">
        <table style="width: 100%;">
            <tr>
                <td style="width: 60%;">
                    <div style="font-size: 7.5pt; font-weight: bold; color: #64748b; text-transform: uppercase;">FORNECEDOR DESTINATÁRIO</div>
                    <div style="font-size: 10pt; font-weight: bold; color: #0f172a;">{{ $order->supplier->name ?? 'Fornecedor' }}</div>
                    <div style="font-size: 8.5pt; color: #475569;">NIF: {{ $order->supplier->nif ?? 'N/D' }}</div>
                </td>
                <td style="width: 40%;">
                    <div style="font-size: 7.5pt; font-weight: bold; color: #64748b; text-transform: uppercase;">EMISSOR</div>
                    <div style="font-size: 10pt; font-weight: bold; color: #0f172a;">{{ $order->creator->name ?? 'Departamento de Compras' }}</div>
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
            @forelse($order->items as $idx => $item)
                <tr>
                    <td class="text-center" style="color: #64748b;">{{ $idx + 1 }}</td>
                    <td><strong>{{ $item->product->name ?? 'Artigo' }}</strong></td>
                    <td class="text-center font-mono">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                    <td class="text-right font-mono">{{ number_format($item->unit_price, 2, ',', '.') }} Kz</td>
                    <td class="text-right font-mono fw-bold">{{ number_format($item->quantity * $item->unit_price, 2, ',', '.') }} Kz</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-3">Sem artigos encomendados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="total-box">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%;">
                <div class="total-card">
                    <div class="total-label">TOTAL DA ENCOMENDA</div>
                    <div class="total-amount">{{ number_format($order->total_amount, 2, ',', '.') }} AKZ</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        <table style="width: 100%;">
            <tr>
                <td>Nota de Encomenda oficial a fornecedor - CONSULVOLT ERP</td>
                <td style="text-align: right;">Página 1 de 1</td>
            </tr>
        </table>
    </div>

</body>
</html>

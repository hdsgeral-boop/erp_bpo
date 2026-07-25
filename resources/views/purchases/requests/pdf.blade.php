<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Pedido Interno de Compra #REQ-{{ str_pad($purchaseRequest->id, 4, '0', STR_PAD_LEFT) }}</title>
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
        .footer { position: fixed; bottom: -20px; left: 0; right: 0; border-top: 1px solid #e2e8f0; padding-top: 6px; font-size: 8pt; color: #64748b; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                <div class="company-title">{{ $purchaseRequest->company->name ?? 'CONSULVOLT SOLUÇÕES - ERP' }}</div>
                <div style="font-size: 8.5pt; color: #475569;">NIF: {{ $purchaseRequest->company->nif ?? '5417000000' }}</div>
            </td>
            <td style="width: 45%;">
                <div class="doc-title">REQUISIÇÃO / PEDIDO INTERNO</div>
                <div class="doc-number">#REQ-{{ str_pad($purchaseRequest->id, 4, '0', STR_PAD_LEFT) }}</div>
                <div style="text-align: right; font-size: 8.5pt; color: #475569;">
                    Data: {{ $purchaseRequest->date ? $purchaseRequest->date->format('d/m/Y') : date('d/m/Y') }}<br>
                    Estado: <strong>{{ $purchaseRequest->status }}</strong>
                </div>
            </td>
        </tr>
    </table>

    <div class="info-box">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%;">
                    <div style="font-size: 7.5pt; font-weight: bold; color: #64748b; text-transform: uppercase;">REQUERENTE</div>
                    <div style="font-size: 10pt; font-weight: bold; color: #0f172a;">{{ $purchaseRequest->requester_name }}</div>
                </td>
                <td style="width: 50%;">
                    <div style="font-size: 7.5pt; font-weight: bold; color: #64748b; text-transform: uppercase;">DEPARTAMENTO SOLICITANTE</div>
                    <div style="font-size: 10pt; font-weight: bold; color: #0f172a;">{{ $purchaseRequest->department->name ?? 'Geral' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="table-items">
        <thead>
            <tr>
                <th style="width: 8%;" class="text-center">#</th>
                <th style="width: 62%;">ARTIGO / EQUIPAMENTO REQUISITADO</th>
                <th style="width: 30%;" class="text-center">QUANTIDADE NECESSÁRIA</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchaseRequest->items as $idx => $item)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><strong>{{ $item->product->name ?? 'Artigo' }}</strong></td>
                    <td class="text-center font-mono"><strong>{{ number_format($item->quantity, 2, ',', '.') }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center py-3">Sem artigos requisitados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <table style="width: 100%;">
            <tr>
                <td>Documento interno de aprovisionamento - CONSULVOLT ERP</td>
                <td style="text-align: right;">Página 1 de 1</td>
            </tr>
        </table>
    </div>

</body>
</html>

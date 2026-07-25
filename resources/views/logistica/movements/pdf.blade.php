<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Histórico de Movimentos de Stock</title>
    <style>
        @page { margin: 25px 30px 40px 30px; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 9pt; color: #1e293b; line-height: 1.4; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .company-title { font-size: 14pt; font-weight: bold; color: #0f172a; text-transform: uppercase; }
        .doc-title { font-size: 13pt; font-weight: bold; color: #2563eb; text-transform: uppercase; text-align: right; }
        .table-items { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 20px; }
        .table-items th { background-color: #1e293b; color: #ffffff; font-size: 8pt; font-weight: bold; text-transform: uppercase; padding: 7px 9px; }
        .table-items td { font-size: 8.5pt; padding: 6px 9px; border-bottom: 1px solid #e2e8f0; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }
        .badge-in { color: #15803d; font-weight: bold; }
        .badge-out { color: #b91c1c; font-weight: bold; }
        .footer { position: fixed; bottom: -20px; left: 0; right: 0; border-top: 1px solid #e2e8f0; padding-top: 6px; font-size: 8pt; color: #64748b; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                <div class="company-title">{{ $company->name ?? 'CONSULVOLT SOLUÇÕES - ERP' }}</div>
                <div style="font-size: 8.5pt; color: #475569;">NIF: {{ $company->nif ?? '5417000000' }}</div>
            </td>
            <td style="width: 45%;">
                <div class="doc-title">RELATÓRIO DE MOVIMENTOS DE STOCK</div>
                <div style="text-align: right; font-size: 8.5pt; color: #475569;">
                    Data de Emissão: {{ date('d/m/Y H:i') }}
                </div>
            </td>
        </tr>
    </table>

    <table class="table-items">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">#</th>
                <th style="width: 12%;">DATA</th>
                <th style="width: 18%;">ARMAZÉM</th>
                <th style="width: 35%;">ARTIGO / CÓDIGO</th>
                <th style="width: 12%;" class="text-center">TIPO</th>
                <th style="width: 18%;" class="text-right">QUANTIDADE</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movements as $idx => $m)
                <tr>
                    <td class="text-center" style="color: #64748b;">{{ $idx + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($m->date)->format('d/m/Y') }}</td>
                    <td>{{ $m->warehouse ? $m->warehouse->name : 'Geral' }}</td>
                    <td>
                        <strong>{{ $m->product->name ?? 'Artigo #' . $m->product_id }}</strong>
                        @if($m->product && $m->product->code)
                            <br><small style="color: #64748b;">[{{ $m->product->code }}]</small>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="{{ $m->type === 'IN' ? 'badge-in' : 'badge-out' }}">
                            {{ $m->type === 'IN' ? 'ENTRADA' : ($m->type === 'OUT' ? 'SAÍDA' : 'AJUSTE') }}
                        </span>
                    </td>
                    <td class="text-right font-mono {{ $m->type === 'IN' ? 'badge-in' : 'badge-out' }}">
                        {{ $m->type === 'IN' ? '+' : '-' }}{{ number_format($m->quantity, 2, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-3">Sem movimentos registados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <table style="width: 100%;">
            <tr>
                <td>Relatório de Logística e Gestão de Stocks - CONSULVOLT ERP</td>
                <td style="text-align: right;">Página 1 de 1</td>
            </tr>
        </table>
    </div>

</body>
</html>

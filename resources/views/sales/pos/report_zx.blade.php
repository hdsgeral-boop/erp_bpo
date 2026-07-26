<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>{{ $isReportZ ? 'Relatório Z (Fecho Fiscal de Caixa)' : 'Relatório X (Leitura Intermédia)' }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 15px;
            background: #f1f5f9;
        }
        .ticket-container {
            max-width: 380px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .header h2 {
            margin: 0 0 5px 0;
            font-size: 16px;
            text-transform: uppercase;
        }
        .header p {
            margin: 2px 0;
            font-size: 11px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }
        .section-title {
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            margin-top: 12px;
            margin-bottom: 6px;
            padding-bottom: 2px;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        .totals-table td {
            padding: 3px 0;
        }
        .totals-table td.amount {
            text-align: right;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            border-top: 2px dashed #000;
            padding-top: 10px;
            margin-top: 15px;
            font-size: 10px;
        }
        .btn-print {
            display: block;
            width: 100%;
            max-width: 380px;
            margin: 0 auto 15px auto;
            padding: 10px;
            background: #0284c7;
            color: #fff;
            text-align: center;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .ticket-container { border: none; box-shadow: none; width: 100%; max-width: 100%; padding: 0; }
            .btn-print { display: none; }
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="btn-print">🖨️ Imprimir {{ $isReportZ ? 'Relatório Z' : 'Relatório X' }}</button>

    <div class="ticket-container">
        <div class="header">
            <h2>{{ $company->name ?? 'CONSULVOLT, LDA' }}</h2>
            <p>NIF: {{ $company->nif ?? '5417370762' }}</p>
            <p>{{ $company->address ?? 'Luanda, Angola' }}</p>
            <h3 style="margin-top: 10px; margin-bottom: 0;">
                {{ $isReportZ ? 'RELATÓRIO Z (FECHO DE CAIXA)' : 'RELATÓRIO X (LEITURA INTERMÉDIA)' }}
            </h3>
        </div>

        <div class="info-row">
            <span>Terminal / Caixa:</span>
            <strong>{{ $session->posRegister->name ?? 'Caixa Principal' }}</strong>
        </div>
        <div class="info-row">
            <span>Operador:</span>
            <strong>{{ $session->user->name ?? 'Operador' }}</strong>
        </div>
        <div class="info-row">
            <span>Data de Abertura:</span>
            <span>{{ \Carbon\Carbon::parse($session->opened_at)->format('d/m/Y H:i') }}</span>
        </div>
        @if($isReportZ && $session->closed_at)
        <div class="info-row">
            <span>Data de Fecho:</span>
            <span>{{ \Carbon\Carbon::parse($session->closed_at)->format('d/m/Y H:i') }}</span>
        </div>
        @else
        <div class="info-row">
            <span>Data Emissão:</span>
            <span>{{ date('d/m/Y H:i') }}</span>
        </div>
        @endif

        <!-- Secção de Movimentos de Caixa -->
        <div class="section-title">Resumo Financeiro de Caixa</div>
        <table class="totals-table">
            <tr>
                <td>Fundo de Maneio Inicial:</td>
                <td class="amount">{{ number_format($session->opening_balance, 2) }} Kz</td>
            </tr>
            <tr>
                <td>(+) Total de Vendas (Líquido):</td>
                <td class="amount">{{ number_format($totalSales, 2) }} Kz</td>
            </tr>
            <tr>
                <td>(+) Total de Reforços de Caixa:</td>
                <td class="amount">{{ number_format($reforcos, 2) }} Kz</td>
            </tr>
            <tr>
                <td>(-) Total de Sangrias de Caixa:</td>
                <td class="amount">-{{ number_format($sangrias, 2) }} Kz</td>
            </tr>
            <tr style="border-top: 1px dashed #000;">
                <td><strong>(=) Saldo Esperado em Caixa:</strong></td>
                <td class="amount"><strong>{{ number_format($session->opening_balance + $totalSales + $reforcos - $sangrias, 2) }} Kz</strong></td>
            </tr>
            @if($isReportZ && isset($session->closing_balance))
            <tr>
                <td>Contagem Física Declarada:</td>
                <td class="amount">{{ number_format($session->closing_balance, 2) }} Kz</td>
            </tr>
            <tr>
                <td>Quebra / Sobra de Caixa:</td>
                <td class="amount" style="color: {{ $session->difference < 0 ? '#dc2626' : '#16a34a' }};">
                    {{ number_format($session->difference, 2) }} Kz
                </td>
            </tr>
            @endif
        </table>

        <!-- Secção de Resumo Fiscal de IVA -->
        <div class="section-title">Resumo de IVA Cobrado</div>
        <table class="totals-table">
            <tr>
                <td>Base Incidência (Net Total):</td>
                <td class="amount">{{ number_format($totalSales, 2) }} Kz</td>
            </tr>
            <tr>
                <td>Total de IVA Liquidado:</td>
                <td class="amount">{{ number_format($totalTax, 2) }} Kz</td>
            </tr>
            <tr style="border-top: 1px solid #000;">
                <td><strong>Total Bruto Faturado:</strong></td>
                <td class="amount"><strong>{{ number_format($totalGross, 2) }} Kz</strong></td>
            </tr>
        </table>

        <!-- Detalhe de Vendas por Documento -->
        <div class="section-title">Documentos Emitidos ({{ $sales->count() }})</div>
        <div class="info-row">
            <span>Faturas / Recibos Emitidos:</span>
            <strong>{{ $sales->count() }}</strong>
        </div>

        <div class="footer">
            <p>Processado por computador / Sistema de Gestão ERP</p>
            <p>Software Certificado / Angola AGT</p>
        </div>
    </div>

    @if(request()->has('auto_print'))
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
    @endif
</body>
</html>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Extrato de Conta - {{ $account->name }}</title>
    <style>
        @page {
            margin: 25px 30px 40px 30px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
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
            font-size: 16pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }
        .company-subtitle {
            font-size: 8.5pt;
            color: #64748b;
            margin: 0;
        }
        .document-badge {
            text-align: right;
        }
        .doc-title {
            font-size: 14pt;
            font-weight: bold;
            color: #1e40af;
            text-transform: uppercase;
            margin: 0 0 4px 0;
        }
        .doc-meta {
            font-size: 8.5pt;
            color: #475569;
        }

        /* Account & KPI Card Section */
        .info-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 20px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 4px 6px;
            vertical-align: middle;
        }
        .label {
            font-size: 7.5pt;
            text-transform: uppercase;
            font-weight: bold;
            color: #64748b;
        }
        .value {
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
        }
        .val-in { color: #16a34a; }
        .val-out { color: #dc2626; }
        .val-bal { color: #1d4ed8; }

        /* Table Movements */
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table-data th {
            background-color: #1e293b;
            color: #ffffff;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 7px 8px;
            border: 1px solid #1e293b;
        }
        .table-data td {
            font-size: 8.5pt;
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
            border-left: 1px solid #f1f5f9;
            border-right: 1px solid #f1f5f9;
        }
        .table-data tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: 'Courier New', Courier, monospace; font-size: 8.5pt; }

        .badge-doc {
            display: inline-block;
            padding: 2px 5px;
            font-size: 7.5pt;
            font-weight: bold;
            border-radius: 3px;
        }
        .badge-in { background-color: #dcfce7; color: #15803d; }
        .badge-out { background-color: #fee2e2; color: #b91c1c; }

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
            color: #94a3b8;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                <div class="company-title">{{ $company->name ?? 'CONSULVOLT SOLUÇÕES' }}</div>
                <div class="company-subtitle">NIF: {{ $company->nif ?? '5417000000' }} | {{ $company->address ?? 'Luanda, Angola' }}</div>
                <div class="company-subtitle">Email: {{ $company->email ?? 'contacto@consulvolt.co.ao' }} | Tel: {{ $company->phone ?? '+244 923 000 000' }}</div>
            </td>
            <td style="width: 45%;" class="document-badge">
                <div class="doc-title">Extrato de Tesouraria</div>
                <div class="doc-meta"><strong>Período:</strong> {{ date('d/m/Y', strtotime($startDate)) }} a {{ date('d/m/Y', strtotime($endDate)) }}</div>
                <div class="doc-meta"><strong>Emissão:</strong> {{ date('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>

    <!-- Info Account Box -->
    <div class="info-box">
        <table class="info-table">
            <tr>
                <td style="width: 30%;">
                    <div class="label">CONTA DE TESOURARIA</div>
                    <div class="value">{{ $account->name }}</div>
                    <small style="color:#64748b; font-size:7.5pt;">Moeda: {{ $account->currency }}</small>
                </td>
                <td style="width: 22%;">
                    <div class="label">SALDO INICIAL</div>
                    <div class="value">{{ number_format($account->initial_balance, 2, ',', '.') }} Kz</div>
                </td>
                <td style="width: 24%;">
                    <div class="label">ENTRADAS / SAÍDAS</div>
                    <div class="value" style="font-size: 9.5pt;">
                        <span class="val-in">+ {{ number_format($totalIn, 2, ',', '.') }}</span><br>
                        <span class="val-out">- {{ number_format($totalOut, 2, ',', '.') }}</span>
                    </div>
                </td>
                <td style="width: 24%; text-align: right;">
                    <div class="label">SALDO ATUAL DISPONÍVEL</div>
                    <div class="value val-bal" style="font-size: 13pt;">{{ number_format($account->current_balance, 2, ',', '.') }} Kz</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Table Movements -->
    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">#</th>
                <th style="width: 12%;" class="text-center">DATA</th>
                <th style="width: 18%;">DOCUMENTO / REF</th>
                <th style="width: 27%;">TERCEIRO / DESCRIÇÃO</th>
                <th style="width: 10%;" class="text-center">MÉTODO</th>
                <th style="width: 14%;" class="text-right">ENTRADA (+)</th>
                <th style="width: 14%;" class="text-right">SAÍDA (-)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($receipts as $idx => $r)
                @php
                    $isIn = in_array(strtoupper($r->doc_type), ['REC', 'RC', 'DEP']);
                    $val = (float)$r->total_amount;
                @endphp
                <tr>
                    <td class="text-center" style="color: #64748b;">{{ $idx + 1 }}</td>
                    <td class="text-center font-mono">{{ $r->date ? $r->date->format('d/m/Y') : '-' }}</td>
                    <td class="font-mono">
                        <span class="badge-doc {{ $isIn ? 'badge-in' : 'badge-out' }}">{{ $r->doc_type }}</span>
                        {{ $r->doc_number }}
                    </td>
                    <td>
                        <strong>{{ $r->thirdParty->name ?? $r->payment_reference ?? 'Movimento de Tesouraria' }}</strong>
                        @if($r->payment_reference && $r->thirdParty)
                            <div style="font-size: 7.5pt; color: #64748b;">{{ $r->payment_reference }}</div>
                        @endif
                    </td>
                    <td class="text-center font-mono" style="font-size: 7.5pt;">{{ $r->payment_method ?? 'TRANSF' }}</td>
                    <td class="text-right val-in font-mono">
                        {{ $isIn ? '+ ' . number_format($val, 2, ',', '.') : '-' }}
                    </td>
                    <td class="text-right val-out font-mono">
                        {{ !$isIn ? '- ' . number_format($val, 2, ',', '.') : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 30px; color: #64748b;">
                        Nenhum movimento financeiro registado para os critérios selecionados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <table class="footer-table">
            <tr>
                <td style="width: 70%;">ERP CONSULVOLT — Módulo de Tesouraria e Gestão Financeira | Documento gerado eletronicamente</td>
                <td style="width: 30%; text-align: right;">Página 1 de 1</td>
            </tr>
        </table>
    </div>

</body>
</html>

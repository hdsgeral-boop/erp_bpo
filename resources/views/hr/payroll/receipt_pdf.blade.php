<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Vencimento — {{ $employee->name }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        .header-table, .info-table, .items-table, .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .company-name { font-size: 18px; font-weight: bold; color: #0f172a; }
        .doc-title { font-size: 16px; font-weight: bold; text-align: right; color: #2563eb; }
        .card { border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px; background: #f8fafc; }
        .items-table th { background: #1e293b; color: #fff; padding: 6px 8px; font-size: 10px; text-transform: uppercase; }
        .items-table td { border-bottom: 1px solid #e2e8f0; padding: 6px 8px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .net-salary-box {
            background: #10b981;
            color: #fff;
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            border-radius: 6px;
            margin-top: 15px;
        }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #2563eb; color: #fff; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
            🖨️ Imprimir Recibo / PDF
        </button>
    </div>

    <!-- Cabeçalho -->
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <div class="company-name">{{ $company->name }}</div>
                <div>NIF: {{ $company->nif ?? '5001440276' }}</div>
                <div>{{ $company->address ?? 'Luanda, Angola' }}</div>
            </td>
            <td style="width: 40%;" class="text-right">
                <div class="doc-title">RECIBO DE VENCIMENTO</div>
                <div>Período: <strong>{{ sprintf('%02d/%d', $receipt->payrollRun->month ?? date('m'), $receipt->payrollRun->year ?? date('Y')) }}</strong></div>
            </td>
        </tr>
    </table>

    <!-- Ficha do Colaborador -->
    <div class="card" style="margin-bottom: 15px;">
        <table class="info-table" style="margin: 0;">
            <tr>
                <td><strong>Colaborador:</strong> {{ $employee->name }}</td>
                <td><strong>NIF:</strong> {{ $employee->nif ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Cargo:</strong> {{ $employee->position->title ?? 'Especialista' }}</td>
                <td><strong>Departamento:</strong> {{ $employee->department->name ?? 'Geral' }}</td>
            </tr>
            <tr>
                <td><strong>IBAN:</strong> {{ $employee->iban ?? 'AO06 0000 0000 0000 0000 0' }}</td>
                <td><strong>Vencimento Base:</strong> {{ number_format($receipt->base_salary, 2, ',', '.') }} Kz</td>
            </tr>
        </table>
    </div>

    <!-- Discriminação de Rubricas -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="text-align: left;">Descrição da Rubrica</th>
                <th class="text-center">Tipo</th>
                <th class="text-right">Proventos (Ganhos)</th>
                <th class="text-right">Descontos</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Salário Base Vencimento</td>
                <td class="text-center">PROVENTO</td>
                <td class="text-right">{{ number_format($receipt->base_salary, 2, ',', '.') }} Kz</td>
                <td class="text-right">-</td>
            </tr>
            @if(($receipt->other_additions ?? 0) > 0)
            <tr>
                <td>Subsídios e Outros Proventos</td>
                <td class="text-center">PROVENTO</td>
                <td class="text-right">{{ number_format($receipt->other_additions, 2, ',', '.') }} Kz</td>
                <td class="text-right">-</td>
            </tr>
            @endif
            <tr>
                <td>Segurança Social — INSS (3% Trabalhador)</td>
                <td class="text-center">DESCONTO</td>
                <td class="text-right">-</td>
                <td class="text-right">{{ number_format($receipt->inss_employee, 2, ',', '.') }} Kz</td>
            </tr>
            <tr>
                <td>Imposto sobre Rendimento do Trabalho (IRT)</td>
                <td class="text-center">DESCONTO</td>
                <td class="text-right">-</td>
                <td class="text-right">{{ number_format($receipt->irt, 2, ',', '.') }} Kz</td>
            </tr>
            @if(($receipt->other_deductions ?? 0) > 0)
            <tr>
                <td>Outras Deduções</td>
                <td class="text-center">DESCONTO</td>
                <td class="text-right">-</td>
                <td class="text-right">{{ number_format($receipt->other_deductions, 2, ',', '.') }} Kz</td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- Resumo dos Totais -->
    <table class="header-table">
        <tr>
            <td style="width: 50%;">
                <div>Contribuição INSS Empresa (8%): <strong>{{ number_format($receipt->inss_company, 2, ',', '.') }} Kz</strong></div>
                <div style="font-size: 10px; color: #64748b; margin-top: 5px;">Processado por Computador — ERP Consulvolt</div>
            </td>
            <td style="width: 50%;">
                <div class="net-salary-box">
                    LÍQUIDO A RECEBER: {{ number_format($receipt->net_total, 2, ',', '.') }} Kz
                </div>
            </td>
        </tr>
    </table>

</body>
</html>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Vencimento</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #0d6efd; padding-bottom: 10px; }
        .company-name { font-size: 20px; font-weight: bold; color: #0d6efd; }
        .receipt-title { font-size: 16px; font-weight: bold; margin-top: 5px; text-transform: uppercase; }
        .info-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .info-table td { padding: 5px; border: 1px solid #ddd; }
        .info-table .label { font-weight: bold; background-color: #f8f9fa; width: 25%; }
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .details-table th, .details-table td { border: 1px solid #000; padding: 8px; text-align: right; }
        .details-table th { background-color: #0d6efd; color: #fff; text-align: center; }
        .details-table .desc { text-align: left; }
        .totals { width: 50%; float: right; border-collapse: collapse; }
        .totals td { padding: 8px; border: 1px solid #000; text-align: right; }
        .totals .label { font-weight: bold; text-align: left; background-color: #f8f9fa; }
        .net-pay { font-size: 16px; font-weight: bold; color: #198754; }
        .footer { clear: both; margin-top: 50px; text-align: center; font-size: 10px; color: #666; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">ERP CONSULVOLT</div>
        <div class="receipt-title">Recibo de Vencimento - {{ str_pad($receipt->payrollRun->month, 2, '0', STR_PAD_LEFT) }}/{{ $receipt->payrollRun->year }}</div>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Funcionário:</td>
            <td>{{ $receipt->employee->first_name }} {{ $receipt->employee->last_name }}</td>
            <td class="label">NIF / BI:</td>
            <td>{{ $receipt->employee->nif ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Departamento:</td>
            <td>{{ $receipt->employee->department->name ?? '-' }}</td>
            <td class="label">Data de Admissão:</td>
            <td>{{ $receipt->employee->admission_date ? \Carbon\Carbon::parse($receipt->employee->admission_date)->format('d/m/Y') : '-' }}</td>
        </tr>
    </table>

    <table class="details-table">
        <thead>
            <tr>
                <th class="desc">Descrição</th>
                <th>Abonos (Kz)</th>
                <th>Descontos (Kz)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="desc">Vencimento Base</td>
                <td>{{ number_format($receipt->base_salary, 2, ',', '.') }}</td>
                <td></td>
            </tr>
            @if($receipt->other_additions > 0)
            <tr>
                <td class="desc">Outros Abonos (Horas Extra / Subsídios)</td>
                <td>{{ number_format($receipt->other_additions, 2, ',', '.') }}</td>
                <td></td>
            </tr>
            @endif
            @if($receipt->other_deductions > 0)
            <tr>
                <td class="desc">Faltas Injustificadas / Outras Deduções</td>
                <td></td>
                <td>{{ number_format($receipt->other_deductions, 2, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <td class="desc">INSS Trabalhador (Base: {{ number_format($receipt->inss_base, 2, ',', '.') }})</td>
                <td></td>
                <td>{{ number_format($receipt->inss_employee, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="desc">Imposto sobre Rendimento do Trabalho (IRT)</td>
                <td></td>
                <td>{{ number_format($receipt->irt, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="label">Total de Abonos</td>
            <td>{{ number_format($receipt->base_salary + $receipt->other_additions, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Total de Descontos</td>
            <td>{{ number_format($receipt->other_deductions + $receipt->inss_employee + $receipt->irt, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Líquido a Receber</td>
            <td class="net-pay">{{ number_format($receipt->net_total, 2, ',', '.') }} Kz</td>
        </tr>
    </table>

    <div class="footer">
        <p>Processado por Computador - ERP Consulvolt &copy; {{ date('Y') }}</p>
        <p>A entidade empregadora contribuiu com {{ number_format($receipt->inss_company, 2, ',', '.') }} Kz para a Segurança Social (INSS).</p>
    </div>
</body>
</html>

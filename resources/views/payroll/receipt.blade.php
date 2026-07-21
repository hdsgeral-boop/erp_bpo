<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Vencimento - {{ $receipt->employee->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 2rem; display: flex; justify-content: center; }
        .receipt-container { background: white; width: 100%; max-width: 800px; padding: 3rem; border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1); }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #e2e8f0; padding-bottom: 1.5rem; margin-bottom: 2rem; }
        .company-info h1 { margin: 0 0 0.5rem 0; font-size: 1.5rem; color: #0f172a; }
        .company-info p { margin: 0; color: #64748b; font-size: 0.875rem; }
        .receipt-title { text-align: right; }
        .receipt-title h2 { margin: 0 0 0.5rem 0; font-size: 1.25rem; color: #3b82f6; text-transform: uppercase; letter-spacing: 1px; }
        .receipt-title p { margin: 0; font-weight: 600; color: #475569; }
        
        .employee-card { background: #f8fafc; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; border: 1px solid #e2e8f0; }
        .info-group label { display: block; font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 600; margin-bottom: 0.25rem; }
        .info-group div { font-weight: 500; }
        
        .table-receipt { width: 100%; border-collapse: collapse; margin-bottom: 2rem; }
        .table-receipt th { background: #f1f5f9; padding: 0.75rem 1rem; text-align: left; font-size: 0.875rem; color: #475569; border-bottom: 2px solid #e2e8f0; }
        .table-receipt td { padding: 0.75rem 1rem; border-bottom: 1px solid #f1f5f9; }
        .table-receipt .amount { text-align: right; font-variant-numeric: tabular-nums; }
        .text-green { color: #16a34a; font-weight: 500; }
        .text-red { color: #dc2626; font-weight: 500; }
        
        .totals { margin-top: 2rem; border-top: 2px solid #e2e8f0; padding-top: 1.5rem; display: flex; justify-content: flex-end; }
        .totals-box { width: 300px; }
        .total-row { display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.9rem; }
        .total-row.net { font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed #cbd5e1; }
        
        .footer { margin-top: 4rem; text-align: center; color: #94a3b8; font-size: 0.875rem; }
        .signature-area { margin-top: 3rem; display: flex; justify-content: space-around; }
        .signature-box { text-align: center; width: 200px; }
        .signature-line { border-top: 1px solid #1e293b; margin-bottom: 0.5rem; height: 1px; }
        
        @media print {
            body { background: white; padding: 0; display: block; }
            .receipt-container { box-shadow: none; max-width: 100%; padding: 0; }
            .print-btn { display: none; }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div style="text-align: right; margin-bottom: 1rem;" class="print-btn">
            <button onclick="window.print()" style="background: #3b82f6; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; font-weight: 600;">Imprimir Recibo</button>
        </div>
        
        <div class="header">
            <div class="company-info">
                <h1>{{ config('app.name', 'ERP Consulvolt') }}</h1>
                <p>NIF: 500000000</p>
                <p>Luanda, Angola</p>
            </div>
            <div class="receipt-title">
                <h2>Recibo de Vencimento</h2>
                <p>Mês/Ano: {{ str_pad($receipt->payrollRun->month, 2, '0', STR_PAD_LEFT) }}/{{ $receipt->payrollRun->year }}</p>
            </div>
        </div>

        <div class="employee-card">
            <div class="info-group">
                <label>Colaborador</label>
                <div>{{ $receipt->employee->name }}</div>
            </div>
            <div class="info-group">
                <label>NIF / INSS</label>
                <div>{{ $receipt->employee->nif ?? 'N/A' }} / {{ $receipt->employee->inss ?? 'N/A' }}</div>
            </div>
            <div class="info-group">
                <label>Função / Departamento</label>
                <div>{{ $receipt->employee->position ?? 'N/A' }} / {{ $receipt->employee->department ?? 'N/A' }}</div>
            </div>
            <div class="info-group">
                <label>Banco / IBAN</label>
                <div>{{ $receipt->employee->bank_name ?? 'N/A' }} | {{ $receipt->employee->iban ?? 'N/A' }}</div>
            </div>
        </div>

        <table class="table-receipt">
            <thead>
                <tr>
                    <th>Descrição / Rubrica</th>
                    <th class="amount">Vencimentos (Kz)</th>
                    <th class="amount">Descontos (Kz)</th>
                </tr>
            </thead>
            <tbody>
                <!-- Salário Base e Subsídios (Simplificação de demonstração) -->
                <tr>
                    <td>Salário Base</td>
                    <td class="amount text-green">{{ number_format($receipt->base_salary, 2, ',', '.') }}</td>
                    <td class="amount"></td>
                </tr>
                @if($receipt->other_additions > 0)
                <tr>
                    <td>Subsídios e Outros Vencimentos</td>
                    <td class="amount text-green">{{ number_format($receipt->other_additions, 2, ',', '.') }}</td>
                    <td class="amount"></td>
                </tr>
                @endif
                
                <!-- Descontos Legais -->
                <tr>
                    <td>INSS (Trabalhador 3%)</td>
                    <td class="amount"></td>
                    <td class="amount text-red">{{ number_format($receipt->inss_employee, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>IRT (Imposto de Rendimento)</td>
                    <td class="amount"></td>
                    <td class="amount text-red">{{ number_format($receipt->irt, 2, ',', '.') }}</td>
                </tr>
                @if($receipt->other_deductions > 0)
                <tr>
                    <td>Outros Descontos</td>
                    <td class="amount"></td>
                    <td class="amount text-red">{{ number_format($receipt->other_deductions, 2, ',', '.') }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-box">
                <div class="total-row">
                    <span>Total Vencimentos:</span>
                    <span class="text-green">{{ number_format($receipt->base_salary + $receipt->other_additions, 2, ',', '.') }} Kz</span>
                </div>
                <div class="total-row">
                    <span>Total Descontos:</span>
                    <span class="text-red">{{ number_format($receipt->inss_employee + $receipt->irt + $receipt->other_deductions, 2, ',', '.') }} Kz</span>
                </div>
                <div class="total-row net">
                    <span>Líquido a Receber:</span>
                    <span>{{ number_format($receipt->net_total, 2, ',', '.') }} Kz</span>
                </div>
            </div>
        </div>

        <div class="signature-area">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div>A Entidade Empregadora</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div>O Colaborador</div>
            </div>
        </div>
        
        <div class="footer">
            <p>Processado por computador - {{ date('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>

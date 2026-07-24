<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Vencimento - {{ $employee->name }}</title>
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #1e293b;
            --accent: #2563eb;
            --success: #059669;
            --danger: #e11d48;
            --bg-page: #f1f5f9;
        }

        * {
            box-sizing: border-box;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        body {
            background-color: var(--bg-page);
            color: #334155;
            margin: 0;
            padding: 0;
            font-size: 13px;
            line-height: 1.5;
        }

        /* Top Action Bar (Screen Only) */
        .top-toolbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 14px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-print {
            background: #2563eb;
            color: #ffffff;
            border: none;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }

        .btn-print:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .btn-back {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .btn-back:hover {
            background: #e2e8f0;
            color: #1e293b;
        }

        /* Paper Canvas Container */
        .document-wrapper {
            max-width: 850px;
            margin: 30px auto;
            padding: 0 15px;
        }

        .paper-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            padding: 45px 50px;
            position: relative;
        }

        /* Watermark & Document Headers */
        .header-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            padding-bottom: 25px;
            border-bottom: 2px dashed #e2e8f0;
            margin-bottom: 25px;
        }

        .company-title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }

        .meta-text {
            color: #64748b;
            font-size: 12px;
            margin-bottom: 2px;
        }

        .doc-badge {
            text-align: right;
        }

        .doc-badge-title {
            font-size: 18px;
            font-weight: 800;
            color: #2563eb;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .period-pill {
            display: inline-block;
            background: #eff6ff;
            color: #1d4ed8;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 12px;
            border: 1px solid #bfdbfe;
        }

        /* Employee Info Box */
        .employee-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px 30px;
        }

        .info-group {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .info-value {
            font-size: 13px;
            font-weight: 600;
            color: #0f172a;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 30px;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .items-table th {
            background: #0f172a;
            color: #ffffff;
            padding: 12px 16px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .items-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
            background: #ffffff;
            font-size: 13px;
        }

        .items-table tr:nth-child(even) td {
            background: #f8fafc;
        }

        .items-table tr:last-child td {
            border-bottom: none;
        }

        .badge-type {
            font-size: 10px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            text-transform: uppercase;
        }

        .badge-provento {
            background: #d1fae5;
            color: #047857;
        }

        .badge-desconto {
            background: #fee2e2;
            color: #be123c;
        }

        /* Summary & Net Salary Banner */
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            align-items: end;
            margin-bottom: 35px;
        }

        .net-pay-banner {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: #ffffff;
            padding: 20px 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 10px 20px -5px rgba(5, 150, 105, 0.3);
        }

        .net-pay-label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.9;
            margin-bottom: 4px;
        }

        .net-pay-amount {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .company-tax-info {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 15px 20px;
            border-radius: 10px;
            font-size: 12px;
            color: #475569;
        }

        /* Signatures Footer */
        .signatures-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            margin-top: 40px;
            padding-top: 25px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #94a3b8;
            margin-top: 40px;
            padding-top: 8px;
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
        }

        /* Print Media Styles */
        @media print {
            .top-toolbar {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .document-wrapper {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .paper-card {
                box-shadow: none !important;
                border: none !important;
                padding: 20px !important;
                border-radius: 0 !important;
            }
        }
    </style>
</head>
<body>

    <!-- Top Action Bar (Preview Mode) -->
    <div class="top-toolbar">
        <div style="display: flex; align-items: center; gap: 15px;">
            <a href="javascript:history.back()" class="btn-action btn-back">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <span style="font-weight: 700; color: #0f172a; font-size: 15px;">
                <i class="fas fa-receipt text-primary me-2"></i> Pré-visualização do Recibo de Vencimento
            </span>
        </div>
        <div>
            <button onclick="window.print()" class="btn-action btn-print">
                <i class="fas fa-print"></i> Imprimir Recibo / Guardar PDF
            </button>
        </div>
    </div>

    <!-- Document Container -->
    <div class="document-wrapper">
        <div class="paper-card">
            
            <!-- Header Grid -->
            <div class="header-grid">
                <div>
                    <div class="company-title">{{ $company->name ?? 'Empresa' }}</div>
                    <div class="meta-text"><i class="fas fa-id-card text-muted me-1"></i> NIF: <strong>{{ $company->nif ?? '5001440276' }}</strong></div>
                    <div class="meta-text"><i class="fas fa-map-marker-alt text-muted me-1"></i> {{ $company->address ?? 'Luanda, Angola' }}</div>
                </div>
                <div class="doc-badge">
                    <div class="doc-badge-title">Recibo de Vencimento</div>
                    <div style="margin-bottom: 6px;">
                        <span class="period-pill">
                            <i class="fas fa-calendar-alt me-1"></i> Período: {{ sprintf('%02d/%d', $receipt->payrollRun->month ?? date('m'), $receipt->payrollRun->year ?? date('Y')) }}
                        </span>
                    </div>
                    <div class="meta-text">Refª: <strong>VENC-{{ $receipt->payrollRun->reference ?? date('m-Y') }}-E{{ $employee->id }}</strong></div>
                </div>
            </div>

            <!-- Employee Card -->
            <div class="employee-card">
                <div class="info-group">
                    <span class="info-label">Colaborador / Funcionário</span>
                    <span class="info-value text-primary fw-extrabold">{{ $employee->name }}</span>
                </div>
                <div class="info-group">
                    <span class="info-label">NIF do Trabalhador</span>
                    <span class="info-value">{{ $employee->nif ?? 'N/A' }}</span>
                </div>
                <div class="info-group">
                    <span class="info-label">Cargo / Função</span>
                    <span class="info-value">{{ $employee->position->title ?? 'Especialista' }}</span>
                </div>
                <div class="info-group">
                    <span class="info-label">Departamento</span>
                    <span class="info-value">{{ $employee->department->name ?? 'Geral' }}</span>
                </div>
                <div class="info-group">
                    <span class="info-label">Coordenadas Bancárias (IBAN)</span>
                    <span class="info-value font-monospace">{{ $employee->iban ?? 'AO06 0000 0000 0000 0000 0' }}</span>
                </div>
                <div class="info-group">
                    <span class="info-label">Vencimento Base Contratual</span>
                    <span class="info-value text-dark fw-bold">{{ number_format($receipt->base_salary, 2, ',', '.') }} Kz</span>
                </div>
            </div>

            <!-- Items Table -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="text-align: left;">Descrição da Rubrica Salarial</th>
                        <th style="text-align: center; width: 120px;">Tipo</th>
                        <th style="text-align: right; width: 160px;">Proventos (Ganhos)</th>
                        <th style="text-align: right; width: 160px;">Descontos</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="font-weight: 600; color: #0f172a;">Salário Base Vencimento</td>
                        <td style="text-align: center;"><span class="badge-type badge-provento">Provento</span></td>
                        <td style="text-align: right; font-weight: 700; color: #059669;">{{ number_format($receipt->base_salary, 2, ',', '.') }} Kz</td>
                        <td style="text-align: right; color: #94a3b8;">-</td>
                    </tr>
                    @if(($receipt->other_additions ?? 0) > 0)
                    <tr>
                        <td style="font-weight: 600; color: #0f172a;">Subsídios e Outros Proventos Isentos/Tributáveis</td>
                        <td style="text-align: center;"><span class="badge-type badge-provento">Provento</span></td>
                        <td style="text-align: right; font-weight: 700; color: #059669;">{{ number_format($receipt->other_additions, 2, ',', '.') }} Kz</td>
                        <td style="text-align: right; color: #94a3b8;">-</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="font-weight: 600; color: #0f172a;">Segurança Social - INSS (3% Conta do Trabalhador)</td>
                        <td style="text-align: center;"><span class="badge-type badge-desconto">Desconto</span></td>
                        <td style="text-align: right; color: #94a3b8;">-</td>
                        <td style="text-align: right; font-weight: 700; color: #be123c;">{{ number_format($receipt->inss_employee, 2, ',', '.') }} Kz</td>
                    </tr>
                    <tr>
                        <td style="font-weight: 600; color: #0f172a;">Imposto sobre o Rendimento do Trabalho (IRT 2026)</td>
                        <td style="text-align: center;"><span class="badge-type badge-desconto">Desconto</span></td>
                        <td style="text-align: right; color: #94a3b8;">-</td>
                        <td style="text-align: right; font-weight: 700; color: #be123c;">{{ number_format($receipt->irt, 2, ',', '.') }} Kz</td>
                    </tr>
                    @if(($receipt->other_deductions ?? 0) > 0)
                    <tr>
                        <td style="font-weight: 600; color: #0f172a;">Outras Deduções e Descontos Diversos</td>
                        <td style="text-align: center;"><span class="badge-type badge-desconto">Desconto</span></td>
                        <td style="text-align: right; color: #94a3b8;">-</td>
                        <td style="text-align: right; font-weight: 700; color: #be123c;">{{ number_format($receipt->other_deductions, 2, ',', '.') }} Kz</td>
                    </tr>
                    @endif
                </tbody>
            </table>

            <!-- Summary Grid & Banner -->
            <div class="summary-grid">
                <div class="company-tax-info">
                    <div style="margin-bottom: 4px;"><strong>Encargo Patronal INSS Empresa (8%):</strong> {{ number_format($receipt->inss_company, 2, ',', '.') }} Kz</div>
                    <div style="font-size: 11px; color: #64748b;"><i class="fas fa-lock me-1"></i> Documento Processado por Computador — ERP Consulvolt</div>
                </div>
                <div class="net-pay-banner">
                    <div class="net-pay-label">Líquido a Receber</div>
                    <div class="net-pay-amount">{{ number_format($receipt->net_total, 2, ',', '.') }} Kz</div>
                </div>
            </div>

            <!-- Signatures Section -->
            <div class="signatures-grid">
                <div>
                    <div class="signature-line">Assinatura do Colaborador</div>
                </div>
                <div>
                    <div class="signature-line">Carimbo & Assinatura da Empresa</div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>

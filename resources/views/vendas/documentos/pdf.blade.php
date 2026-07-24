<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Fatura Comercial AGT - {{ $sale->doc_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 12px; color: #1e293b; line-height: 1.5; padding: 20px; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #dc2626; padding-bottom: 15px; margin-bottom: 20px; }
        .company-title { font-size: 18px; font-weight: bold; color: #0f172a; }
        .doc-title { font-size: 20px; font-weight: bold; color: #dc2626; text-align: right; }
        .details-grid { display: flex; justify-content: space-between; margin-bottom: 25px; }
        .box { background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px; border-radius: 8px; width: 48%; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .table th { background: #0f172a; color: #ffffff; padding: 8px; text-align: left; font-size: 11px; text-transform: uppercase; }
        .table td { padding: 8px; border-bottom: 1px solid #e2e8f0; }
        .totals { float: right; width: 40%; margin-bottom: 30px; }
        .totals-row { display: flex; justify-content: space-between; padding: 5px 0; }
        .totals-row.grand { font-size: 14px; font-weight: bold; border-top: 2px solid #0f172a; padding-top: 8px; }
        .agt-footer { margin-top: 50px; padding-top: 15px; border-top: 1px solid #cbd5e1; font-size: 10px; color: #64748b; text-align: center; }
        .hash-code { font-family: monospace; font-weight: bold; background: #e2e8f0; padding: 4px 8px; border-radius: 4px; display: inline-block; margin-top: 5px; }
    </style>
</head>
<body>

    <div class="header">
        <div>
            <div class="company-title">ERP CONSULVOLT ANGOLA</div>
            <div>Consulvolt - Prestação de Serviços, Lda</div>
            <div>Luanda, Angola &bull; NIF: 5417000000</div>
            <div>Email: comercial@consulvolt.com</div>
        </div>
        <div>
            <div class="doc-title">FATURA / RECIBO</div>
            <div style="font-weight: bold;">N.º {{ $sale->doc_number }}</div>
            <div>Data de Emissão: {{ date('d/m/Y', strtotime($sale->date)) }}</div>
            <div>Moeda: AOA (Kwanza)</div>
        </div>
    </div>

    <div class="details-grid">
        <div class="box">
            <strong>DADOS DO CLIENTE:</strong><br>
            <strong>Empresa:</strong> {{ $company->name }}<br>
            <strong>NIF:</strong> {{ $company->nif ?? '999999999' }}<br>
            <strong>Endereço:</strong> {{ $company->province ?? 'Luanda' }}, {{ $company->municipality ?? 'Angola' }}
        </div>
        <div class="box">
            <strong>CONDIÇÕES DE PAGAMENTO:</strong><br>
            <strong>Método:</strong> Pronto Pagamento / Transferência<br>
            <strong>Estado:</strong> Liquidado / PAGO<br>
            <strong>Software Certificado AGT:</strong> n.º 142/AGT/2019
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Descrição do Serviço / Licença SaaS</th>
                <th style="text-align: center;">Qtd</th>
                <th style="text-align: right;">Preço Unit.</th>
                <th style="text-align: right;">Taxa IVA</th>
                <th style="text-align: right;">Total Líquido</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Subscrição SaaS ERP Consulvolt (30 Dias)</strong><br><small style="color: #64748b;">Licenciamento de software de gestão empresarial em nuvem.</small></td>
                <td style="text-align: center;">1</td>
                <td style="text-align: right;">{{ number_format($sale->total_amount, 2, ',', '.') }} Kz</td>
                <td style="text-align: right;">14%</td>
                <td style="text-align: right;">{{ number_format($sale->total_amount, 2, ',', '.') }} Kz</td>
            </tr>
        </tbody>
    </table>

    <div class="totals">
        <div class="totals-row">
            <span>Incidência IVA (14%):</span>
            <span>{{ number_format($sale->total_amount, 2, ',', '.') }} Kz</span>
        </div>
        <div class="totals-row">
            <span>Total Imposto (IVA):</span>
            <span>{{ number_format($sale->total_tax, 2, ',', '.') }} Kz</span>
        </div>
        <div class="totals-row grand">
            <span>Total da Fatura:</span>
            <span>{{ number_format($sale->amount_paid, 2, ',', '.') }} Kz</span>
        </div>
    </div>

    <div style="clear: both;"></div>

    <div class="agt-footer">
        <div>Processado por Programa Certificado n.º 142/AGT/2019 - ERP Consulvolt</div>
        <div class="hash-code">{{ $sale->hash ?? '4Y81-b-WSTB-2026-FT' }} - Hash Assinatura Digital RSA</div>
        <div style="margin-top: 5px;">Os bens/serviços foram colocados à disposição do adquirente na data do documento.</div>
    </div>

</body>
</html>

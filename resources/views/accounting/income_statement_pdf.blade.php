<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Demonstração de Resultados - {{ $data['year'] }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; color: #2c3e50; }
        .header p { margin: 5px 0; color: #7f8c8d; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .amount { text-align: right; }
        .total-row { font-weight: bold; background-color: #ecf0f1; }
        .title { font-size: 14px; font-weight: bold; margin-top: 20px; margin-bottom: 10px; border-bottom: 2px solid #2c3e50; padding-bottom: 5px; }
        .level-1 { font-weight: bold; }
        .level-2 { padding-left: 20px; }
        .level-3 { padding-left: 40px; color: #555; }
        .highlight { font-size: 14px; font-weight: bold; text-align: right; margin-top: 20px; padding: 10px; background-color: #f1f8ff; border: 1px solid #cce5ff; }
    </style>
</head>
<body>

<div class="header">
    <h1>{{ $company->name ?? 'Empresa Demonstração' }}</h1>
    <p>NIF: {{ $company->nif ?? 'N/A' }} | Email: {{ $company->email ?? 'N/A' }}</p>
    <h2>DEMONSTRAÇÃO DE RESULTADOS POR NATUREZA</h2>
    <p>Exercício: {{ $data['year'] }}</p>
</div>

<div class="title">RENDIMENTOS E GANHOS (Classe 7)</div>
<table>
    <thead>
        <tr>
            <th>Conta</th>
            <th>Descrição</th>
            <th class="amount">Valor (AOA)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['rendimentos'] as $acc)
        <tr>
            <td class="{{ strlen($acc['code']) == 1 ? 'level-1' : (strlen($acc['code']) == 2 ? 'level-2' : 'level-3') }}">{{ $acc['code'] }}</td>
            <td class="{{ strlen($acc['code']) == 1 ? 'level-1' : (strlen($acc['code']) == 2 ? 'level-2' : 'level-3') }}">{{ $acc['name'] ?? $acc['description'] ?? '' }}</td>
            <td class="amount {{ strlen($acc['code']) == 1 ? 'level-1' : '' }}">{{ number_format($acc['balance'], 2, ',', '.') }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="2" style="text-align: right;">TOTAL DE RENDIMENTOS:</td>
            <td class="amount">{{ number_format($data['total_rendimentos'], 2, ',', '.') }}</td>
        </tr>
    </tbody>
</table>

<div class="title">GASTOS E PERDAS (Classe 6)</div>
<table>
    <thead>
        <tr>
            <th>Conta</th>
            <th>Descrição</th>
            <th class="amount">Valor (AOA)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['gastos'] as $acc)
        <tr>
            <td class="{{ strlen($acc['code']) == 1 ? 'level-1' : (strlen($acc['code']) == 2 ? 'level-2' : 'level-3') }}">{{ $acc['code'] }}</td>
            <td class="{{ strlen($acc['code']) == 1 ? 'level-1' : (strlen($acc['code']) == 2 ? 'level-2' : 'level-3') }}">{{ $acc['name'] ?? $acc['description'] ?? '' }}</td>
            <td class="amount {{ strlen($acc['code']) == 1 ? 'level-1' : '' }}">{{ number_format($acc['balance'], 2, ',', '.') }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="2" style="text-align: right;">TOTAL DE GASTOS:</td>
            <td class="amount">{{ number_format($data['total_gastos'], 2, ',', '.') }}</td>
        </tr>
    </tbody>
</table>

<div class="highlight">
    RESULTADO LÍQUIDO DO EXERCÍCIO: <span style="color: {{ $data['resultado_liquido'] >= 0 ? 'green' : 'red' }};">{{ number_format($data['resultado_liquido'], 2, ',', '.') }} AOA</span>
</div>

<div style="margin-top: 50px; text-align: center;">
    <p>_____________________________________</p>
    <p>A Gerência / Contabilista</p>
</div>

</body>
</html>

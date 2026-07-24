@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <div class="d-flex justify-content-between align-items-center mb-4" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="margin: 0; font-weight: 700; color: #0f172a; font-size: 1.5rem;">
                <i class="fas fa-file-invoice-dollar text-primary me-2"></i> Extratos de Contas Bancárias
            </h2>
            <p style="margin: 0; color: #64748b; font-size: 0.9rem;">
                Movimentos de entrada e saída por conta de tesouraria.
            </p>
        </div>
    </div>

    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #e2e8f0; background: #f8fafc;">
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Data</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Conta Bancária</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Descrição do Movimento</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Debito / Crédito</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Saldo Resultante</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 1rem; color: #64748b;">{{ date('d/m/Y') }}</td>
                    <td style="padding: 1rem; font-weight: 700; color: #0f172a;">BAI — Conta Corrente Kz</td>
                    <td style="padding: 1rem; font-weight: 600; color: #16a34a;">Recebimento Fatura FT 2026/001</td>
                    <td style="padding: 1rem; font-weight: 700; color: #16a34a;">+ 250.000,00 Kz</td>
                    <td style="padding: 1rem; font-weight: 700; color: #0f172a;">14.250.000,00 Kz</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <div class="d-flex justify-content-between align-items-center mb-4" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="margin: 0; font-weight: 700; color: #0f172a; font-size: 1.5rem;">
                <i class="fas fa-layer-group text-primary me-2"></i> Tabela Oficial de Escalões IRT (Angola)
            </h2>
            <p style="margin: 0; color: #64748b; font-size: 0.9rem;">
                Imposto sobre o Rendimento do Trabalho — Código do IRT Geral.
            </p>
        </div>
    </div>

    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #e2e8f0; background: #f8fafc;">
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Escalão</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Rendimento Colectável (Kz)</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Parcela Fixa</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Taxa Marginal</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Excesso De</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 1rem; font-weight: 700; color: #2563eb;">1.º Escalão</td>
                    <td style="padding: 1rem; font-weight: 600; color: #16a34a;">Até 100.000,00 Kz</td>
                    <td style="padding: 1rem; color: #64748b;">0,00 Kz</td>
                    <td style="padding: 1rem; font-weight: 700; color: #16a34a;">ISENTO (0%)</td>
                    <td style="padding: 1rem; color: #64748b;">0,00 Kz</td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 1rem; font-weight: 700; color: #2563eb;">2.º Escalão</td>
                    <td style="padding: 1rem; font-weight: 600; color: #0f172a;">De 100.001,00 a 150.000,00 Kz</td>
                    <td style="padding: 1rem; color: #64748b;">0,00 Kz</td>
                    <td style="padding: 1rem; font-weight: 700; color: #2563eb;">13%</td>
                    <td style="padding: 1rem; color: #64748b;">100.000,00 Kz</td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 1rem; font-weight: 700; color: #2563eb;">3.º Escalão</td>
                    <td style="padding: 1rem; font-weight: 600; color: #0f172a;">De 150.001,00 a 200.000,00 Kz</td>
                    <td style="padding: 1rem; color: #64748b;">6.500,00 Kz</td>
                    <td style="padding: 1rem; font-weight: 700; color: #2563eb;">16%</td>
                    <td style="padding: 1rem; color: #64748b;">150.000,00 Kz</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

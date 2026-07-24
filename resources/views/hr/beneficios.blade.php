@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <div class="d-flex justify-content-between align-items-center mb-4" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="margin: 0; font-weight: 700; color: #0f172a; font-size: 1.5rem;">
                <i class="fas fa-gift text-primary me-2"></i> Benefícios e Deduções de Processamento
            </h2>
            <p style="margin: 0; color: #64748b; font-size: 0.9rem;">
                Subsídios de alimentação, transporte, residência e descontos diversos.
            </p>
        </div>
        <div>
            <button class="btn btn-primary" style="padding: 0.6rem 1.2rem; background: #2563eb; color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                <i class="fas fa-plus me-1"></i> Adicionar Benefício/Dedução
            </button>
        </div>
    </div>

    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #e2e8f0; background: #f8fafc;">
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Designação</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Tipo</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Incidência IRT</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Incidência INSS</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Valor Padrão</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 1rem; font-weight: 700; color: #0f172a;">Subsídio de Alimentação (Isento até limite)</td>
                    <td style="padding: 1rem; font-weight: 600; color: #16a34a;">Provento / Subsídio</td>
                    <td style="padding: 1rem;"><span style="background: #dcfce7; color: #166534; padding: 0.25rem 0.6rem; border-radius: 4px; font-weight: 600; font-size: 0.75rem;">Isento até 30.000 Kz</span></td>
                    <td style="padding: 1rem;"><span style="background: #dcfce7; color: #166534; padding: 0.25rem 0.6rem; border-radius: 4px; font-weight: 600; font-size: 0.75rem;">Isento</span></td>
                    <td style="padding: 1rem; font-weight: 700;">30.000,00 Kz</td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 1rem; font-weight: 700; color: #0f172a;">Subsídio de Transporte (Isento até limite)</td>
                    <td style="padding: 1rem; font-weight: 600; color: #16a34a;">Provento / Subsídio</td>
                    <td style="padding: 1rem;"><span style="background: #dcfce7; color: #166534; padding: 0.25rem 0.6rem; border-radius: 4px; font-weight: 600; font-size: 0.75rem;">Isento até 30.000 Kz</span></td>
                    <td style="padding: 1rem;"><span style="background: #dcfce7; color: #166534; padding: 0.25rem 0.6rem; border-radius: 4px; font-weight: 600; font-size: 0.75rem;">Isento</span></td>
                    <td style="padding: 1rem; font-weight: 700;">30.000,00 Kz</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

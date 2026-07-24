@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <div class="d-flex justify-content-between align-items-center mb-4" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="margin: 0; font-weight: 700; color: #0f172a; font-size: 1.5rem;">
                <i class="fas fa-plane-departure text-primary me-2"></i> Gestão de Férias e Ausências
            </h2>
            <p style="margin: 0; color: #64748b; font-size: 0.9rem;">
                Mapa de férias anuais, dispensas e ausências justificadas.
            </p>
        </div>
        <div>
            <button class="btn btn-primary" style="padding: 0.6rem 1.2rem; background: #2563eb; color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                <i class="fas fa-calendar-plus me-1"></i> Pedido de Férias / Ausência
            </button>
        </div>
    </div>

    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #e2e8f0; background: #f8fafc;">
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Colaborador</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Tipo de Ausência</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Início</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Fim</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Dias</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Estado</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 1rem; font-weight: 700; color: #0f172a;">Eng. Pascoal Paulo</td>
                    <td style="padding: 1rem; font-weight: 600; color: #2563eb;">Férias Anuais Regulamentares</td>
                    <td style="padding: 1rem; color: #64748b;">01/08/2026</td>
                    <td style="padding: 1rem; color: #64748b;">22/08/2026</td>
                    <td style="padding: 1rem; font-weight: 700;">22 Dias</td>
                    <td style="padding: 1rem;"><span style="background: #dcfce7; color: #166534; padding: 0.25rem 0.6rem; border-radius: 4px; font-weight: 600; font-size: 0.75rem;">APROVADO</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

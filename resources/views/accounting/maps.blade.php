@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <div class="d-flex justify-content-between align-items-center mb-4" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="margin: 0; font-weight: 700; color: #0f172a; font-size: 1.5rem;">
                <i class="fas fa-project-diagram text-primary me-2"></i> Mapeamentos Contabilísticos PGC
            </h2>
            <p style="margin: 0; color: #64748b; font-size: 0.9rem;">
                Integração de contas de faturação, inventário e tesouraria com o PGC Angola.
            </p>
        </div>
    </div>

    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #e2e8f0; background: #f8fafc;">
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Módulo de Origem</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Operação</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Conta Débito PGC</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Conta Crédito PGC</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 1rem; font-weight: 700; color: #0f172a;">Vendas a Pronto Pagamento</td>
                    <td style="padding: 1rem; color: #64748b;">Faturação POS</td>
                    <td style="padding: 1rem; font-weight: 700; color: #2563eb;">45.1.1 — Caixas Gerais</td>
                    <td style="padding: 1rem; font-weight: 700; color: #16a34a;">61.1.1 — Venda de Mercadorias</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

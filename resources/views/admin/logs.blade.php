@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="margin: 0; font-weight: 700; color: #0f172a; font-size: 1.5rem;">
                <i class="fas fa-history text-primary me-2"></i> Logs de Auditoria do Sistema
            </h2>
            <p style="margin: 0; color: #64748b; font-size: 0.9rem;">
                Rastreabilidade de ações, alterações em faturas, logins e operações críticas.
            </p>
        </div>
    </div>

    <!-- Table Card -->
    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #e2e8f0; background: #f8fafc;">
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Data & Hora</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Utilizador</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Ação Realizada</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Módulo / Entidade</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Endereço IP</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 1rem; color: #64748b;">{{ date('d/m/Y H:i:s') }}</td>
                    <td style="padding: 1rem; font-weight: 700; color: #0f172a;">Administrador Principal</td>
                    <td style="padding: 1rem; font-weight: 600; color: #16a34a;">Emissão de Fatura Assinada AGT (FT 2026/001)</td>
                    <td style="padding: 1rem; color: #64748b;">Vendas & AGT Engine</td>
                    <td style="padding: 1rem; font-family: monospace; font-size: 0.85rem; color: #475569;">127.0.0.1</td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 1rem; color: #64748b;">{{ date('d/m/Y H:i:s', strtotime('-15 mins')) }}</td>
                    <td style="padding: 1rem; font-weight: 700; color: #0f172a;">Administrador Principal</td>
                    <td style="padding: 1rem; font-weight: 600; color: #2563eb;">Autenticação Web de Utilizador (Login)</td>
                    <td style="padding: 1rem; color: #64748b;">Módulo Auth</td>
                    <td style="padding: 1rem; font-family: monospace; font-size: 0.85rem; color: #475569;">127.0.0.1</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

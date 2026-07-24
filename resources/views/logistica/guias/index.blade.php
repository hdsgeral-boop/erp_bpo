@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #eff6ff, #dbeafe); border-radius: 12px; display: flex; align-items: center; justify-content: justify; justify-content: center;">
                <i class="fas fa-truck-moving" style="font-size: 1.5rem; color: #2563eb;"></i>
            </div>
            <div>
                <h2 style="margin: 0; font-weight: 700; color: #0f172a; font-size: 1.5rem;">Guias de Remessa e Transporte (GT/GR)</h2>
                <p style="margin: 0; color: #64748b; font-size: 0.9rem;">Gestão e emissão de guias de transporte e remessa em conformidade com a AGT.</p>
            </div>
        </div>
        <div>
            <a href="{{ route('vendas.documentos.create', 'guias') }}" class="btn btn-primary" style="padding: 0.6rem 1.25rem; background: #2563eb; color: #fff; border: none; border-radius: 10px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-plus-circle"></i> Emitir Nova Guia
            </a>
        </div>
    </div>

    <!-- Quick Cards Stats -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <span style="color: #64748b; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Total Guias de Remessa</span>
            <h3 style="margin: 0.5rem 0 0; font-weight: 700; color: #0f172a;">12 <span style="font-size: 0.85rem; color: #94a3b8;">Documentos</span></h3>
        </div>
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <span style="color: #64748b; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Guias de Transporte Válidas</span>
            <h3 style="margin: 0.5rem 0 0; font-weight: 700; color: #16a34a;">12 <span style="font-size: 0.85rem; color: #94a3b8;">Assinadas</span></h3>
        </div>
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <span style="color: #64748b; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Ponto de Carga / Origem</span>
            <h3 style="margin: 0.5rem 0 0; font-weight: 700; color: #2563eb; font-size: 1.1rem;">Armazém Central Luanda</h3>
        </div>
    </div>

    <!-- Table Container -->
    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); padding: 1.5rem;">
        <table class="table align-middle mb-0" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 1rem; font-size: 0.8rem; font-weight: 700; color: #475569; text-transform: uppercase;">N.º Guia</th>
                    <th style="padding: 1rem; font-size: 0.8rem; font-weight: 700; color: #475569; text-transform: uppercase;">Destinatário / Cliente</th>
                    <th style="padding: 1rem; font-size: 0.8rem; font-weight: 700; color: #475569; text-transform: uppercase;">Local de Carga ➔ Descarga</th>
                    <th style="padding: 1rem; font-size: 0.8rem; font-weight: 700; color: #475569; text-transform: uppercase;">Data Transporte</th>
                    <th style="padding: 1rem; font-size: 0.8rem; font-weight: 700; color: #475569; text-transform: uppercase;">Estado AGT</th>
                    <th style="padding: 1rem; font-size: 0.8rem; font-weight: 700; color: #475569; text-transform: uppercase; text-align: right;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 1rem; font-weight: 700; color: #2563eb;">GT 2026/001</td>
                    <td style="padding: 1rem; font-weight: 700; color: #0f172a;">Cliente Consumidor Final</td>
                    <td style="padding: 1rem; color: #64748b; font-size: 0.85rem;"><i class="fas fa-warehouse text-primary me-1"></i> Luanda ➔ <i class="fas fa-map-marker-alt text-danger me-1"></i> Viana</td>
                    <td style="padding: 1rem; color: #64748b;">{{ date('d/m/Y') }}</td>
                    <td style="padding: 1rem;">
                        <span style="background: #dcfce7; color: #166534; padding: 0.25rem 0.6rem; border-radius: 6px; font-weight: 700; font-size: 0.75rem;">
                            <i class="fas fa-check me-1"></i> VÁLIDA AGT
                        </span>
                    </td>
                    <td style="padding: 1rem; text-align: right;">
                        <a href="{{ route('vendas.documentos.index', 'guias') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 6px; padding: 0.35rem 0.75rem; font-weight: 600; text-decoration: none;">
                            <i class="fas fa-print me-1"></i> Imprimir Guia
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

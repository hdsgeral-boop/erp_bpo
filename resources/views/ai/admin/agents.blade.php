@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="margin: 0; font-weight: 800; color: #0f172a; font-size: 1.6rem; letter-spacing: -0.5px;">
                <i class="fas fa-robot text-primary me-2"></i> Agentes de IA Configurados
            </h2>
            <p style="margin: 0; color: #64748b; font-size: 0.925rem;">
                Gestão de prompts, papéis e permissões de agentes inteligentes do ERP Consulvolt.
            </p>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 16px; background: #fff;">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light fs-8 text-uppercase">
                        <tr>
                            <th>Agente</th>
                            <th>Modelo Padrão</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark fs-7"><i class="fas fa-brain text-primary me-2"></i> Assistente Executivo ERP</div>
                                <span class="text-muted fs-8">Análise de Vendas, RH e Balancetes</span>
                            </td>
                            <td><span class="badge bg-light text-dark border">Gemini 1.5 Pro</span></td>
                            <td><span class="badge bg-success-subtle text-success fw-bold">Ativo</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary fw-bold" style="border-radius: 8px;">Configurar</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

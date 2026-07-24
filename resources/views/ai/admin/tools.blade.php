@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="margin: 0; font-weight: 800; color: #0f172a; font-size: 1.6rem; letter-spacing: -0.5px;">
                <i class="fas fa-tools text-primary me-2"></i> Ferramentas e Function Calling
            </h2>
            <p style="margin: 0; color: #64748b; font-size: 0.925rem;">
                Ferramentas de integração que a IA pode invocar (consultas de stock, relatórios, salários).
            </p>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
        <div class="card-body p-4">
            <div class="list-group list-group-flush">
                <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                    <div>
                        <h6 class="fw-bold mb-1">get_stock_level</h6>
                        <p class="text-muted fs-8 mb-0">Consulta o nível de stock de um determinado produto ou armazém.</p>
                    </div>
                    <span class="badge bg-primary-subtle text-primary">Ativo</span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                    <div>
                        <h6 class="fw-bold mb-1">get_monthly_sales_summary</h6>
                        <p class="text-muted fs-8 mb-0">Retorna o resumo mensal de vendas e faturação por empresa.</p>
                    </div>
                    <span class="badge bg-primary-subtle text-primary">Ativo</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

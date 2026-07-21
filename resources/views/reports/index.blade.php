@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h2 class="view-title mb-0"><i class="fas fa-chart-pie text-info"></i> Relatórios Globais</h2>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Central de extração de Mapas Fiscais, Balancetes e Relatórios de Gestão.</p>
        </div>
    </div>

    <div class="row">
        <!-- Mapa de Faturação -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-primary"><i class="fas fa-file-invoice-dollar"></i> Mapa de Faturação</h5>
                    <p class="card-text text-muted small">Resumo diário e mensal de todas as vendas e faturas emitidas.</p>
                    <button class="btn btn-outline-primary btn-sm w-100 mt-2" onclick="alert('Exportação de PDF em desenvolvimento na Fase 7.1')">
                        <i class="fas fa-file-pdf"></i> Gerar PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- Mapa de IVA -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-success"><i class="fas fa-balance-scale"></i> Mapa de Apuramento (IVA)</h5>
                    <p class="card-text text-muted small">Quadro resumo de IVA Suportado vs IVA Liquidado.</p>
                    <button class="btn btn-outline-success btn-sm w-100 mt-2" onclick="alert('Exportação de PDF em desenvolvimento na Fase 7.1')">
                        <i class="fas fa-file-pdf"></i> Gerar PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- SAFT-AO Atalho -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title text-dark"><i class="fas fa-file-code"></i> Ficheiro SAFT-AO</h5>
                    <p class="card-text text-muted small">Submissão tributária à AGT.</p>
                    <a href="{{ route('vendas.saft') }}" class="btn btn-dark btn-sm w-100 mt-2">
                        <i class="fas fa-arrow-right"></i> Aceder à Exportação
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

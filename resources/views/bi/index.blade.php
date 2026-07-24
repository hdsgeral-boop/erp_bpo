@extends('layouts.app')

@push('styles')
<!-- PivotTable.js CSS via CDN -->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/pivottable/2.23.0/pivot.min.css">
<style>
    .pvtUi { width: 100%; border: none !important; font-family: 'Inter', sans-serif !important; border-collapse: separate; border-spacing: 0; }
    .pvtAxisContainer, .pvtVals { background: #f8fafc !important; border-radius: 12px; border: 1px dashed #cbd5e1; padding: 14px; margin-bottom: 1rem; }
    .pvtAxisContainer li, .pvtAttr { padding: 6px 12px !important; background: #2563eb !important; color: #ffffff !important; border-radius: 8px !important; box-shadow: 0 2px 4px rgba(37,99,235,0.2) !important; margin: 4px !important; font-size: 0.8rem !important; font-weight: 600 !important; cursor: grab; border: none !important; }
    .pvtTable { border-collapse: collapse; width: 100%; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-top: 1rem; }
    .pvtTable thead tr th, .pvtTable tbody tr th { background-color: #f1f5f9; color: #334155; font-weight: 700; padding: 10px 14px; border: 1px solid #e2e8f0; font-size: 0.8rem; text-transform: uppercase; }
    .pvtTable tbody tr td { padding: 10px 14px; border: 1px solid #e2e8f0; color: #0f172a; text-align: right; font-weight: 500; font-size: 0.875rem; }
    .pvtTotal, .pvtGrandTotal { font-weight: 800 !important; background-color: #e2e8f0 !important; color: #0f172a !important; }
    .pvtRenderer, .pvtAggregator, .pvtAttrDropdown { padding: 8px 14px; border-radius: 8px; border: 1px solid #cbd5e1; background: white; font-size: 0.875rem; font-weight: 600; outline: none; margin-right: 8px; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header Title -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-bottom">
        <div>
            <h2 class="fw-bold text-dark mb-0"><i class="fas fa-chart-pie text-primary me-2"></i>Business Intelligence (BI)</h2>
            <p class="text-muted mb-0 fs-8">Painel Executivo de Análise Financeira, Vendas, Compras e Contabilidade PGC.</p>
        </div>
        <div>
            <button class="btn btn-primary fw-bold shadow-sm px-4" style="border-radius: 10px;" onclick="loadBiData()">
                <i class="fas fa-sync-alt me-1"></i> Atualizar Métricas
            </button>
        </div>
    </div>

    <!-- Filter Control Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-light">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Período de Análise</label>
                    <select id="filter-bi-period" class="form-select form-select-sm rounded-3 fw-semibold" onchange="loadBiData()">
                        <option value="all">Todo o Histórico</option>
                        <option value="current_year" selected>Ano Atual</option>
                        <option value="current_month">Mês Atual</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Módulo Específico</label>
                    <select id="filter-bi-module" class="form-select form-select-sm rounded-3 fw-semibold" onchange="loadBiData()">
                        <option value="all">Todos os Módulos</option>
                        <option value="Vendas">Vendas & Faturação</option>
                        <option value="Compras">Compras & Fornecedores</option>
                        <option value="Contabilidade">Contabilidade PGC</option>
                    </select>
                </div>
                <div class="col-md-6 text-end d-none d-md-block">
                    <span class="badge bg-secondary bg-opacity-10 text-secondary p-2 rounded-3 fs-8 fw-normal">
                        <i class="fas fa-info-circle me-1"></i> Dica: Arraste os blocos azuis para alterar as dimensões de análise.
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 14px; border-left: 4px solid #2563eb !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-8 fw-semibold text-uppercase">Total Vendas</span>
                        <h4 class="fw-bold text-dark mb-0 mt-1" id="kpi-vendas">0,00 Kz</h4>
                    </div>
                    <div class="bg-primary-light text-primary p-3 rounded-3">
                        <i class="fas fa-shopping-cart fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 14px; border-left: 4px solid #ef4444 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-8 fw-semibold text-uppercase">Total Compras</span>
                        <h4 class="fw-bold text-dark mb-0 mt-1" id="kpi-compras">0,00 Kz</h4>
                    </div>
                    <div class="bg-danger-light text-danger p-3 rounded-3">
                        <i class="fas fa-shopping-bag fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 14px; border-left: 4px solid #10b981 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-8 fw-semibold text-uppercase">Margem Operacional</span>
                        <h4 class="fw-bold text-dark mb-0 mt-1" id="kpi-margem">0,00 Kz</h4>
                    </div>
                    <div class="bg-success-light text-success p-3 rounded-3">
                        <i class="fas fa-chart-line fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 14px; border-left: 4px solid #06b6d4 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-8 fw-semibold text-uppercase">Registos Analisados</span>
                        <h4 class="fw-bold text-dark mb-0 mt-1" id="kpi-registos">0</h4>
                    </div>
                    <div class="bg-info-light text-info p-3 rounded-3">
                        <i class="fas fa-database fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Spinner -->
    <div id="bi-loader" class="alert alert-primary border-0 shadow-sm mb-4" style="display: none; border-radius: 12px;">
        <i class="fas fa-spinner fa-spin me-2"></i> A consolidar dados executivos e métricas financeiras...
    </div>

    <!-- Dynamic Pivot Table Analysis Section -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
        <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-0"><i class="fas fa-table text-primary me-2"></i>Análise Multidimensional (Matriz Pivot Dinâmica)</h5>
                <p class="text-muted fs-8 mb-0">Cruze Módulos, Entidades, Mês, Ano e Valores em tempo real.</p>
            </div>
        </div>
        <div class="card-body p-4 overflow-auto">
            <div id="pivot-container">
                <div class="text-center text-muted py-5">
                    <i class="fas fa-spinner fa-spin fa-2x mb-3 text-primary"></i>
                    <h5>A carregar o BI...</h5>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- jQuery UI -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<!-- PivotTable.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pivottable/2.23.0/pivot.min.js"></script>

<script>
    async function loadBiData() {
        const loader = document.getElementById('bi-loader');
        const container = $("#pivot-container");
        
        loader.style.display = 'block';
        container.css('opacity', '0.5');

        const period = document.getElementById('filter-bi-period').value;
        const moduleVal = document.getElementById('filter-bi-module').value;

        try {
            const response = await fetch(`/bi/dataset?period=${period}&module=${moduleVal}`, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Falha na resposta do servidor.');
            const data = await response.json();

            // Calcular KPIs Executivos
            let totalVendas = 0;
            let totalCompras = 0;

            data.forEach(item => {
                const val = parseFloat(item.Valor || 0);
                if (item.Módulo === 'Vendas') totalVendas += val;
                if (item.Módulo === 'Compras') totalCompras += val;
            });

            document.getElementById('kpi-vendas').innerText = totalVendas.toLocaleString('pt-AO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' Kz';
            document.getElementById('kpi-compras').innerText = totalCompras.toLocaleString('pt-AO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' Kz';
            document.getElementById('kpi-margem').innerText = (totalVendas - totalCompras).toLocaleString('pt-AO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' Kz';
            document.getElementById('kpi-registos').innerText = data.length;

            // Renderizar Pivot Table Dinâmica ou HTML Table Fallback
            container.empty().css('opacity', '1');

            if (typeof $.fn.pivotUI === 'function') {
                try {
                    container.pivotUI(data, {
                        rows: ["Módulo", "Categoria"],
                        cols: ["Mês"],
                        vals: ["Valor"],
                        aggregatorName: "Sum",
                        rendererName: "Table"
                    });
                } catch (pivotErr) {
                    console.error("PivotUI execution fallback:", pivotErr);
                    renderFallbackTable(data, container);
                }
            } else {
                renderFallbackTable(data, container);
            }

        } catch (error) {
            console.error('Erro no BI:', error);
            container.html('<div class="alert alert-warning">Não foi possível carregar os dados de BI: ' + error.message + '</div>').css('opacity', '1');
        } finally {
            loader.style.display = 'none';
        }
    }

    function renderFallbackTable(data, container) {
        if (!data || data.length === 0) {
            container.html('<div class="text-center text-muted py-4">Sem movimentos registados para o período selecionado.</div>');
            return;
        }

        let html = `
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Data</th>
                            <th>Módulo</th>
                            <th>Tipo</th>
                            <th>Entidade</th>
                            <th>Categoria</th>
                            <th>Natureza</th>
                            <th class="text-end">Valor (Kz)</th>
                            <th class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>`;

        data.forEach(item => {
            html += `
                <tr>
                    <td>${item.Data}</td>
                    <td><span class="badge bg-secondary bg-opacity-10 text-dark">${item.Módulo}</span></td>
                    <td>${item.Tipo}</td>
                    <td class="fw-bold">${item.Entidade}</td>
                    <td>${item.Categoria}</td>
                    <td>${item.Natureza}</td>
                    <td class="text-end fw-bold">${parseFloat(item.Valor || 0).toLocaleString('pt-AO', { minimumFractionDigits: 2 })} Kz</td>
                    <td class="text-center"><span class="badge ${item.Estado === 'Pago' || item.Estado === 'Confirmado' ? 'bg-success' : 'bg-warning text-dark'}">${item.Estado}</span></td>
                </tr>`;
        });

        html += '</tbody></table></div>';
        container.html(html);
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadBiData();
    });
</script>
@endpush

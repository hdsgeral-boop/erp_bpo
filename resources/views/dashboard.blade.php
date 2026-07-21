@extends('layouts.app')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<style>
    .kpi-card {
        border: none;
        border-radius: 1rem;
        transition: transform 0.2s, box-shadow 0.2s;
        background: white;
        overflow: hidden;
    }
    .kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08);
    }
    .kpi-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 1.5rem;
    }
    .chart-card {
        border: none;
        border-radius: 1rem;
        background: white;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4" style="background-color: #f8f9fc; min-height: 100vh;">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1 text-dark">Business Intelligence</h2>
            <p class="text-muted mb-0">Visão Geral e Indicadores de Performance (Ano corrente)</p>
        </div>
        <div>
            <button class="btn btn-primary shadow-sm rounded-pill px-4">
                <i class="fas fa-download me-2"></i> Exportar Relatório
            </button>
        </div>
    </div>

    <!-- KPIs Row -->
    <div class="row g-4 mb-4">
        <!-- Faturação Mensal -->
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-uppercase text-muted fw-bold mb-1" style="font-size: 0.8rem;">Faturação Mensal ({{ date('M') }})</p>
                            <h3 class="fw-bold mb-0 text-success">{{ number_format($kpis['monthly_sales'], 2) }} <span class="fs-6">Kz</span></h3>
                        </div>
                        <div class="kpi-icon bg-success bg-opacity-10 text-success">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tesouraria -->
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-uppercase text-muted fw-bold mb-1" style="font-size: 0.8rem;">Saldo Tesouraria</p>
                            <h3 class="fw-bold mb-0 text-primary">{{ number_format($kpis['treasury_balance'], 2) }} <span class="fs-6">Kz</span></h3>
                        </div>
                        <div class="kpi-icon bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produtos -->
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-uppercase text-muted fw-bold mb-1" style="font-size: 0.8rem;">Catálogo de Produtos</p>
                            <h3 class="fw-bold mb-0 text-info">{{ number_format($kpis['products']) }} <span class="fs-6">Ref.</span></h3>
                        </div>
                        <div class="kpi-icon bg-info bg-opacity-10 text-info">
                            <i class="fas fa-box"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colaboradores -->
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-uppercase text-muted fw-bold mb-1" style="font-size: 0.8rem;">Colaboradores Ativos</p>
                            <h3 class="fw-bold mb-0 text-warning">{{ number_format($kpis['employees']) }} <span class="fs-6">Pessoas</span></h3>
                        </div>
                        <div class="kpi-icon bg-warning bg-opacity-10 text-warning">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Gráfico Linhas: Faturação Anual -->
        <div class="col-lg-8">
            <div class="card chart-card h-100 p-4">
                <h5 class="fw-bold mb-4">Evolução de Faturação Anual</h5>
                <div id="salesChart" style="min-height: 300px;"></div>
            </div>
        </div>
        
        <!-- Gráfico Circular: Despesas por Conta -->
        <div class="col-lg-4">
            <div class="card chart-card h-100 p-4">
                <h5 class="fw-bold mb-4">Despesas por Categoria / Conta</h5>
                @if(count($expenseData) > 0)
                    <div id="expensesChart" style="min-height: 300px;"></div>
                @else
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                        <i class="fas fa-chart-pie fa-3x mb-3 opacity-25"></i>
                        <p>Não há dados de despesas registados este ano.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tabela Últimos Movimentos -->
    <div class="row">
        <div class="col-12">
            <div class="card chart-card p-4">
                <h5 class="fw-bold mb-4">Últimas Faturações (Tempo Real)</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-muted text-uppercase" style="font-size: 0.8rem">Documento</th>
                                <th class="text-muted text-uppercase" style="font-size: 0.8rem">Data</th>
                                <th class="text-muted text-uppercase" style="font-size: 0.8rem">Cliente</th>
                                <th class="text-muted text-uppercase" style="font-size: 0.8rem">Valor (Kz)</th>
                                <th class="text-muted text-uppercase" style="font-size: 0.8rem">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSales as $sale)
                                <tr>
                                    <td class="fw-bold text-primary">{{ $sale->doc_number }}</td>
                                    <td>{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y H:i') }}</td>
                                    <td>{{ $sale->customer->name ?? 'Consumidor Final' }}</td>
                                    <td class="fw-bold">{{ number_format($sale->total_amount, 2) }}</td>
                                    <td>
                                        @if($sale->status == 'DRAFT')
                                            <span class="badge bg-secondary">Rascunho</span>
                                        @elseif($sale->status == 'FINAL')
                                            <span class="badge bg-success">Fechado</span>
                                        @elseif($sale->status == 'CANCELLED')
                                            <span class="badge bg-danger">Anulado</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Nenhuma venda registada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Gráfico de Faturação
    var salesOptions = {
        series: [{
            name: 'Faturação (Kz)',
            data: @json($salesChartData)
        }],
        chart: {
            height: 350,
            type: 'area',
            fontFamily: 'inherit',
            toolbar: { show: false }
        },
        colors: ['#10b981'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        fill: {
            type: 'gradient',
            gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] }
        },
        xaxis: {
            categories: @json($months),
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: { formatter: function (val) { return val.toLocaleString() + " Kz"; } }
        },
        grid: { strokeDashArray: 4 }
    };
    var salesChart = new ApexCharts(document.querySelector("#salesChart"), salesOptions);
    salesChart.render();

    // Gráfico Circular de Despesas
    @if(count($expenseData) > 0)
    var expenseOptions = {
        series: @json($expenseData),
        chart: {
            type: 'donut',
            height: 350,
            fontFamily: 'inherit'
        },
        labels: @json($expenseLabels),
        colors: ['#ef4444', '#f59e0b', '#3b82f6', '#8b5cf6', '#ec4899', '#14b8a6'],
        plotOptions: {
            pie: { donut: { size: '65%' } }
        },
        dataLabels: { enabled: false },
        legend: { position: 'bottom' },
        tooltip: {
            y: { formatter: function (val) { return val.toLocaleString() + " Kz"; } }
        }
    };
    var expensesChart = new ApexCharts(document.querySelector("#expensesChart"), expenseOptions);
    expensesChart.render();
    @endif
});
</script>
@endsection

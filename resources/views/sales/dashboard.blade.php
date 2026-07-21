@extends('layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        border: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 140px;
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .card-premium {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
    }
    .table-custom { margin-bottom: 0; }
    .table-custom thead th {
        background-color: #f8fafc; color: #475569; font-weight: 600; font-size: 0.85rem; padding: 1rem 1.5rem;
    }
    .table-custom tbody td { padding: 1rem 1.5rem; vertical-align: middle; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-chart-line text-primary me-2"></i>Dashboard de Vendas</h2>
            <p class="text-muted mb-0">Resumo da faturação e desempenho.</p>
        </div>
        <a href="{{ route('vendas.pos') }}" class="btn btn-primary" style="border-radius: 10px;"><i class="fas fa-desktop me-2"></i> Ir para o POS</a>
    </div>

    <div class="row g-4 mb-4">
        <!-- Total Faturado -->
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted fw-semibold mb-1">Total Faturado</p>
                        <h3 class="fw-bold text-dark mb-0">{{ number_format($totalFaturado, 2, ',', '.') }} Kz</h3>
                    </div>
                    <div class="stat-icon bg-primary text-white" style="opacity: 0.9;">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
                <div class="mt-3 text-success small fw-semibold">
                    <i class="fas fa-arrow-up me-1"></i> Global do sistema
                </div>
            </div>
        </div>

        <!-- Faturas Pendentes -->
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted fw-semibold mb-1">Valores a Receber</p>
                        <h3 class="fw-bold text-dark mb-0">{{ number_format($faturasPendentes, 2, ',', '.') }} Kz</h3>
                    </div>
                    <div class="stat-icon bg-warning text-dark" style="opacity: 0.9;">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="mt-3 text-warning small fw-semibold">
                    <i class="fas fa-exclamation-circle me-1"></i> Faturas Pendentes de Pagamento
                </div>
            </div>
        </div>

        <!-- Vendas Hoje -->
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted fw-semibold mb-1">Nº Vendas Hoje</p>
                        <h3 class="fw-bold text-dark mb-0">{{ $vendasHoje }}</h3>
                    </div>
                    <div class="stat-icon bg-success text-white" style="opacity: 0.9;">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                </div>
                <div class="mt-3 text-muted small fw-semibold">
                    <i class="fas fa-calendar-day me-1"></i> Documentos emitidos hoje
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela Vendas Recentes -->
    <div class="card-premium">
        <h5 class="fw-bold mb-4 text-dark">Documentos Recentes</h5>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Nº Doc</th>
                        <th>Cliente</th>
                        <th>Data</th>
                        <th>Total (Kz)</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSales as $sale)
                    <tr>
                        <td class="fw-bold">{{ $sale->doc_number }}</td>
                        <td>{{ $sale->customer ? $sale->customer->name : 'Consumidor Final' }}</td>
                        <td>{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</td>
                        <td class="fw-bold">{{ number_format($sale->total_amount, 2, ',', '.') }}</td>
                        <td>
                            @if($sale->status == 'CONCLUIDO')
                                <span class="badge bg-success">Concluído</span>
                            @elseif($sale->status == 'PENDENTE_PAGAMENTO')
                                <span class="badge bg-warning text-dark">Pendente Pagamento</span>
                            @elseif($sale->status == 'CANCELADO')
                                <span class="badge bg-danger">Cancelado</span>
                            @else
                                <span class="badge bg-secondary">{{ $sale->status }}</span>
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
@endsection

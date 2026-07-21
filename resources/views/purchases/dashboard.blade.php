@extends('layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    .card-premium {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .card-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.1);
    }
    .stat-value { font-size: 2rem; font-weight: 700; color: #1e293b; line-height: 1; margin-top: 0.5rem; }
    .stat-label { color: #64748b; font-size: 0.875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .icon-box {
        width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;
    }
    .icon-primary { background: #eff6ff; color: #3b82f6; }
    .icon-warning { background: #fffbeb; color: #f59e0b; }
    .icon-success { background: #f0fdf4; color: #10b981; }
    .icon-danger { background: #fef2f2; color: #ef4444; }

    .module-card {
        display: flex; align-items: center; padding: 1.25rem; border-radius: 12px; background: #f8fafc; text-decoration: none; color: inherit; border: 1px solid #e2e8f0; transition: all 0.2s;
    }
    .module-card:hover {
        background: #ffffff; border-color: #3b82f6; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
    }
    
    .table-custom { margin-bottom: 0; }
    .table-custom thead th { background-color: transparent; color: #475569; font-weight: 600; font-size: 0.8rem; padding: 1rem 0.5rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; }
    .table-custom tbody td { padding: 1rem 0.5rem; vertical-align: middle; color: #1e293b; border-bottom: 1px solid #f1f5f9; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-shopping-cart text-primary me-2"></i>Dashboard de Compras</h2>
            <p class="text-muted mb-0">Visão geral do aprovisionamento e despesas.</p>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card-premium h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Pedidos Internos</div>
                        <div class="stat-value">{{ $stats['total_pedidos'] }}</div>
                    </div>
                    <div class="icon-box icon-warning"><i class="fas fa-clipboard-list"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-premium h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Encomendas</div>
                        <div class="stat-value">{{ $stats['total_encomendas'] }}</div>
                    </div>
                    <div class="icon-box icon-primary"><i class="fas fa-truck-loading"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-premium h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Faturas de Fornecedor</div>
                        <div class="stat-value">{{ $stats['total_faturas'] }}</div>
                    </div>
                    <div class="icon-box icon-success"><i class="fas fa-file-invoice"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-premium h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Total Gasto (Kz)</div>
                        <div class="stat-value text-danger">{{ number_format($stats['total_gasto'], 0, ',', '.') }}</div>
                    </div>
                    <div class="icon-box icon-danger"><i class="fas fa-wallet"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Navegação Rápida -->
        <div class="col-lg-4">
            <div class="card-premium h-100">
                <h5 class="fw-bold mb-4">Fluxo de Compras</h5>
                
                <a href="{{ route('compras.requests.index') }}" class="module-card mb-3">
                    <div class="icon-box bg-white shadow-sm text-warning me-3"><i class="fas fa-clipboard-list"></i></div>
                    <div>
                        <h6 class="fw-bold mb-1">Pedidos de Compra</h6>
                        <p class="text-muted small mb-0">Necessidades internas de material</p>
                    </div>
                    <i class="fas fa-chevron-right ms-auto text-muted"></i>
                </a>
                
                <a href="{{ route('compras.orders.index') }}" class="module-card mb-3">
                    <div class="icon-box bg-white shadow-sm text-primary me-3"><i class="fas fa-file-contract"></i></div>
                    <div>
                        <h6 class="fw-bold mb-1">Encomendas a Fornecedor</h6>
                        <p class="text-muted small mb-0">Notas de encomenda (PO)</p>
                    </div>
                    <i class="fas fa-chevron-right ms-auto text-muted"></i>
                </a>
                
                <a href="{{ route('compras.deliveries.index') }}" class="module-card mb-3">
                    <div class="icon-box bg-white shadow-sm text-info me-3"><i class="fas fa-box-open"></i></div>
                    <div>
                        <h6 class="fw-bold mb-1">Receção de Mercadoria</h6>
                        <p class="text-muted small mb-0">Entradas em Armazém / Guias</p>
                    </div>
                    <i class="fas fa-chevron-right ms-auto text-muted"></i>
                </a>

                <a href="{{ route('compras.faturas.index') }}" class="module-card">
                    <div class="icon-box bg-white shadow-sm text-success me-3"><i class="fas fa-file-invoice-dollar"></i></div>
                    <div>
                        <h6 class="fw-bold mb-1">Faturas de Fornecedor</h6>
                        <p class="text-muted small mb-0">Registo de dívidas e despesas</p>
                    </div>
                    <i class="fas fa-chevron-right ms-auto text-muted"></i>
                </a>
            </div>
        </div>

        <!-- Tabelas Recentes -->
        <div class="col-lg-8">
            <div class="card-premium mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Últimas Encomendas</h5>
                    <a href="{{ route('compras.orders.index') }}" class="btn btn-sm btn-light">Ver Todas</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Nº Encomenda</th>
                                <th>Data</th>
                                <th>Fornecedor</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td class="fw-bold">{{ $order->order_number }}</td>
                                <td>{{ \Carbon\Carbon::parse($order->date)->format('d/m/Y') }}</td>
                                <td>{{ $order->supplier->name ?? 'N/A' }}</td>
                                <td><span class="badge bg-primary-subtle text-primary border">{{ $order->status }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted">Sem encomendas recentes.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-premium">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Últimas Faturas Registadas</h5>
                    <a href="{{ route('compras.faturas.index') }}" class="btn btn-sm btn-light">Ver Todas</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Fatura</th>
                                <th>Data</th>
                                <th>Fornecedor</th>
                                <th class="text-end">Total (Kz)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentInvoices as $invoice)
                            <tr>
                                <td class="fw-bold">{{ $invoice->invoice_number }}</td>
                                <td>{{ \Carbon\Carbon::parse($invoice->date)->format('d/m/Y') }}</td>
                                <td>{{ $invoice->supplier->name ?? 'N/A' }}</td>
                                <td class="text-end fw-bold text-success">{{ number_format($invoice->total_amount, 2, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted">Sem faturas recentes.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

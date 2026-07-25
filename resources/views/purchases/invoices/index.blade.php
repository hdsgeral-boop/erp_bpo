@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    .kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1.25rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    }
    .badge-issued { background-color: #dbeafe; color: #1e40af; }
    .badge-paid { background-color: #dcfce7; color: #15803d; }
    .badge-partial { background-color: #fef3c7; color: #b45309; }
    .badge-cancelled { background-color: #fee2e2; color: #b91c1c; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-extrabold text-dark mb-1">
                <i class="fas fa-file-invoice-dollar text-primary me-2"></i> Faturas de Fornecedores
            </h2>
            <p class="text-muted small mb-0">Gestão de faturas de compras, despesas e aprovisionamento.</p>
        </div>
        <div>
            <a href="{{ route('compras.faturas.create') }}" class="btn btn-primary fw-bold px-3 py-2" style="border-radius: 10px;">
                <i class="fas fa-plus me-1"></i> Registar Fatura
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="kpi-card border-start border-primary border-4">
                <div class="text-muted small fw-bold text-uppercase">Faturas Registadas</div>
                <div class="fs-4 fw-extrabold text-dark mt-1">{{ number_format($stats['total_count'] ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card border-start border-info border-4">
                <div class="text-muted small fw-bold text-uppercase">Total Compras</div>
                <div class="fs-4 fw-extrabold text-info mt-1">{{ number_format($stats['total_amount'] ?? 0, 2, ',', '.') }} Kz</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card border-start border-success border-4">
                <div class="text-muted small fw-bold text-uppercase">Total Liquidado / Pago</div>
                <div class="fs-4 fw-extrabold text-success mt-1">{{ number_format($stats['total_paid'] ?? 0, 2, ',', '.') }} Kz</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card border-start border-warning border-4">
                <div class="text-muted small fw-bold text-uppercase">Saldo Pendente</div>
                <div class="fs-4 fw-extrabold text-warning mt-1">{{ number_format($stats['total_pending'] ?? 0, 2, ',', '.') }} Kz</div>
            </div>
        </div>
    </div>

    <!-- Main Card & Table -->
    <div class="card-premium overflow-hidden">
        <!-- Filter Header -->
        <div class="p-3 border-bottom bg-light">
            <form action="{{ route('compras.faturas.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold mb-1">Pesquisar Nº Fatura ou Fornecedor</label>
                    <input type="text" name="search" class="form-control form-control-sm rounded-3" placeholder="Ex: FT 2026/001, Empresa LDA..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold mb-1">Fornecedor</label>
                    <select name="supplier_id" class="form-select form-select-sm rounded-3">
                        <option value="">Todos os Fornecedores</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold mb-1">Estado</label>
                    <select name="status" class="form-select form-select-sm rounded-3">
                        <option value="">Todos os Estados</option>
                        <option value="ISSUED" {{ request('status') == 'ISSUED' ? 'selected' : '' }}>Registada (Emitida)</option>
                        <option value="CANCELLED" {{ request('status') == 'CANCELLED' ? 'selected' : '' }}>Anulada</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm rounded-3 grow fw-bold"><i class="fas fa-filter me-1"></i> Filtrar</button>
                    <a href="{{ route('compras.faturas.index') }}" class="btn btn-outline-secondary btn-sm rounded-3"><i class="fas fa-undo me-1"></i> Limpar</a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">N.º FATURA</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">FORNECEDOR</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">DATA DE EMISSÃO</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold text-end">TOTAL (AKZ)</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold text-center">ESTADO</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold text-center">AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                    <tr>
                        <td class="py-3 px-4 font-monospace">
                            <a href="{{ route('compras.faturas.show', $inv->id) }}" class="fw-bold text-primary text-decoration-none">
                                {{ $inv->invoice_number }}
                            </a>
                        </td>
                        <td class="py-3 px-4">
                            <div class="fw-bold text-dark">{{ $inv->supplier ? $inv->supplier->name : 'Fornecedor Desconhecido' }}</div>
                            <div class="small text-muted font-monospace">NIF: {{ $inv->supplier->nif ?? 'N/D' }}</div>
                        </td>
                        <td class="py-3 px-4 text-muted font-monospace">
                            {{ \Carbon\Carbon::parse($inv->date)->format('d/m/Y') }}
                        </td>
                        <td class="py-3 px-4 text-end fw-extrabold text-dark fs-6">
                            {{ number_format($inv->total_amount, 2, ',', '.') }} Kz
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($inv->status === 'CANCELLED')
                                <span class="badge badge-cancelled px-3 py-2 rounded-pill fw-bold"><i class="fas fa-ban me-1"></i> Anulada</span>
                            @elseif($inv->payment_status === 'PAID')
                                <span class="badge badge-paid px-3 py-2 rounded-pill fw-bold"><i class="fas fa-check-double me-1"></i> Paga</span>
                            @elseif($inv->payment_status === 'PARTIAL')
                                <span class="badge badge-partial px-3 py-2 rounded-pill fw-bold"><i class="fas fa-clock me-1"></i> Parcial</span>
                            @else
                                <span class="badge badge-issued px-3 py-2 rounded-pill fw-bold"><i class="fas fa-file-invoice me-1"></i> Registada</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('compras.faturas.show', $inv->id) }}" class="btn btn-sm btn-outline-primary fw-bold px-2 py-1" style="border-radius: 8px;" title="Ver Detalhes">
                                    <i class="fas fa-eye me-1"></i> Analisar
                                </a>
                                <a href="{{ route('compras.faturas.pdf', $inv->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary fw-bold px-2 py-1" style="border-radius: 8px;" title="Imprimir PDF">
                                    <i class="fas fa-print"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-file-invoice-dollar fa-2x mb-3 d-block text-secondary opacity-50"></i>
                            Nenhuma fatura de fornecedor registada.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
        <div class="p-3 border-top bg-light">
            {{ $invoices->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection

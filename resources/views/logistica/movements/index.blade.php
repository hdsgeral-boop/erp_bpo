@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    .kpi-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 4px 15px -3px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px -5px rgba(0,0,0,0.08);
    }
    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
    }
    .table-custom thead th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 600;
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 1rem 1.25rem;
        border-bottom: 2px solid #e2e8f0;
    }
    .table-custom tbody td {
        padding: 1rem 1.25rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .badge-in {
        background-color: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
        font-weight: 600;
    }
    .badge-out {
        background-color: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
        font-weight: 600;
    }
    .badge-adj {
        background-color: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
        font-weight: 600;
    }
    .btn-add-new {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white;
        border-radius: 10px;
        padding: 0.6rem 1.4rem;
        font-weight: 600;
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
    }
    .btn-add-new:hover { color: white; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.4); }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-history text-primary me-2"></i>Histórico de Movimentos de Stock</h2>
            <p class="text-muted mb-0">Registo completo de entradas, saídas e ajustes manuais de inventário.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('logistica.movements.pdf', request()->query()) }}" target="_blank" class="btn btn-outline-secondary fw-bold" style="border-radius: 10px; padding: 0.6rem 1.2rem;">
                <i class="fas fa-file-pdf text-danger me-2"></i> Exportar PDF
            </a>
            <button type="button" class="btn btn-add-new" data-bs-toggle="modal" data-bs-target="#modalNewMovement">
                <i class="fas fa-plus-circle me-2"></i> Registar Movimento
            </button>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" style="border-radius: 10px;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" style="border-radius: 10px;">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="kpi-card d-flex align-items-center">
                <div class="kpi-icon bg-primary bg-opacity-10 text-primary me-3">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Total Movimentos</div>
                    <div class="fs-4 fw-bold text-dark">{{ number_format($stats['total_count'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card d-flex align-items-center">
                <div class="kpi-icon bg-success bg-opacity-10 text-success me-3">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Entradas (+ Qtd.)</div>
                    <div class="fs-4 fw-bold text-success">+{{ number_format($stats['total_in'], 2, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card d-flex align-items-center">
                <div class="kpi-icon bg-danger bg-opacity-10 text-danger me-3">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Saídas (- Qtd.)</div>
                    <div class="fs-4 fw-bold text-danger">-{{ number_format($stats['total_out'], 2, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card d-flex align-items-center">
                <div class="kpi-icon bg-info bg-opacity-10 text-info me-3">
                    <i class="fas fa-balance-scale"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Balanço Líquido</div>
                    <div class="fs-4 fw-bold {{ ($stats['total_in'] - $stats['total_out']) >= 0 ? 'text-primary' : 'text-danger' }}">
                        {{ number_format($stats['total_in'] - $stats['total_out'], 2, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card & Filters -->
    <div class="card-premium mb-4">
        <div class="p-4 border-bottom bg-light">
            <form action="{{ route('logistica.movements.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold mb-1">Pesquisar Artigo / Ref.</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Ex: Produto, Código..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold mb-1">Armazém</label>
                    <select name="warehouse_id" class="form-select">
                        <option value="">Todos os Armazéns</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold mb-1">Tipo de Movimento</label>
                    <select name="type" class="form-select">
                        <option value="">Todos os Tipos</option>
                        <option value="IN" {{ request('type') == 'IN' ? 'selected' : '' }}>Entrada (IN)</option>
                        <option value="OUT" {{ request('type') == 'OUT' ? 'selected' : '' }}>Saída (OUT)</option>
                        <option value="ADJUSTMENT" {{ request('type') == 'ADJUSTMENT' ? 'selected' : '' }}>Ajuste Manual</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold mb-1">Data Inicial</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary grow fw-bold" style="border-radius: 8px;">
                        <i class="fas fa-filter me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('logistica.movements.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">
                        <i class="fas fa-undo me-1"></i> Limpar
                    </a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-custom align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 12%;">Data</th>
                        <th style="width: 15%;">Armazém</th>
                        <th style="width: 30%;">Artigo / Produto</th>
                        <th style="width: 13%;" class="text-center">Tipo</th>
                        <th style="width: 12%;" class="text-end">Quantidade</th>
                        <th style="width: 18%;">Referência / Origem</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $m)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($m->date)->format('d/m/Y') }}</div>
                            <small class="text-muted">{{ $m->created_at ? $m->created_at->format('H:i') : '' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-2 py-1">
                                <i class="fas fa-warehouse text-muted me-1"></i> {{ $m->warehouse ? $m->warehouse->name : 'Geral' }}
                            </span>
                        </td>
                        <td>
                            @if($m->product)
                                <div class="fw-bold text-primary">{{ $m->product->name }}</div>
                                <small class="text-muted font-monospace">{{ $m->product->code }}</small>
                            @else
                                <span class="text-muted">Artigo #{{ $m->product_id }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($m->type === 'IN')
                                <span class="badge badge-in px-3 py-1 rounded-pill"><i class="fas fa-arrow-down me-1"></i> Entrada</span>
                            @elseif($m->type === 'OUT')
                                <span class="badge badge-out px-3 py-1 rounded-pill"><i class="fas fa-arrow-up me-1"></i> Saída</span>
                            @else
                                <span class="badge badge-adj px-3 py-1 rounded-pill"><i class="fas fa-adjust me-1"></i> Ajuste</span>
                            @endif
                        </td>
                        <td class="text-end font-monospace fw-bold {{ $m->type === 'IN' ? 'text-success' : 'text-danger' }}">
                            {{ $m->type === 'IN' ? '+' : '-' }}{{ number_format($m->quantity, 2, ',', '.') }}
                        </td>
                        <td>
                            <span class="text-secondary small fw-semibold">{{ $m->reference ?: 'Ajuste Interno' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-exchange-alt fa-3x mb-3 d-block text-slate-300"></i>
                            Nenhum movimento de stock registado até ao momento.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($movements->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $movements->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Novo Movimento / Ajuste de Stock -->
<div class="modal fade" id="modalNewMovement" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-boxes text-primary me-2"></i>Registar Movimento de Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('logistica.movements.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Tipo de Movimento <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="IN">Entrada (Acrescentar ao Stock)</option>
                            <option value="OUT">Saída (Abater ao Stock)</option>
                            <option value="ADJUSTMENT">Ajuste de Inventário</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Armazém <span class="text-danger">*</span></label>
                        <select name="warehouse_id" class="form-select" required>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Artigo / Produto <span class="text-danger">*</span></label>
                        <select name="product_id" class="form-select" required>
                            <option value="">Selecione o Artigo...</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} (Código: {{ $p->code ?? 'N/D' }} | Stock Atual: {{ number_format($p->stock_qty, 2, ',', '.') }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Data <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Quantidade <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" name="quantity" class="form-control" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Referência / Motivo</label>
                        <input type="text" name="reference" class="form-control" placeholder="Ex: Contagem física, Danificado, Ajuste manual...">
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Confirmar e Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

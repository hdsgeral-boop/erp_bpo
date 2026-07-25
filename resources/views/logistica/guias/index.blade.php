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
    .table-custom thead th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.8rem;
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
        <div class="d-flex align-items-center gap-3">
            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #eff6ff, #dbeafe); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-truck-moving text-primary" style="font-size: 1.5rem;"></i>
            </div>
            <div>
                <h2 class="fw-bold mb-0 text-dark">Guias de Remessa e Transporte (GT/GR)</h2>
                <p class="text-muted mb-0">Gestão e emissão de guias de transporte e remessa em conformidade com a AGT.</p>
            </div>
        </div>
        <div>
            <a href="{{ route('vendas.documentos.create', 'guias') }}" class="btn btn-add-new">
                <i class="fas fa-plus-circle me-2"></i> Emitir Nova Guia
            </a>
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

    <!-- Quick Cards Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="kpi-card">
                <span class="text-muted small fw-bold text-uppercase d-block mb-1">Total Guias de Remessa</span>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['total_count'], 0, ',', '.') }} <span class="fs-6 text-muted fw-normal">Documentos</span></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card">
                <span class="text-muted small fw-bold text-uppercase d-block mb-1">Guias de Transporte Válidas</span>
                <h3 class="fw-bold text-success mb-0">{{ number_format($stats['valid_count'], 0, ',', '.') }} <span class="fs-6 text-muted fw-normal">Assinadas</span></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card">
                <span class="text-muted small fw-bold text-uppercase d-block mb-1">Ponto de Carga / Origem</span>
                <h3 class="fw-bold text-primary mb-0 fs-5 text-truncate">{{ $stats['main_warehouse'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="card-premium mb-4">
        <div class="p-4 border-bottom bg-light">
            <form action="{{ route('logistica.guias.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold mb-1">Pesquisar Guia ou Cliente</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Ex: GT 2026/001, Cliente..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold mb-1">Tipo de Documento</label>
                    <select name="doc_type" class="form-select">
                        <option value="">Todas as Guias (GT / GR)</option>
                        <option value="GT" {{ request('doc_type') == 'GT' ? 'selected' : '' }}>GT - Guia de Transporte</option>
                        <option value="GR" {{ request('doc_type') == 'GR' ? 'selected' : '' }}>GR - Guia de Remessa</option>
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
                    <a href="{{ route('logistica.guias.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">
                        <i class="fas fa-undo me-1"></i> Limpar
                    </a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-custom align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 15%;">N.º Guia</th>
                        <th style="width: 25%;">Destinatário / Cliente</th>
                        <th style="width: 25%;">Local de Carga ➔ Descarga</th>
                        <th style="width: 15%;">Data Transporte</th>
                        <th style="width: 10%;">Estado AGT</th>
                        <th style="width: 10%;" class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guias as $guia)
                    <tr>
                        <td class="fw-bold">
                            <a href="{{ route('vendas.documentos.show', ['category' => 'guias', 'id' => $guia->id]) }}" class="text-primary text-decoration-none">
                                {{ $guia->doc_number }}
                            </a>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $guia->customer ? $guia->customer->name : 'Consumidor Final' }}</div>
                            <small class="text-muted">NIF: {{ $guia->customer ? ($guia->customer->tax_id ?: 'Consumidor Final') : '999999999' }}</small>
                        </td>
                        <td class="small text-secondary">
                            <i class="fas fa-warehouse text-primary me-1"></i> {{ $guia->warehouse ? $guia->warehouse->name : 'Armazém Central' }}
                            <i class="fas fa-long-arrow-alt-right mx-1 text-muted"></i>
                            <i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $guia->customer && $guia->customer->address ? $guia->customer->address : 'Destino Indicado' }}
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($guia->date)->format('d/m/Y') }}</div>
                        </td>
                        <td>
                            @if($guia->status === 'CANCELLED')
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2 py-1" style="font-size: 0.75rem;">
                                    <i class="fas fa-times me-1"></i> ANULADA
                                </span>
                            @elseif(!empty($guia->hash))
                                <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1" style="font-size: 0.75rem;">
                                    <i class="fas fa-check me-1"></i> VÁLIDA AGT
                                </span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-2 py-1" style="font-size: 0.75rem;">
                                    <i class="fas fa-clock me-1"></i> RASCUNHO
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('vendas.documentos.pdf', ['category' => 'guias', 'id' => $guia->id]) }}" target="_blank" class="btn btn-sm btn-outline-primary fw-bold" style="border-radius: 6px; padding: 0.35rem 0.75rem;" title="Imprimir Guia A4">
                                    <i class="fas fa-print me-1"></i> Imprimir
                                </a>
                                <a href="{{ route('vendas.documentos.show', ['category' => 'guias', 'id' => $guia->id]) }}" class="btn btn-sm btn-light text-secondary border" style="border-radius: 6px;" title="Ver Detalhes">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-truck-loading fa-3x mb-3 text-slate-300 d-block"></i>
                            Nenhuma Guia de Transporte ou Remessa encontrada.
                            <div class="mt-3">
                                <a href="{{ route('vendas.documentos.create', 'guias') }}" class="btn btn-primary btn-sm fw-bold">
                                    <i class="fas fa-plus me-1"></i> Emitir Nova Guia
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($guias->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $guias->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection

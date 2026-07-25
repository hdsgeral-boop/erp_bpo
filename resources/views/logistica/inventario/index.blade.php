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
                <i class="fas fa-tasks text-primary" style="font-size: 1.5rem;"></i>
            </div>
            <div>
                <h2 class="fw-bold mb-0 text-dark">Sessões de Inventário</h2>
                <p class="text-muted mb-0">Gestão do processo de contagem física e regularização de stocks.</p>
            </div>
        </div>
        <div>
            <button class="btn btn-add-new" data-bs-toggle="modal" data-bs-target="#createSessionModal">
                <i class="fas fa-plus me-2"></i> Nova Sessão
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

    <!-- Quick Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="kpi-card">
                <span class="text-muted small fw-bold text-uppercase d-block mb-1">Total de Sessões</span>
                <h3 class="fw-bold text-dark mb-0">{{ $sessions->count() }} <span class="fs-6 text-muted fw-normal">Registos</span></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card">
                <span class="text-muted small fw-bold text-uppercase d-block mb-1">Sessões Abertas</span>
                <h3 class="fw-bold text-primary mb-0">{{ $sessions->where('status', 'OPEN')->count() }} <span class="fs-6 text-muted fw-normal">Em Contagem</span></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card">
                <span class="text-muted small fw-bold text-uppercase d-block mb-1">Em Revisão / Análise</span>
                <h3 class="fw-bold text-warning mb-0">{{ $sessions->where('status', 'REVIEW')->count() }} <span class="fs-6 text-muted fw-normal">Pendente Apoio</span></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card">
                <span class="text-muted small fw-bold text-uppercase d-block mb-1">Concluídas & Regularizadas</span>
                <h3 class="fw-bold text-success mb-0">{{ $sessions->where('status', 'CLOSED')->count() }} <span class="fs-6 text-muted fw-normal">Fechadas</span></h3>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card-premium">
        <div class="table-responsive">
            <table class="table table-custom align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 10%;">ID</th>
                        <th style="width: 15%;">Data</th>
                        <th style="width: 25%;">Armazém</th>
                        <th style="width: 25%;">Responsável</th>
                        <th style="width: 13%;">Status</th>
                        <th style="width: 12%;" class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $session)
                    <tr>
                        <td class="fw-bold">
                            <span class="badge bg-light text-dark border px-2 py-1">#{{ str_pad($session->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($session->date)->format('d/m/Y') }}</div>
                        </td>
                        <td>
                            <span class="fw-bold text-primary">
                                <i class="fas fa-warehouse me-1 text-muted"></i> {{ $session->warehouse->name ?? 'N/D' }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $session->responsible_name ?: 'Equipa de Inventário' }}</div>
                        </td>
                        <td>
                            @if($session->status == 'OPEN')
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-1 rounded-pill">
                                    <i class="fas fa-edit me-1"></i> ABERTO (Contagem)
                                </span>
                            @elseif($session->status == 'REVIEW')
                                <span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-3 py-1 rounded-pill">
                                    <i class="fas fa-balance-scale me-1"></i> EM REVISÃO
                                </span>
                            @else
                                <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1 rounded-pill">
                                    <i class="fas fa-check-circle me-1"></i> CONCLUÍDO
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($session->status == 'OPEN')
                                <a href="{{ route('logistica.inventario.contagem', $session->id) }}" class="btn btn-sm btn-primary fw-bold" style="border-radius: 8px;">
                                    <i class="fas fa-list-ol me-1"></i> Continuar Contagem
                                </a>
                            @elseif($session->status == 'REVIEW')
                                <a href="{{ route('logistica.inventario.review', $session->id) }}" class="btn btn-sm btn-warning text-dark fw-bold" style="border-radius: 8px;">
                                    <i class="fas fa-balance-scale me-1"></i> Rever Diferenças
                                </a>
                            @else
                                <a href="{{ route('logistica.inventario.review', $session->id) }}" class="btn btn-sm btn-light text-secondary border" style="border-radius: 8px;">
                                    <i class="fas fa-eye me-1"></i> Ver Resumo
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-clipboard-list fa-3x mb-3 text-slate-300 d-block"></i>
                            Nenhuma sessão de inventário registada.
                            <div class="mt-3">
                                <button class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#createSessionModal">
                                    <i class="fas fa-plus me-1"></i> Iniciar Primeira Sessão
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Nova Sessão -->
    <div class="modal fade" id="createSessionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <form action="{{ route('logistica.inventario.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom bg-light">
                        <h5 class="modal-title fw-bold text-dark"><i class="fas fa-clipboard-check text-primary me-2"></i>Iniciar Sessão de Inventário</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert alert-info border-0 bg-info bg-opacity-10 text-info mb-3">
                            <i class="fas fa-info-circle me-1"></i> Esta ação irá capturar uma instantânea do stock do armazém para comparar com a contagem física real.
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Armazém a Inventariar <span class="text-danger">*</span></label>
                            <select name="warehouse_id" class="form-select" required>
                                <option value="">-- Selecione o Armazém --</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Data de Contagem <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Responsável / Equipa (Opcional)</label>
                            <input type="text" name="responsible_name" class="form-control" placeholder="Ex: João Silva / Equipa Logística">
                        </div>
                    </div>
                    <div class="modal-footer border-top bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary fw-bold px-4">Gerar Folha de Contagem</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

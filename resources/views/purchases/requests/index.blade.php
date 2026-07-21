@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    .table-custom thead th {
        background-color: #ffffff;
        color: #475569;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        padding: 1rem 1.5rem;
        border-bottom: 2px solid #e2e8f0;
    }
    .table-custom tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .btn-add-new {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border-radius: 10px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
    }
    .btn-add-new:hover { color: white; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4); }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-file-signature text-primary me-2"></i>Pedidos Internos</h2>
            <p class="text-muted mb-0">Solicitações de material por parte dos colaboradores/departamentos.</p>
        </div>
        <a href="{{ route('compras.pedidos.create') }}" class="btn btn-add-new">
            <i class="fas fa-plus me-2"></i> Novo Pedido
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm" style="border-radius: 10px;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger shadow-sm" style="border-radius: 10px;">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card-premium">
        <div class="p-4 border-bottom bg-light">
            <form action="{{ route('compras.pedidos.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold mb-1">Pesquisar ID ou Requerente</label>
                    <input type="text" name="search" class="form-control" placeholder="..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold mb-1">Estado</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>Pendente</option>
                        <option value="APPROVED" {{ request('status') == 'APPROVED' ? 'selected' : '' }}>Aprovado</option>
                        <option value="REJECTED" {{ request('status') == 'REJECTED' ? 'selected' : '' }}>Rejeitado</option>
                        <option value="CONVERTED" {{ request('status') == 'CONVERTED' ? 'selected' : '' }}>Convertido em Enc.</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1" style="border-radius: 8px;">Filtrar</button>
                    <a href="{{ route('compras.pedidos.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">Limpar</a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th>Nº Pedido</th>
                        <th>Requerente</th>
                        <th>Departamento</th>
                        <th>Data</th>
                        <th>Estado</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        <td class="fw-bold text-primary">#REQ-{{ str_pad($req->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td class="fw-bold text-dark">{{ $req->requester_name }}</td>
                        <td>{{ $req->department ? $req->department->name : '-' }}</td>
                        <td>{{ $req->date->format('d/m/Y') }}</td>
                        <td>
                            @if($req->status === 'PENDING')
                                <span class="badge bg-warning text-dark border px-2 py-1"><i class="fas fa-clock me-1"></i> Pendente</span>
                            @elseif($req->status === 'APPROVED')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1"><i class="fas fa-check me-1"></i> Aprovado</span>
                            @elseif($req->status === 'REJECTED')
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2 py-1"><i class="fas fa-times me-1"></i> Rejeitado</span>
                            @elseif($req->status === 'CONVERTED')
                                <span class="badge bg-info bg-opacity-10 text-info border border-info px-2 py-1"><i class="fas fa-exchange-alt me-1"></i> Convertido</span>
                            @else
                                <span class="badge bg-secondary">{{ $req->status }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('compras.pedidos.show', $req->id) }}" class="btn btn-sm btn-light text-primary border" title="Ver Detalhes">
                                <i class="fas fa-eye"></i> Analisar
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-2x mb-3 d-block"></i>
                            Nenhum pedido interno encontrado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($requests->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $requests->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection

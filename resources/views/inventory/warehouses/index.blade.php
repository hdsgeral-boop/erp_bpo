@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        overflow: hidden;
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
    .btn-action {
        border-radius: 8px;
        padding: 0.4rem 0.8rem;
        transition: all 0.2s;
    }
    .btn-action:hover { background: #f1f5f9; transform: translateY(-2px); }
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
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-warehouse text-primary me-2"></i>Gestão de Armazéns</h2>
            <p class="text-muted mb-0">Controlo de locais físicos de armazenamento.</p>
        </div>
        <a href="{{ route('inventario.armazens.create') }}" class="btn btn-add-new">
            <i class="fas fa-plus me-2"></i> Novo Armazém
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm" style="border-radius: 10px; border-left: 4px solid #10b981;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger shadow-sm" style="border-radius: 10px; border-left: 4px solid #ef4444;">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card-premium">
        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th>Armazém</th>
                        <th>Localização / Morada</th>
                        <th>Estado</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($warehouses as $warehouse)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 rounded p-2 me-3 text-primary">
                                    <i class="fas fa-pallet fa-lg"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $warehouse->name }}</div>
                                    <small class="text-muted font-monospace">COD: {{ $warehouse->code ?? str_pad($warehouse->id, 4, '0', STR_PAD_LEFT) }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-muted"><i class="fas fa-map-marker-alt me-1"></i> {{ $warehouse->location ?: 'Não especificada' }}</span>
                        </td>
                        <td>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1">Ativo</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('inventario.armazens.show', $warehouse->id) }}" class="btn btn-sm btn-action text-info" title="Ver Stock">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('inventario.armazens.edit', $warehouse->id) }}" class="btn btn-sm btn-action text-primary" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="fas fa-warehouse fa-2x mb-3 d-block"></i>
                            Nenhum armazém encontrado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($warehouses->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $warehouses->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection

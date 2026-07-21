@extends('layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    .card-premium {
        background: #ffffff; border: none; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); padding: 1.5rem;
    }
    .table-custom { margin-bottom: 0; }
    .table-custom thead th { background-color: #f8fafc; color: #475569; font-weight: 600; font-size: 0.85rem; padding: 1rem 1.5rem; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; }
    .table-custom tbody td { padding: 1rem 1.5rem; vertical-align: middle; color: #1e293b; border-bottom: 1px solid #f1f5f9; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-project-diagram text-primary me-2"></i>Mapeamentos Contabilísticos</h2>
            <p class="text-muted mb-0">Configuração de contas automáticas para operações do ERP.</p>
        </div>
    </div>

    <div class="alert alert-info shadow-sm" style="border-radius: 10px;">
        <i class="fas fa-info-circle me-2"></i> Estes mapas são usados pelo motor central para integrar módulos (Vendas, Compras, Salários) de forma automática.
    </div>

    <div class="card-premium">
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Entidade do Sistema</th>
                        <th>Tipo de Operação</th>
                        <th>Conta Configurada</th>
                        <th>Movimento (D/C)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($maps as $map)
                    <tr>
                        <td class="fw-bold text-uppercase">{{ $map->entity_type }}</td>
                        <td>{{ $map->operation_type }}</td>
                        <td class="fw-bold">{{ $map->account_code }}</td>
                        <td>
                            @if($map->type_dc == 'D')
                                <span class="badge bg-info-subtle text-info border">DÉBITO</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border">CRÉDITO</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">Ainda não existem mapeamentos automáticos configurados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

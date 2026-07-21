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
        overflow: hidden;
    }
    .table-custom {
        margin-bottom: 0;
    }
    .table-custom thead th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 1rem 1.5rem;
        border-bottom: 2px solid #e2e8f0;
    }
    .table-custom tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-custom tbody tr:hover {
        background-color: #f8fafc;
    }
    .badge-custom {
        padding: 0.5em 1em;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.75rem;
    }
    .badge-customer { background: #dcfce7; color: #166534; }
    .badge-supplier { background: #dbeafe; color: #1e40af; }
    .btn-action {
        border-radius: 8px;
        padding: 0.4rem 0.8rem;
        transition: all 0.2s;
    }
    .btn-action:hover {
        background: #f1f5f9;
        transform: translateY(-2px);
    }
    .btn-add-new {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border-radius: 10px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
        transition: all 0.2s;
    }
    .btn-add-new:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4);
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-users text-primary me-2"></i>Entidades</h2>
            <p class="text-muted mb-0">Gestão de Clientes e Fornecedores.</p>
        </div>
        <a href="{{ route('entidades.create') }}" class="btn btn-add-new"><i class="fas fa-plus me-2"></i> Nova Entidade</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm" style="border-radius: 10px; border-left: 4px solid #10b981;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card-premium">
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Nome / Designação</th>
                        <th>NIF</th>
                        <th>Contacto</th>
                        <th>Tipo</th>
                        <th>Conta Contabilística</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($thirdParties as $tp)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div style="width: 40px; height: 40px; border-radius: 10px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #64748b; font-size: 1.2rem;">
                                    <i class="fas {{ $tp->type === 'CUSTOMER' ? 'fa-user' : 'fa-building' }}"></i>
                                </div>
                                <div>
                                    <strong style="color: #0f172a; display: block;">{{ $tp->name }}</strong>
                                    @if($tp->email)
                                        <small class="text-muted"><i class="fas fa-envelope me-1"></i>{{ $tp->email }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="font-monospace text-muted">{{ $tp->nif ?? '-' }}</td>
                        <td>
                            @if($tp->phone)
                                <span class="text-muted"><i class="fas fa-phone-alt me-1"></i>{{ $tp->phone }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($tp->type === 'CUSTOMER')
                                <span class="badge-custom badge-customer"><i class="fas fa-shopping-bag me-1"></i> Cliente</span>
                            @elseif($tp->type === 'SUPPLIER')
                                <span class="badge-custom badge-supplier"><i class="fas fa-truck me-1"></i> Fornecedor</span>
                            @else
                                <span class="badge-custom bg-secondary text-white">{{ $tp->type }}</span>
                            @endif
                        </td>
                        <td class="font-monospace text-muted">{{ $tp->account_code ?? '-' }}</td>
                        <td class="text-center">
                            <a href="{{ route('entidades.edit', $tp) }}" class="btn btn-action text-primary" title="Editar"><i class="fas fa-edit"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3" style="opacity: 0.5;"></i>
                            <h5 class="text-muted">Nenhuma entidade registada.</h5>
                            <a href="{{ route('entidades.create') }}" class="btn btn-outline-primary mt-2">Registar a Primeira</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

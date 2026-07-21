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
    }
    .table-custom { margin-bottom: 0; }
    .table-custom thead th {
        background-color: #f8fafc; color: #475569; font-weight: 600; font-size: 0.85rem; padding: 1rem 1.5rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0;
    }
    .table-custom tbody td { padding: 1rem 1.5rem; vertical-align: middle; color: #1e293b; border-bottom: 1px solid #f1f5f9; }
    .table-custom tbody tr:hover { background-color: #f8fafc; }
    .btn-add-new {
        background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; border-radius: 10px; padding: 0.6rem 1.5rem; font-weight: 600; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3); transition: all 0.2s;
    }
    .btn-add-new:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4); color: white; }
    .btn-action { border-radius: 8px; padding: 0.4rem 0.8rem; transition: all 0.2s; background: #f1f5f9; color: #475569; border: none; }
    .btn-action:hover { background: #e2e8f0; color: #1e293b; transform: translateY(-2px); }
    .badge-custom { padding: 0.5em 1em; border-radius: 6px; font-weight: 600; font-size: 0.75rem; }
    .badge-success-custom { background: #dcfce7; color: #166534; }
    .badge-danger-custom { background: #fee2e2; color: #991b1b; }
    .badge-secondary-custom { background: #f1f5f9; color: #475569; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-list-ul text-primary me-2"></i>Infotipos (Rubricas)</h2>
            <p class="text-muted mb-0">Gestão de rubricas de vencimentos e descontos.</p>
        </div>
        <a href="{{ route('rh.infotypes.create') }}" class="btn btn-add-new"><i class="fas fa-plus me-2"></i> Novo Infotipo</a>
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
                        <th>Nome da Rubrica</th>
                        <th>Natureza</th>
                        <th>Sujeito a INSS</th>
                        <th>Sujeito a IRT</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($infotypes as $infotype)
                    <tr>
                        <td class="fw-bold text-dark">{{ $infotype->name }}</td>
                        <td>
                            <span class="badge-custom {{ $infotype->type === 'VENCIMENTO' ? 'badge-success-custom' : 'badge-danger-custom' }}">
                                <i class="fas {{ $infotype->type === 'VENCIMENTO' ? 'fa-plus-circle' : 'fa-minus-circle' }} me-1"></i> {{ $infotype->type }}
                            </span>
                        </td>
                        <td>
                            @if($infotype->is_inss_base)
                                <span class="badge-custom badge-secondary-custom"><i class="fas fa-check text-success me-1"></i> Sim</span>
                            @else
                                <span class="badge-custom badge-secondary-custom"><i class="fas fa-times text-danger me-1"></i> Não</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-custom badge-secondary-custom">{{ $infotype->irt_type ?? 'Não' }}</span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('rh.infotypes.edit', $infotype) }}" class="btn btn-action"><i class="fas fa-edit"></i> Editar</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-folder-open fa-2x mb-3 d-block"></i>
                            Nenhum infotipo registado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    .info-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    .info-value {
        font-size: 1.05rem;
        color: #1e293b;
        font-weight: 500;
        margin-bottom: 1.5rem;
    }
    .btn-edit {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border-radius: 10px;
        padding: 0.6rem 2rem;
        font-weight: 600;
        border: none;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
    }
    .btn-edit:hover { color: white; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4); }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.companies.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
                <i class="fas fa-arrow-left me-1"></i> Voltar à Listagem
            </a>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-building text-primary me-2"></i>Detalhes da Empresa</h2>
        </div>
        <a href="{{ route('admin.companies.edit', $company->id) }}" class="btn btn-edit">
            <i class="fas fa-edit me-2"></i> Editar Dados
        </a>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card-premium p-4 text-center h-100 d-flex flex-column align-items-center justify-content-center">
                @if($company->logo)
                    <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo" class="img-fluid rounded mb-4" style="max-height: 150px;">
                @else
                    <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted mb-4 mx-auto" style="width: 150px; height: 150px;">
                        <i class="fas fa-image fa-3x"></i>
                    </div>
                @endif
                <h4 class="fw-bold text-dark">{{ $company->name }}</h4>
                <p class="text-muted mb-3">NIF: {{ $company->nif }}</p>
                
                @if($company->is_master_data)
                    <span class="badge bg-success" style="padding: 0.6em 1em; border-radius: 8px; font-size: 0.85rem;"><i class="fas fa-star me-1"></i> Empresa Matriz</span>
                @endif
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card-premium p-4 p-md-5 h-100">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-4">Informação Detalhada</h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-label">Nome de Registo</div>
                        <div class="info-value">{{ $company->name }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Número de Identificação Fiscal (NIF)</div>
                        <div class="info-value">{{ $company->nif }}</div>
                    </div>
                    
                    <div class="col-12 mt-3">
                        <h6 class="fw-bold text-muted border-bottom pb-2 mb-3">Localização e Endereço</h6>
                    </div>

                    <div class="col-md-4">
                        <div class="info-label">Província</div>
                        <div class="info-value">{{ $company->province ?: 'Não definido' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Município</div>
                        <div class="info-value">{{ $company->municipality ?: 'Não definido' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Comuna</div>
                        <div class="info-value">{{ $company->commune ?: 'Não definido' }}</div>
                    </div>

                    <div class="col-12 mt-3">
                        <h6 class="fw-bold text-muted border-bottom pb-2 mb-3">Informações de Sistema</h6>
                    </div>

                    <div class="col-md-4">
                        <div class="info-label">Data de Registo</div>
                        <div class="info-value">{{ $company->created_at ? $company->created_at->format('d/m/Y H:i') : 'N/D' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Última Atualização</div>
                        <div class="info-value">{{ $company->updated_at ? $company->updated_at->format('d/m/Y H:i') : 'N/D' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

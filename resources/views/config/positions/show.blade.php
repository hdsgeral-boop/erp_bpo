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
            <a href="{{ route('config.positions.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
                <i class="fas fa-arrow-left me-1"></i> Voltar à Listagem
            </a>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-id-badge text-primary me-2"></i>Detalhes do Cargo</h2>
        </div>
        <a href="{{ route('config.positions.edit', $position->id) }}" class="btn btn-edit">
            <i class="fas fa-edit me-2"></i> Editar Cargo
        </a>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card-premium p-4 text-center h-100 d-flex flex-column align-items-center justify-content-center">
                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                    <i class="fas fa-user-tag text-primary fa-2x"></i>
                </div>
                <h4 class="fw-bold text-dark">{{ $position->title }}</h4>
                <p class="text-muted mb-3">Cód: {{ $position->code }}</p>
                
                @if($position->is_management)
                    <span class="badge bg-warning text-dark px-3 py-2" style="border-radius: 8px;">
                        <i class="fas fa-star me-1"></i> Cargo de Chefia
                    </span>
                @else
                    <span class="badge bg-secondary px-3 py-2" style="border-radius: 8px;">
                        Cargo Operacional
                    </span>
                @endif
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card-premium p-4 p-md-5 h-100">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-4">Informação Organizacional</h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-label">Título do Cargo</div>
                        <div class="info-value">{{ $position->title }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Código (Referência)</div>
                        <div class="info-value">{{ $position->code }}</div>
                    </div>
                    
                    <div class="col-12 mt-2">
                        <div class="info-label">Descrição das Funções</div>
                        <div class="info-value">{{ $position->description ?: 'Sem descrição definida.' }}</div>
                    </div>

                    <div class="col-12 mt-3">
                        <h6 class="fw-bold text-muted border-bottom pb-2 mb-3">Enquadramento Estrutural</h6>
                    </div>

                    <div class="col-md-6">
                        <div class="info-label">Departamento</div>
                        <div class="info-value">
                            @if($position->department)
                                <a href="{{ route('config.departments.show', $position->department->id) }}" class="text-decoration-none">
                                    <i class="fas fa-sitemap me-1"></i> {{ $position->department->name }}
                                </a>
                            @else
                                <span class="badge bg-dark">Transversal (Sem departamento)</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

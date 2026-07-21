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
            <a href="{{ route('config.document-series.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
                <i class="fas fa-arrow-left me-1"></i> Voltar à Listagem
            </a>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-file-invoice text-primary me-2"></i>Detalhes da Série</h2>
        </div>
        <a href="{{ route('config.document-series.edit', $documentSeries->id) }}" class="btn btn-edit">
            <i class="fas fa-edit me-2"></i> Editar Série
        </a>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card-premium p-4 text-center h-100 d-flex flex-column align-items-center justify-content-center">
                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                    <i class="fas fa-barcode text-primary fa-2x"></i>
                </div>
                <h4 class="fw-bold text-dark">{{ $documentSeries->document_type }} {{ $documentSeries->identifier }}</h4>
                <p class="text-muted mb-3">Empresa: {{ $documentSeries->company->name }}</p>
                
                <div class="d-flex justify-content-center gap-2">
                    @if($documentSeries->is_active)
                        <span class="badge bg-success px-3 py-2" style="border-radius: 8px;">
                            <i class="fas fa-check-circle me-1"></i> Ativa
                        </span>
                    @else
                        <span class="badge bg-secondary px-3 py-2" style="border-radius: 8px;">
                            Inativa
                        </span>
                    @endif
                    
                    @if($documentSeries->is_default)
                        <span class="badge bg-primary px-3 py-2" style="border-radius: 8px;">
                            <i class="fas fa-star me-1"></i> Padrão
                        </span>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card-premium p-4 p-md-5 h-100">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-4">Informação da Numeração</h5>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-label">Tipo de Documento</div>
                        <div class="info-value"><span class="badge bg-light text-dark border">{{ $documentSeries->document_type }}</span></div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Identificador de Série</div>
                        <div class="info-value fw-bold text-primary">{{ $documentSeries->identifier }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Numerador Atual</div>
                        <div class="info-value">
                            <span class="badge" style="background-color: #f1f5f9; color: #475569; padding: 0.5em 0.8em; border-radius: 6px; font-family: monospace; font-size: 1.1rem;">
                                {{ str_pad($documentSeries->current_number, 4, '0', STR_PAD_LEFT) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="col-12 mt-2">
                        <div class="info-label">Próximo Documento (Pré-visualização)</div>
                        <div class="info-value mt-1">
                            <div class="p-3 bg-light rounded border border-primary d-inline-block font-monospace">
                                {{ $documentSeries->document_type }} {{ $documentSeries->identifier }}/{{ $documentSeries->current_number + 1 }}
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-3">
                        <div class="info-label">Descrição</div>
                        <div class="info-value">{{ $documentSeries->description ?: 'Sem descrição definida.' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

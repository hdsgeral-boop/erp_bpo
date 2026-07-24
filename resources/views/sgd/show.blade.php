@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="margin: 0; font-weight: 800; color: #0f172a; font-size: 1.6rem; letter-spacing: -0.5px;">
                <i class="fas fa-file-alt text-primary me-2"></i> Visualização de Documento (SGD)
            </h2>
            <p style="margin: 0; color: #64748b; font-size: 0.925rem;">
                Detalhes do ficheiro armazenado no Arquivo Digital.
            </p>
        </div>
        <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary fw-bold" style="border-radius: 10px;">
            <i class="fas fa-arrow-left me-1"></i> Voltar ao Arquivo
        </a>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
        <div class="card-body p-4 text-center py-5">
            <i class="fas fa-file-pdf text-danger mb-3" style="font-size: 3rem;"></i>
            <h5 class="fw-bold text-dark">Documento do Sistema</h5>
            <p class="text-muted fs-8">Utilize a lista documental para selecionar anexos de contratos, faturas ou ativos.</p>
        </div>
    </div>
</div>
@endsection

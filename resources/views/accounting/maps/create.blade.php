@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="margin: 0; font-weight: 800; color: #0f172a; font-size: 1.6rem; letter-spacing: -0.5px;">
                <i class="fas fa-project-diagram text-primary me-2"></i> Novo Mapeamento Contábil
            </h2>
            <p style="margin: 0; color: #64748b; font-size: 0.925rem;">
                Associe rubricas salariais e categorias às contas do Plano PGC.
            </p>
        </div>
        <a href="{{ route('contabilidade.maps.index') }}" class="btn btn-outline-secondary fw-bold" style="border-radius: 10px;">
            <i class="fas fa-arrow-left me-1"></i> Voltar
        </a>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
        <div class="card-body p-4">
            <form action="{{ route('contabilidade.maps.index') }}" method="GET">
                <div class="mb-3">
                    <label class="form-label fw-bold">Descrição da Rubrica</label>
                    <input type="text" class="form-control" placeholder="ex: Vencimento Base" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Conta PGC Débito</label>
                    <input type="text" class="form-control" placeholder="ex: 62.1.1" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Conta PGC Crédito</label>
                    <input type="text" class="form-control" placeholder="ex: 37.1.1" required>
                </div>
                <button type="submit" class="btn btn-primary fw-bold" style="border-radius: 10px;">Guardar Mapeamento</button>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="margin: 0; font-weight: 800; color: #0f172a; font-size: 1.6rem; letter-spacing: -0.5px;">
                <i class="fas fa-balance-scale text-primary me-2"></i> Revisão & Regularização de Inventário
            </h2>
            <p style="margin: 0; color: #64748b; font-size: 0.925rem;">
                Confronto entre contagem física e stock em sistema com acertos automáticos.
            </p>
        </div>
        <div>
            <a href="{{ route('logistica.inventario.index') }}" class="btn btn-outline-secondary fw-bold px-3 py-2" style="border-radius: 10px;">
                <i class="fas fa-arrow-left me-1"></i> Voltar às Sessões
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 16px; background: #fff;">
        <div class="card-body p-4 text-center py-5">
            <i class="fas fa-clipboard-check text-muted mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
            <h4 class="fw-bold text-dark mb-2">Revisão da Sessão de Inventário</h4>
            <p class="text-secondary mb-4" style="max-width: 500px; margin: 0 auto;">
                Selecione uma sessão de inventário ativa na lista de sessões para proceder à conciliação e acerto de stock.
            </p>
            <a href="{{ route('logistica.inventario.index') }}" class="btn btn-primary fw-bold px-4 py-2" style="border-radius: 10px;">
                Ver Sessões de Inventário
            </a>
        </div>
    </div>
</div>
@endsection

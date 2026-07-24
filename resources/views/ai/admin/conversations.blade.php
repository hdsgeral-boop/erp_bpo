@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="margin: 0; font-weight: 800; color: #0f172a; font-size: 1.6rem; letter-spacing: -0.5px;">
                <i class="fas fa-comments text-primary me-2"></i> Histórico de Conversas IA
            </h2>
            <p style="margin: 0; color: #64748b; font-size: 0.925rem;">
                Logs de interações entre os utilizadores e o Agente de IA.
            </p>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
        <div class="card-body p-4 text-center py-5">
            <i class="fas fa-history text-muted mb-3" style="font-size: 2.5rem; opacity: 0.4;"></i>
            <p class="text-muted font-semibold mb-0">Nenhuma conversa registada recentemente.</p>
        </div>
    </div>
</div>
@endsection

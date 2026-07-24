@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="margin: 0; font-weight: 800; color: #0f172a; font-size: 1.6rem; letter-spacing: -0.5px;">
                <i class="fas fa-server text-primary me-2"></i> Provedores de IA
            </h2>
            <p style="margin: 0; color: #64748b; font-size: 0.925rem;">
                Configuração de chaves de API para OpenAI, Google Gemini, Anthropic e Ollama local.
            </p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="p-3 bg-primary-subtle text-primary rounded-3"><i class="fab fa-google fs-3"></i></div>
                        <div>
                            <h5 class="fw-bold mb-0">Google Gemini</h5>
                            <span class="badge bg-success-subtle text-success">Ligado</span>
                        </div>
                    </div>
                    <p class="text-muted fs-8">Provedor padrão para análise de dados e relatórios.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="p-3 bg-success-subtle text-success rounded-3"><i class="fas fa-brain fs-3"></i></div>
                        <div>
                            <h5 class="fw-bold mb-0">OpenAI</h5>
                            <span class="badge bg-secondary-subtle text-secondary">Desligado</span>
                        </div>
                    </div>
                    <p class="text-muted fs-8">GPT-4o e GPT-3.5 Turbo.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

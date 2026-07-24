@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="margin: 0; font-weight: 800; color: #0f172a; font-size: 1.6rem; letter-spacing: -0.5px;">
                <i class="fas fa-microchip text-primary me-2"></i> Modelos de Inteligência Artificial
            </h2>
            <p style="margin: 0; color: #64748b; font-size: 0.925rem;">
                Modelos de LLM ativos e limites de tokens por requisição.
            </p>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
        <div class="card-body p-4">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light fs-8 text-uppercase">
                    <tr>
                        <th>Modelo</th>
                        <th>Provedor</th>
                        <th>Max Tokens</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold">gemini-1.5-pro</td>
                        <td>Google AI</td>
                        <td>1,000,000</td>
                        <td><span class="badge bg-success-subtle text-success">Ativo</span></td>
                    </tr>
                    <tr>
                        <td class="fw-bold">gpt-4o</td>
                        <td>OpenAI</td>
                        <td>128,000</td>
                        <td><span class="badge bg-secondary-subtle text-secondary">Inativo</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@push('styles')
<style>
    .card-premium { background: #ffffff; border: none; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); }
    .table-custom thead th { background-color: #ffffff; color: #475569; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; padding: 1rem 1.5rem; border-bottom: 2px solid #e2e8f0; }
    .table-custom tbody td { padding: 1rem 1.5rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-tools text-primary me-2"></i>Ferramentas de IA</h2>
            <p class="text-muted mb-0">Gestão das capacidades (Skills) que os Agentes de Inteligência Artificial podem utilizar.</p>
        </div>
        <div>
            <button class="btn btn-primary shadow-sm" style="border-radius: 8px;">
                <i class="fas fa-plus me-2"></i>Registar Ferramenta
            </button>
        </div>
    </div>

    <div class="card-premium">
        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th>Nome da Ferramenta</th>
                        <th>Descrição</th>
                        <th>Namespace / Classe PHP</th>
                        <th>Estado</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-tools fa-2x mb-3 d-block opacity-50"></i>
                            O módulo de Ferramentas Autónomas encontra-se em desenvolvimento (Fase de Integração de Agentes).
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

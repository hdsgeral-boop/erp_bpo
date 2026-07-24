@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-extrabold text-dark mb-1">
                <i class="fas fa-percent text-primary me-2"></i> Taxas Sociais de Segurança Social (INSS)
            </h2>
            <p class="text-muted small mb-0">Decreto Presidencial n.º 227/18 - Regime de Proteção Social Obrigatória dos Trabalhadores por Conta de Outrem em Angola.</p>
        </div>
        <a href="{{ route('rh.reports.inss') }}" class="btn btn-outline-primary fw-bold px-3 py-2" style="border-radius: 10px;">
            <i class="fas fa-file-excel me-1"></i> Ver Mapa de INSS
        </a>
    </div>

    <!-- Cards Grid -->
    <div class="row g-4 mb-4">
        <!-- Contribuição do Trabalhador -->
        <div class="col-md-4">
            <div class="card-premium p-4 text-center h-100 border-start border-4 border-primary">
                <span class="text-muted small fw-bold text-uppercase d-block mb-2">Desconto do Trabalhador</span>
                <div class="display-4 fw-extrabold text-primary mb-2">3,00 %</div>
                <div class="badge bg-primary-subtle text-primary px-3 py-1 fw-bold mb-3">Dedução Salarial</div>
                <p class="text-muted small mb-0">Retido na fonte sobre o vencimento ilíquido base e subsídios tributáveis do colaborador.</p>
            </div>
        </div>

        <!-- Contribuição da Empresa -->
        <div class="col-md-4">
            <div class="card-premium p-4 text-center h-100 border-start border-4 border-success">
                <span class="text-muted small fw-bold text-uppercase d-block mb-2">Encargo Patronal (Empresa)</span>
                <div class="display-4 fw-extrabold text-success mb-2">8,00 %</div>
                <div class="badge bg-success-subtle text-success px-3 py-1 fw-bold mb-3">Custo da Empresa</div>
                <p class="text-muted small mb-0">Suportado integralmente pela entidade patronal como encargo social adicional à folha.</p>
            </div>
        </div>

        <!-- Total da Segurança Social -->
        <div class="col-md-4">
            <div class="card-premium p-4 text-center h-100 border-start border-4 border-warning">
                <span class="text-muted small fw-bold text-uppercase d-block mb-2">Taxa Global de INSS</span>
                <div class="display-4 fw-extrabold text-dark mb-2">11,00 %</div>
                <div class="badge bg-warning-subtle text-warning-emphasis px-3 py-1 fw-bold mb-3">Total a Liquidar</div>
                <p class="text-muted small mb-0">Montante total entregue mensalmente ao Instituto Nacional de Segurança Social (INSS).</p>
            </div>
        </div>
    </div>

    <!-- Information Card -->
    <div class="card-premium p-4">
        <h5 class="fw-bold text-dark mb-3"><i class="fas fa-info-circle text-primary me-2"></i> Regras de Incidência do INSS em Angola</h5>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="p-3 bg-light rounded-3 border h-100">
                    <h6 class="fw-bold text-success"><i class="fas fa-check-circle me-1"></i> Remunerações Sujeitas a INSS</h6>
                    <ul class="small text-secondary mb-0 ps-3">
                        <li>Salário Base e diuturnidades.</li>
                        <li>Horas Extraordinárias e trabalho em dias de descanso.</li>
                        <li>Subsídio de Alimentação (na parcela que excede o limite isento de 30.000 Kz).</li>
                        <li>Subsídio de Transporte (na parcela que excede o limite isento de 30.000 Kz).</li>
                        <li>Comissões, prémios e bónus regulares de produtividade.</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 bg-light rounded-3 border h-100">
                    <h6 class="fw-bold text-danger"><i class="fas fa-times-circle me-1"></i> Remunerações Isentas de INSS</h6>
                    <ul class="small text-secondary mb-0 ps-3">
                        <li>Subsídio de Férias e Subsídio de Natal.</li>
                        <li>Abono de Família (dentro dos limites legais).</li>
                        <li>Abonos para falhas de caixa.</li>
                        <li>Indemnizações por cessação do contrato de trabalho.</li>
                        <li>Reembolso de despesas efetuadas em serviço da empresa.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

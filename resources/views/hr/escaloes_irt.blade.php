@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    .badge-exempt { background: #d1fae5; color: #047857; border: 1px solid #a7f3d0; }
    .badge-tax { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-extrabold text-dark mb-1">
                <i class="fas fa-layer-group text-primary me-2"></i> Tabela Oficial de Escalões do IRT (Angola)
            </h2>
            <p class="text-muted small mb-0">Código do Imposto sobre o Rendimento do Trabalho (Grupo A - Trabalho por Conta de Outrem).</p>
        </div>
        <a href="{{ route('rh.salarios.simulation') }}" class="btn btn-outline-primary fw-bold px-3 py-2" style="border-radius: 10px;">
            <i class="fas fa-calculator me-1"></i> Simular Cálculo IRT
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card-premium p-3">
                <span class="text-muted small fw-bold text-uppercase">Isenção de IRT</span>
                <h3 class="fw-bold text-success mb-0 mt-1">Até 100.000,00 Kz</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-premium p-3">
                <span class="text-muted small fw-bold text-uppercase">Taxa Mínima</span>
                <h3 class="fw-bold text-primary mb-0 mt-1">13,00 %</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-premium p-3">
                <span class="text-muted small fw-bold text-uppercase">Taxa Máxima</span>
                <h3 class="fw-bold text-danger mb-0 mt-1">25,00 %</h3>
            </div>
        </div>
    </div>

    <!-- IRT Table -->
    <div class="card-premium overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">Escalão</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">Rendimento Colectável (Kz)</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold text-end">Parcela Fixa (Kz)</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold text-center">Taxa Marginal</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold text-end">Excesso De (Kz)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="py-3 px-4 fw-bold text-dark">1.º Escalão</td>
                        <td class="py-3 px-4 fw-bold text-success">Até 100.000,00 Kz</td>
                        <td class="py-3 px-4 text-end">0,00 Kz</td>
                        <td class="py-3 px-4 text-center"><span class="badge badge-exempt px-3 py-1 fw-bold">ISENTO (0%)</span></td>
                        <td class="py-3 px-4 text-end text-muted">0,00 Kz</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 fw-bold text-dark">2.º Escalão</td>
                        <td class="py-3 px-4">De 100.001,00 a 150.000,00 Kz</td>
                        <td class="py-3 px-4 text-end">0,00 Kz</td>
                        <td class="py-3 px-4 text-center"><span class="badge badge-tax px-3 py-1 fw-bold">13 %</span></td>
                        <td class="py-3 px-4 text-end text-muted">100.000,00 Kz</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 fw-bold text-dark">3.º Escalão</td>
                        <td class="py-3 px-4">De 150.001,00 a 200.000,00 Kz</td>
                        <td class="py-3 px-4 text-end fw-semibold">6.500,00 Kz</td>
                        <td class="py-3 px-4 text-center"><span class="badge badge-tax px-3 py-1 fw-bold">16 %</span></td>
                        <td class="py-3 px-4 text-end text-muted">150.000,00 Kz</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 fw-bold text-dark">4.º Escalão</td>
                        <td class="py-3 px-4">De 200.001,00 a 300.000,00 Kz</td>
                        <td class="py-3 px-4 text-end fw-semibold">14.500,00 Kz</td>
                        <td class="py-3 px-4 text-center"><span class="badge badge-tax px-3 py-1 fw-bold">18 %</span></td>
                        <td class="py-3 px-4 text-end text-muted">200.000,00 Kz</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 fw-bold text-dark">5.º Escalão</td>
                        <td class="py-3 px-4">De 300.001,00 a 500.000,00 Kz</td>
                        <td class="py-3 px-4 text-end fw-semibold">32.500,00 Kz</td>
                        <td class="py-3 px-4 text-center"><span class="badge badge-tax px-3 py-1 fw-bold">19 %</span></td>
                        <td class="py-3 px-4 text-end text-muted">300.000,00 Kz</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 fw-bold text-dark">6.º Escalão</td>
                        <td class="py-3 px-4">De 500.001,00 a 1.000.000,00 Kz</td>
                        <td class="py-3 px-4 text-end fw-semibold">70.500,00 Kz</td>
                        <td class="py-3 px-4 text-center"><span class="badge badge-tax px-3 py-1 fw-bold">20 %</span></td>
                        <td class="py-3 px-4 text-end text-muted">500.000,00 Kz</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 fw-bold text-dark">7.º Escalão</td>
                        <td class="py-3 px-4">De 1.000.001,00 a 1.500.000,00 Kz</td>
                        <td class="py-3 px-4 text-end fw-semibold">170.500,00 Kz</td>
                        <td class="py-3 px-4 text-center"><span class="badge badge-tax px-3 py-1 fw-bold">21 %</span></td>
                        <td class="py-3 px-4 text-end text-muted">1.000.000,00 Kz</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 fw-bold text-dark">8.º Escalão</td>
                        <td class="py-3 px-4">De 1.500.001,00 a 2.500.000,00 Kz</td>
                        <td class="py-3 px-4 text-end fw-semibold">275.500,00 Kz</td>
                        <td class="py-3 px-4 text-center"><span class="badge badge-tax px-3 py-1 fw-bold">22 %</span></td>
                        <td class="py-3 px-4 text-end text-muted">1.500.000,00 Kz</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 fw-bold text-dark">9.º Escalão</td>
                        <td class="py-3 px-4">De 2.500.001,00 a 5.000.000,00 Kz</td>
                        <td class="py-3 px-4 text-end fw-semibold">495.500,00 Kz</td>
                        <td class="py-3 px-4 text-center"><span class="badge badge-tax px-3 py-1 fw-bold">23 %</span></td>
                        <td class="py-3 px-4 text-end text-muted">2.500.000,00 Kz</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 fw-bold text-dark">10.º Escalão</td>
                        <td class="py-3 px-4">De 5.000.001,00 a 10.000.000,00 Kz</td>
                        <td class="py-3 px-4 text-end fw-semibold">1.070.500,00 Kz</td>
                        <td class="py-3 px-4 text-center"><span class="badge badge-tax px-3 py-1 fw-bold">24 %</span></td>
                        <td class="py-3 px-4 text-end text-muted">5.000.000,00 Kz</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 fw-bold text-dark">13.º Escalão</td>
                        <td class="py-3 px-4 fw-bold text-primary">Superior a 10.000.000,00 Kz</td>
                        <td class="py-3 px-4 text-end fw-semibold">2.270.500,00 Kz</td>
                        <td class="py-3 px-4 text-center"><span class="badge bg-danger-subtle text-danger border px-3 py-1 fw-bold">25 %</span></td>
                        <td class="py-3 px-4 text-end text-muted">10.000.000,00 Kz</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    .infotype-badge {
        font-family: monospace;
        font-weight: 800;
        font-size: 0.85rem;
        padding: 6px 12px;
        border-radius: 8px;
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-extrabold text-dark mb-1">
                <i class="fas fa-list-ul text-primary me-2"></i> Infotipos & Estrutura de Ficha de RH
            </h2>
            <p class="text-muted small mb-0">Catálogo de sub-estruturas de dados de colaboradores (Infotypes padrão ERP/SAP adaptados à legislação laboral angolana).</p>
        </div>
        <a href="{{ route('rh.funcionarios.index') }}" class="btn btn-outline-primary fw-bold px-3 py-2" style="border-radius: 10px;">
            <i class="fas fa-user-friends me-1"></i> Ver Colaboradores
        </a>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card-premium p-3">
                <span class="text-muted small fw-bold text-uppercase">Infotipos Ativos</span>
                <h3 class="fw-bold text-dark mb-0 mt-1">8 Estruturas</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-premium p-3">
                <span class="text-muted small fw-bold text-uppercase">Conformidade Legal</span>
                <h3 class="fw-bold text-success mb-0 mt-1">Lei Geral do Trabalho</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-premium p-3">
                <span class="text-muted small fw-bold text-uppercase">Segurança de Dados</span>
                <h3 class="fw-bold text-primary mb-0 mt-1">Encriptado (AES-256)</h3>
            </div>
        </div>
    </div>

    <!-- Infotypes Table -->
    <div class="card-premium overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold" style="width: 120px;">Código</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">Nome do Registo (Infotipo)</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">Descrição & Campos Abrangidos</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold text-center">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="py-3 px-4"><span class="infotype-badge">IT 0001</span></td>
                        <td class="py-3 px-4 fw-bold text-dark"><i class="fas fa-building text-primary me-2"></i> Atribuição Organizacional</td>
                        <td class="py-3 px-4 text-muted small">Empresa, Departamento, Centro de Custo, Cargo e Função no Sistema.</td>
                        <td class="py-3 px-4 text-center"><span class="badge bg-success-subtle text-success border px-2 py-1">Ativo</span></td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4"><span class="infotype-badge">IT 0002</span></td>
                        <td class="py-3 px-4 fw-bold text-dark"><i class="fas fa-id-card text-primary me-2"></i> Dados Pessoais & NIF/INSS</td>
                        <td class="py-3 px-4 text-muted small">Nome completo, NIF (encriptado), N.º de Segurança Social INSS (encriptado), Estado Civil e Gênero.</td>
                        <td class="py-3 px-4 text-center"><span class="badge bg-success-subtle text-success border px-2 py-1">Ativo</span></td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4"><span class="infotype-badge">IT 0006</span></td>
                        <td class="py-3 px-4 fw-bold text-dark"><i class="fas fa-map-marker-alt text-primary me-2"></i> Endereço & Contactos</td>
                        <td class="py-3 px-4 text-muted small">Morada residencial, Município, Província, E-mail profissional e Contacto Telefónico.</td>
                        <td class="py-3 px-4 text-center"><span class="badge bg-success-subtle text-success border px-2 py-1">Ativo</span></td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4"><span class="infotype-badge">IT 0008</span></td>
                        <td class="py-3 px-4 fw-bold text-dark"><i class="fas fa-money-bill-wave text-primary me-2"></i> Remuneração Base</td>
                        <td class="py-3 px-4 text-muted small">Vencimento ilíquido base mensalisado, regime fiscal de IRT e tipo de contrato laboral.</td>
                        <td class="py-3 px-4 text-center"><span class="badge bg-success-subtle text-success border px-2 py-1">Ativo</span></td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4"><span class="infotype-badge">IT 0009</span></td>
                        <td class="py-3 px-4 fw-bold text-dark"><i class="fas fa-university text-primary me-2"></i> Dados Bancários & IBAN</td>
                        <td class="py-3 px-4 text-muted small">Nome do Banco comercial, IBAN (encriptado) para exportação de ficheiros de pagamento PS2.</td>
                        <td class="py-3 px-4 text-center"><span class="badge bg-success-subtle text-success border px-2 py-1">Ativo</span></td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4"><span class="infotype-badge">IT 0014</span></td>
                        <td class="py-3 px-4 fw-bold text-dark"><i class="fas fa-gift text-primary me-2"></i> Subsídios & Benefícios Regulares</td>
                        <td class="py-3 px-4 text-muted small">Subsídio de Alimentação, Subsídio de Transporte, Abono de Família e isenções legais.</td>
                        <td class="py-3 px-4 text-center"><span class="badge bg-success-subtle text-success border px-2 py-1">Ativo</span></td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4"><span class="infotype-badge">IT 0015</span></td>
                        <td class="py-3 px-4 fw-bold text-dark"><i class="fas fa-stopwatch text-primary me-2"></i> Horas Extraordinárias</td>
                        <td class="py-3 px-4 text-muted small">Registo de trabalho suplementar diurno (50%) e noturno/dias de descanso (100%).</td>
                        <td class="py-3 px-4 text-center"><span class="badge bg-success-subtle text-success border px-2 py-1">Ativo</span></td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4"><span class="infotype-badge">IT 0021</span></td>
                        <td class="py-3 px-4 fw-bold text-dark"><i class="fas fa-users text-primary me-2"></i> Agregado Familiar & Dependentes</td>
                        <td class="py-3 px-4 text-muted small">Número de dependentes a cargo para efeitos de benefícios e deduções fiscais.</td>
                        <td class="py-3 px-4 text-center"><span class="badge bg-success-subtle text-success border px-2 py-1">Ativo</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

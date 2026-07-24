@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="margin: 0; font-weight: 800; color: #0f172a; font-size: 1.6rem; letter-spacing: -0.5px;">
                <i class="fas fa-credit-card text-primary me-2"></i> Subscrição & Licenciamento de Empresas
            </h2>
            <p style="margin: 0; color: #64748b; font-size: 0.925rem;">
                Escolha o plano ideal para a sua empresa. Preços em Kwanzas (AOA) com faturação certificada AGT.
            </p>
        </div>
        <div>
            <a href="{{ route('billing.history') }}" class="btn btn-outline-secondary fw-bold px-3 py-2" style="border-radius: 10px;">
                <i class="fas fa-history me-1"></i> Histórico de Pagamentos
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; background: #fef2f2; color: #991b1b;">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; background: #dcfce7; color: #15803d;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Banner Estado da Licença Atual da Empresa -->
    @php
        $days = $company->remaining_days;
        $isTrial = $company->subscription_status === 'trial';
        $isActive = $company->isLicenseActive();
    @endphp

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: {{ $isActive ? ($days <= 5 ? '#fffbeb' : '#f0fdf4') : '#fef2f2' }}; border: 1px solid {{ $isActive ? ($days <= 5 ? '#fde68a' : '#bbf7d0') : '#fecaca' }} !important;">
        <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width: 52px; height: 52px; border-radius: 14px; background: {{ $isActive ? ($days <= 5 ? '#fef3c7' : '#dcfce7') : '#fee2e2' }}; color: {{ $isActive ? ($days <= 5 ? '#d97706' : '#15803d') : '#dc2626' }}; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fas {{ $isActive ? ($days <= 5 ? 'fa-clock' : 'fa-check-circle') : 'fa-lock' }}"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1" style="color: #0f172a;">
                        Estado Atual: 
                        @if($isActive)
                            <span class="badge {{ $days <= 5 ? 'bg-warning text-dark' : 'bg-success' }} px-3 py-2" style="border-radius: 8px;">
                                {{ $isTrial ? 'Trial de 30 Dias Ativo' : 'Licença Paga Ativa' }}
                            </span>
                        @else
                            <span class="badge bg-danger px-3 py-2" style="border-radius: 8px;">Licença Expirada / Bloqueada</span>
                        @endif
                    </h5>
                    <p class="mb-0 text-muted fs-7">
                        Empresa: <strong>{{ $company->name }}</strong> (NIF: {{ $company->nif ?? 'N/A' }}) &bull; 
                        Validade até: <strong>{{ $company->effective_expiration_date->format('d/m/Y H:i') }}</strong> 
                        (<strong>{{ $days }} dias restantes</strong>)
                    </p>
                </div>
            </div>
            <div>
                <a href="#tabela-planos" class="btn btn-primary fw-bold px-4 py-2" style="border-radius: 10px; background: #2563eb;">
                    <i class="fas fa-shopping-cart me-1"></i> Renovar / Alterar Plano
                </a>
            </div>
        </div>
    </div>

    <!-- Tabela dos 3 Planos -->
    <div id="tabela-planos" class="row g-4 mb-5">
        @foreach($plans as $plan)
            @php
                $isFeatured = $plan->code === 'pro';
            @endphp
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100 position-relative" style="border-radius: 20px; background: #ffffff; {{ $isFeatured ? 'border: 2px solid #dc2626 !important; box-shadow: 0 15px 30px rgba(220,38,38,0.12) !important;' : 'border: 1px solid #e2e8f0 !important;' }}">
                    @if($isFeatured)
                        <span style="position: absolute; top: -14px; right: 20px; background: #dc2626; color: #fff; font-weight: 800; font-size: 0.75rem; padding: 0.3rem 0.9rem; border-radius: 20px; text-transform: uppercase;">
                            Recomendado AGT
                        </span>
                    @endif

                    <div class="card-body p-4 d-flex flex-column">
                        <h4 class="fw-bold text-dark mb-1">{{ $plan->name }}</h4>
                        <span class="text-muted fs-8 mb-3">
                            {{ $plan->code === 'start' ? 'Ideal para Micro e Pequenas Empresas (PME).' : ($plan->code === 'pro' ? 'Para empresas em expansão com RH e Contabilidade.' : 'Para Grupos Empresariais com Multi-Empresa e IA.') }}
                        </span>

                        <div class="my-3">
                            <span style="font-size: 2.2rem; font-weight: 800; color: {{ $isFeatured ? '#dc2626' : '#0f172a' }};">
                                {{ number_format($plan->price_monthly, 0, ',', '.') }}
                            </span>
                            <span class="fs-6 text-muted fw-bold">Kz / mês</span>
                        </div>

                        <hr class="my-3 border-light">

                        <ul class="list-unstyled grow fs-7 text-secondary mb-4">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> {{ $plan->max_users == -1 ? 'Utilizadores Ilimitados' : 'Até ' . $plan->max_users . ' Utilizadores' }}</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Vendas, POS & Faturação AGT</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Emissão SAF-T AO Instantânea</li>
                            <li class="mb-2"><i class="fas fa-check {{ $plan->features['payroll'] ?? false ? 'text-success' : 'text-muted opacity-50' }} me-2"></i> Recursos Humanos & Salários (IRT/INSS)</li>
                            <li class="mb-2"><i class="fas fa-check {{ $plan->features['accounting'] ?? false ? 'text-success' : 'text-muted opacity-50' }} me-2"></i> Contabilidade PGC & Tesouraria</li>
                            <li class="mb-2"><i class="fas fa-check {{ $plan->features['powerbi'] ?? false ? 'text-success' : 'text-muted opacity-50' }} me-2"></i> Conector Direto PowerBI Direct</li>
                            <li class="mb-2"><i class="fas fa-check {{ $plan->features['ai'] ?? false ? 'text-success' : 'text-muted opacity-50' }} me-2"></i> Agente de IA Integrado</li>
                        </ul>

                        <a href="{{ route('billing.checkout', $plan->id) }}" class="btn {{ $isFeatured ? 'btn-danger' : 'btn-outline-dark' }} w-100 py-3 fw-bold" style="border-radius: 12px; {{ $isFeatured ? 'background: #dc2626;' : '' }}">
                            Subscrever {{ $plan->name }}
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Matriz Comparativa Detalhada -->
    <div class="card border-0 shadow-sm" style="border-radius: 20px; background: #ffffff;">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
            <h4 class="fw-bold text-dark mb-1"><i class="fas fa-list-alt text-primary me-2"></i> Matriz Comparativa de Funcionalidades</h4>
            <p class="text-muted fs-8 mb-0">Veja em detalhe o que está incluído em cada nível de subscrição.</p>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead class="table-light fs-8 text-uppercase">
                        <tr>
                            <th class="text-start">Funcionalidade / Recurso</th>
                            <th>Start (5.000 Kz)</th>
                            <th>Pro (8.500 Kz)</th>
                            <th>Enterprise (12.799 Kz)</th>
                        </tr>
                    </thead>
                    <tbody class="fs-7">
                        <tr>
                            <td class="text-start fw-bold text-dark">N.º de Utilizadores Incluídos</td>
                            <td>3 Utilizadores</td>
                            <td>10 Utilizadores</td>
                            <td><span class="badge bg-success-subtle text-success fw-bold">Ilimitados</span></td>
                        </tr>
                        <tr>
                            <td class="text-start fw-bold text-dark">Faturação Certificada AGT & SAF-T AO</td>
                            <td><i class="fas fa-check text-success fs-5"></i></td>
                            <td><i class="fas fa-check text-success fs-5"></i></td>
                            <td><i class="fas fa-check text-success fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="text-start fw-bold text-dark">Frente de Caixa (POS Balcão)</td>
                            <td><i class="fas fa-check text-success fs-5"></i></td>
                            <td><i class="fas fa-check text-success fs-5"></i></td>
                            <td><i class="fas fa-check text-success fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="text-start fw-bold text-dark">Processamento Salarial IRT & INSS</td>
                            <td><i class="fas fa-times text-muted opacity-50 fs-5"></i></td>
                            <td><i class="fas fa-check text-success fs-5"></i></td>
                            <td><i class="fas fa-check text-success fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="text-start fw-bold text-dark">Contabilidade PGC & Balancete</td>
                            <td><i class="fas fa-times text-muted opacity-50 fs-5"></i></td>
                            <td><i class="fas fa-check text-success fs-5"></i></td>
                            <td><i class="fas fa-check text-success fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="text-start fw-bold text-dark">Reconciliação Bancária & Tesouraria</td>
                            <td><i class="fas fa-times text-muted opacity-50 fs-5"></i></td>
                            <td><i class="fas fa-check text-success fs-5"></i></td>
                            <td><i class="fas fa-check text-success fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="text-start fw-bold text-dark">Conector PowerBI OData / JSON Direct</td>
                            <td><i class="fas fa-times text-muted opacity-50 fs-5"></i></td>
                            <td><i class="fas fa-times text-muted opacity-50 fs-5"></i></td>
                            <td><i class="fas fa-check text-success fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="text-start fw-bold text-dark">Gestão Multi-Empresa Nativa</td>
                            <td><i class="fas fa-times text-muted opacity-50 fs-5"></i></td>
                            <td><i class="fas fa-times text-muted opacity-50 fs-5"></i></td>
                            <td><i class="fas fa-check text-success fs-5"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.website')

@section('title', 'ERP Consulvolt — Sistema Integrado de Gestão Empresarial | Angola')

@section('styles')
<style>
    .hero-section {
        background: linear-gradient(rgba(9, 13, 22, 0.88), rgba(15, 23, 42, 0.92)), url('https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1920&q=80');
        background-size: cover;
        background-position: center;
        color: #ffffff;
        padding: 6rem 0 7rem;
        position: relative;
    }

    .hero-badge {
        display: inline-block;
        background: rgba(0, 88, 230, 0.25);
        border: 1px solid rgba(0, 88, 230, 0.4);
        color: #93c5fd;
        font-weight: 700;
        font-size: 0.85rem;
        padding: 0.4rem 1rem;
        border-radius: 50px;
        margin-bottom: 1.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .hero-title {
        font-size: 3.2rem;
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 1.5rem;
        letter-spacing: -1px;
    }

    .hero-description {
        font-size: 1.15rem;
        color: #cbd5e1;
        max-width: 680px;
        margin-bottom: 2.5rem;
        line-height: 1.6;
    }

    .callout-banner {
        background: linear-gradient(135deg, #0058E6, #0047b3);
        color: #ffffff;
        padding: 2.2rem 0;
        box-shadow: 0 10px 25px rgba(0, 88, 230, 0.25);
    }

    .module-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        padding: 2rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        height: 100%;
    }

    .module-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 30px rgba(0, 88, 230, 0.08);
        border-color: var(--primary-blue);
    }

    .module-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: var(--primary-blue);
    }

    .module-icon {
        width: 60px;
        height: 60px;
        border-radius: 14px;
        background: #eff6ff;
        color: var(--primary-blue);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        margin-bottom: 1.5rem;
    }

    .dark-impact-banner {
        background: linear-gradient(135deg, #090d16 0%, #0f172a 100%);
        color: #ffffff;
        padding: 6rem 0;
        position: relative;
        overflow: hidden;
    }

    .pricing-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        padding: 2.5rem 2rem;
        transition: all 0.3s;
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .pricing-card.featured {
        border: 2px solid var(--primary-blue);
        box-shadow: 0 15px 35px rgba(0, 88, 230, 0.15);
        transform: scale(1.03);
    }

    .pricing-badge {
        position: absolute;
        top: -15px;
        right: 25px;
        background: var(--primary-blue);
        color: #ffffff;
        font-weight: 800;
        font-size: 0.75rem;
        padding: 0.3rem 0.9rem;
        border-radius: 20px;
        text-transform: uppercase;
    }

    .price-val {
        font-size: 2.4rem;
        font-weight: 800;
        color: var(--dark-navy);
    }
</style>
@endsection

@section('content')
<!-- HERO SECTION -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="hero-badge"><i class="fas fa-shield-alt me-1"></i> Software Certificado AGT n.º 142/AGT/2019</span>
                <h1 class="hero-title">
                    Soluções de Gestão Empresarial e Tecnologia com 10 Anos de Experiência em Angola.
                </h1>
                <p class="hero-description">
                    Consulvolt Soluções — Venda de materiais elétricos pesados e equipamentos informáticos, consultoria organizacional, gestão de projetos e desenvolvimento de software ERP certificado com faturação AGT, RH e Contabilidade PGC.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="btn btn-blue-primary btn-lg px-4 py-3">
                        <i class="fas fa-rocket me-2"></i> Começar Agora Gratuitamente
                    </a>
                    <a href="{{ route('website.about') }}" class="btn btn-outline-light btn-lg px-4 py-3 fw-bold" style="border-radius: 10px;">
                        <i class="fas fa-file-alt me-2"></i> Carta de Apresentação
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- HIGHLIGHT CALLOUT BANNER -->
<section class="callout-banner">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-lightbulb me-2"></i> Precisa de uma solução tecnológica ou materiais para a sua empresa?</h4>
            <p class="mb-0 text-white-50 fs-7">A Consulvolt Soluções desenvolve aplicações à medida e oferece consultoria organizacional em Angola.</p>
        </div>
        <a href="{{ route('website.contact') }}" class="btn btn-light text-primary fw-bold px-4 py-2 text-nowrap" style="border-radius: 10px; color: var(--primary-blue) !important;">
            Falar com a Consulvolt
        </a>
    </div>
</section>

<!-- 4 PILARES DA CONSULVOLT SOLUÇÕES -->
<section class="py-5" style="background: #ffffff;">
    <div class="container py-4">
        <div class="text-center max-w-2xl mx-auto mb-5">
            <span class="text-primary fw-bold text-uppercase fs-7" style="color: var(--primary-blue) !important;">10 Anos de Experiência</span>
            <h2 class="fw-extrabold text-dark fs-2 mt-1">Serviços da Consulvolt Soluções</h2>
            <p class="text-muted">A nossa atuação concentra-se em quatro pilares estratégicos para o desenvolvimento empresarial em Angola.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="module-card">
                    <div class="module-icon"><i class="fas fa-plug"></i></div>
                    <h5 class="fw-bold text-dark mb-2">Materiais Elétricos & Informáticos</h5>
                    <p class="text-secondary fs-7 mb-3">Venda de materiais elétricos pesados, computadores, servidores e consumíveis para empresas.</p>
                    <a href="{{ route('website.services') }}" class="text-primary fw-bold text-decoration-none fs-7" style="color: var(--primary-blue) !important;">Saber mais &rarr;</a>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="module-card">
                    <div class="module-icon"><i class="fas fa-chart-line"></i></div>
                    <h5 class="fw-bold text-dark mb-2">Consultoria Organizacional</h5>
                    <p class="text-secondary fs-7 mb-3">Parceria contínua para otimização de fluxos de trabalho, produtividade e controlo interno.</p>
                    <a href="{{ route('website.services') }}" class="text-primary fw-bold text-decoration-none fs-7" style="color: var(--primary-blue) !important;">Saber mais &rarr;</a>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="module-card">
                    <div class="module-icon"><i class="fas fa-laptop-code"></i></div>
                    <h5 class="fw-bold text-dark mb-2">Desenvolvimento de Aplicações</h5>
                    <p class="text-secondary fs-7 mb-3">Criação de softwares sob medida e integração com o sistema ERP certificado pela AGT.</p>
                    <a href="{{ route('website.services') }}" class="text-primary fw-bold text-decoration-none fs-7" style="color: var(--primary-blue) !important;">Saber mais &rarr;</a>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="module-card">
                    <div class="module-icon"><i class="fas fa-tasks"></i></div>
                    <h5 class="fw-bold text-dark mb-2">Gestão de Projetos</h5>
                    <p class="text-secondary fs-7 mb-3">Suporte técnico em todas as etapas, assegurando entrega no prazo e cumprimento de orçamentos.</p>
                    <a href="{{ route('website.services') }}" class="text-primary fw-bold text-decoration-none fs-7" style="color: var(--primary-blue) !important;">Saber mais &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MÓDULOS INTEGRADOS ERP -->
<section class="py-5" style="background: #f8fafc;">
    <div class="container py-4">
        <div class="text-center max-w-2xl mx-auto mb-5">
            <span class="text-primary fw-bold text-uppercase fs-7" style="color: var(--primary-blue) !important;">Software ERP Certificado</span>
            <h2 class="fw-extrabold text-dark fs-2 mt-1">Módulos do ERP Consulvolt</h2>
            <p class="text-muted">Software homologado com gestão de faturação AGT, salários e contabilidade PGC.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="module-card">
                    <div class="module-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                    <h4 class="fw-bold text-dark mb-3">Vendas, POS & Faturação AGT</h4>
                    <p class="text-secondary fs-7 mb-4">
                        Emissão de Faturas (FT, FR, OR, PP, NC, ND, GT) com numeração sequencial travada e geração automática do ficheiro SAF-T AO.
                    </p>
                    <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none fs-7" style="color: var(--primary-blue) !important;">Explorar Vendas &rarr;</a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="module-card">
                    <div class="module-icon"><i class="fas fa-users-cog"></i></div>
                    <h4 class="fw-bold text-dark mb-3">Recursos Humanos & Salários</h4>
                    <p class="text-secondary fs-7 mb-4">
                        Cálculo exato de IRT (tabela 2026), INSS (3% + 8%), emissão de recibos PDF e mapa de transferências bancárias PS2.
                    </p>
                    <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none fs-7" style="color: var(--primary-blue) !important;">Explorar RH & Salários &rarr;</a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="module-card">
                    <div class="module-icon"><i class="fas fa-book"></i></div>
                    <h4 class="fw-bold text-dark mb-3">Contabilidade PGC & Tesouraria</h4>
                    <p class="text-secondary fs-7 mb-4">
                        Plano de Contas PGC completo, lançamentos nos diários, Balancete de Verificação, Reconciliação Bancária e Ativos Fixos.
                    </p>
                    <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none fs-7" style="color: var(--primary-blue) !important;">Explorar Contabilidade &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PRICING SECTION -->
<section class="py-5" style="background: #ffffff;">
    <div class="container py-4">
        <div class="text-center max-w-2xl mx-auto mb-5">
            <span class="text-primary fw-bold text-uppercase fs-7" style="color: var(--primary-blue) !important;">Planos & Licenciamento</span>
            <h2 class="fw-extrabold text-dark fs-2 mt-1">Planos de Subscrição ERP</h2>
            <p class="text-muted">Valores transparentes em Kwanzas (AOA) com suporte local em Luanda.</p>
        </div>

        <div class="row g-4 align-items-center">
            <!-- Plan 1 -->
            <div class="col-lg-4">
                <div class="pricing-card">
                    <h4 class="fw-bold text-dark mb-2">Plano Start</h4>
                    <p class="text-muted fs-7 mb-4">Ideal para Micro e Pequenas Empresas (PME).</p>
                    <div class="mb-4">
                        <span class="price-val">5.000</span> <span class="fs-6 text-muted fw-bold">Kz / mês</span>
                    </div>
                    <ul class="list-unstyled fs-7 text-secondary mb-4">
                        <li class="mb-2"><i class="fas fa-check text-primary me-2" style="color: var(--primary-blue) !important;"></i> Até 3 Utilizadores</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2" style="color: var(--primary-blue) !important;"></i> Módulo Vendas & POS</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2" style="color: var(--primary-blue) !important;"></i> Emissão SAF-T AGT</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2" style="color: var(--primary-blue) !important;"></i> Gestão de Stock Básico</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn btn-outline-blue w-100 py-3">Adquirir Plano Start</a>
                </div>
            </div>

            <!-- Plan 2 (Featured) -->
            <div class="col-lg-4">
                <div class="pricing-card featured">
                    <span class="pricing-badge">Mais Popular</span>
                    <h4 class="fw-bold text-dark mb-2">Plano Pro / Growth</h4>
                    <p class="text-muted fs-7 mb-4">Para empresas em rápida expansão.</p>
                    <div class="mb-4">
                        <span class="price-val text-primary" style="color: var(--primary-blue) !important;">8.500</span> <span class="fs-6 text-muted fw-bold">Kz / mês</span>
                    </div>
                    <ul class="list-unstyled fs-7 text-secondary mb-4">
                        <li class="mb-2"><i class="fas fa-check text-primary me-2" style="color: var(--primary-blue) !important;"></i> Até 10 Utilizadores</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2" style="color: var(--primary-blue) !important;"></i> Vendas, POS & Faturação AGT</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2" style="color: var(--primary-blue) !important;"></i> RH & Processamento Salarial</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2" style="color: var(--primary-blue) !important;"></i> Contabilidade & Tesouraria</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2" style="color: var(--primary-blue) !important;"></i> Suporte Prioritário</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn btn-blue-primary w-100 py-3">Adquirir Plano Pro</a>
                </div>
            </div>

            <!-- Plan 3 -->
            <div class="col-lg-4">
                <div class="pricing-card">
                    <h4 class="fw-bold text-dark mb-2">Plano Enterprise</h4>
                    <p class="text-muted fs-7 mb-4">Para Grupos Empresariais e Holdings.</p>
                    <div class="mb-4">
                        <span class="price-val">12.799</span> <span class="fs-6 text-muted fw-bold">Kz / mês</span>
                    </div>
                    <ul class="list-unstyled fs-7 text-secondary mb-4">
                        <li class="mb-2"><i class="fas fa-check text-primary me-2" style="color: var(--primary-blue) !important;"></i> Utilizadores Ilimitados</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2" style="color: var(--primary-blue) !important;"></i> Gestão Multi-Empresa Nativa</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2" style="color: var(--primary-blue) !important;"></i> Conector PowerBI Direct</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2" style="color: var(--primary-blue) !important;"></i> Agente de IA Dedicado</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn btn-outline-blue w-100 py-3">Adquirir Plano Enterprise</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

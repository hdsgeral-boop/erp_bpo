@extends('layouts.website')

@section('title', 'Serviços & Módulos ERP — Consulvolt Soluções')

@section('content')
<!-- Hero Header -->
<section style="background: linear-gradient(135deg, #090d16 0%, #0f172a 100%); padding: 5rem 0 4rem; color: #ffffff;">
    <div class="container">
        <div class="max-w-3xl">
            <span class="badge bg-primary text-white fw-bold text-uppercase px-3 py-2 rounded-pill mb-3" style="background-color: var(--primary-blue) !important; font-size: 0.8rem;">
                <i class="fas fa-cogs me-1"></i> Soluções Empresariais Integradas
            </span>
            <h1 class="fw-extrabold display-4 mb-3 text-white">
                Serviços & Tecnologia ERP
            </h1>
            <p class="lead text-slate-300 fs-5 mb-0" style="max-width: 750px;">
                Descubra a gama completa de serviços e módulos tecnológicos disponibilizados pela Consulvolt Soluções para impulsionar a produtividade e a conformidade fiscal da sua empresa.
            </p>
        </div>
    </div>
</section>

<!-- Módulos ERP Consulvolt -->
<section class="py-5" style="background: #ffffff;">
    <div class="container py-4">
        <div class="text-center max-w-2xl mx-auto mb-5">
            <span class="text-primary fw-bold text-uppercase fs-7" style="color: var(--primary-blue) !important;">Plataforma ERP Consulvolt</span>
            <h2 class="fw-extrabold text-dark fs-2 mt-1">Módulos Integrados de Gestão</h2>
            <p class="text-muted">Tudo o que a sua empresa precisa para operar com segurança fiscal em Angola.</p>
        </div>

        <div class="row g-4 mb-5">
            <!-- Módulo 1 -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm p-4" style="border-radius: 20px; border-top: 4px solid var(--primary-blue) !important;">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="p-3 rounded-3 text-white" style="background: var(--primary-blue); font-size: 1.5rem;"><i class="fas fa-file-invoice-dollar"></i></div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">Vendas, POS & Faturação AGT</h5>
                            <small class="text-primary font-monospace fw-bold" style="color: var(--primary-blue) !important;">Certificado n.º 142/AGT/2019</small>
                        </div>
                    </div>
                    <p class="text-secondary fs-7 mb-4">
                        Emissão instantânea de FT, FR, OR, PP, NC, ND e GT com assinatura digital RSA 1024-bit, controlo rigoroso de séries documentais e exportação do ficheiro SAF-T AO XML.
                    </p>
                    <ul class="list-unstyled text-slate-600 fs-7 mb-4">
                        <li class="mb-2"><i class="fas fa-check text-primary me-2" style="color: var(--primary-blue) !important;"></i> Faturação de Balcão (POS) rápido</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2" style="color: var(--primary-blue) !important;"></i> Gestão de Retenções de Fonte na Fonte</li>
                        <li class="mb-0"><i class="fas fa-check text-primary me-2" style="color: var(--primary-blue) !important;"></i> Validação automática de NIF angolano</li>
                    </ul>
                </div>
            </div>

            <!-- Módulo 2 -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm p-4" style="border-radius: 20px; border-top: 4px solid var(--primary-blue) !important;">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="p-3 rounded-3 text-white" style="background: var(--primary-blue); font-size: 1.5rem;"><i class="fas fa-users-cog"></i></div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">Recursos Humanos & Salários</h5>
                            <small class="text-primary font-monospace fw-bold" style="color: var(--primary-blue) !important;">Tabela IRT 2026 & INSS</small>
                        </div>
                    </div>
                    <p class="text-secondary fs-7 mb-4">
                        Processamento salarial automatizado com cálculo da tabela progressiva de IRT, contribuições para o INSS (3%/8%), recibos de vencimento em PDF e ficheiro bancário PS2.
                    </p>
                    <ul class="list-unstyled text-slate-600 fs-7 mb-4">
                        <li class="mb-2"><i class="fas fa-check text-primary me-2" style="color: var(--primary-blue) !important;"></i> Emissão em lote de Recibos em PDF</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2" style="color: var(--primary-blue) !important;"></i> Relatórios mensais para liquidação do INSS</li>
                        <li class="mb-0"><i class="fas fa-check text-primary me-2" style="color: var(--primary-blue) !important;"></i> Controlo de ausências, subsídios e horas extra</li>
                    </ul>
                </div>
            </div>

            <!-- Módulo 3 -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm p-4" style="border-radius: 20px; border-top: 4px solid var(--primary-blue) !important;">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="p-3 rounded-3 text-white" style="background: var(--primary-blue); font-size: 1.5rem;"><i class="fas fa-book"></i></div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">Contabilidade PGC & Tesouraria</h5>
                            <small class="text-primary font-monospace fw-bold" style="color: var(--primary-blue) !important;">Plano PGC Angolano</small>
                        </div>
                    </div>
                    <p class="text-secondary fs-7 mb-4">
                        Integração completa com o Plano Geral de Contabilidade (PGC), balancetes de verificação, diários de lançamentos, gestão de caixas/bancos e depreciações de imobilizados.
                    </p>
                    <ul class="list-unstyled text-slate-600 fs-7 mb-4">
                        <li class="mb-2"><i class="fas fa-check text-primary me-2" style="color: var(--primary-blue) !important;"></i> Balanço e Demonstração de Resultados</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2" style="color: var(--primary-blue) !important;"></i> Reconciliação bancária de extratos</li>
                        <li class="mb-0"><i class="fas fa-check text-primary me-2" style="color: var(--primary-blue) !important;"></i> Ativos Fixos com mapa de amortizações</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Serviços Prestados pela Consulvolt Soluções -->
<section class="py-5" style="background: #f8fafc;">
    <div class="container py-4">
        <div class="text-center max-w-2xl mx-auto mb-5">
            <span class="text-primary fw-bold text-uppercase fs-7" style="color: var(--primary-blue) !important;">Portefólio de Serviços</span>
            <h2 class="fw-extrabold text-dark fs-2 mt-1">Soluções Especializadas Consulvolt</h2>
            <p class="text-muted">Serviços corporativos desenhados para a realidade do mercado angolano.</p>
        </div>

        <div class="row g-4">
            <!-- Serviço 1 -->
            <div class="col-lg-6">
                <div class="p-4 bg-white rounded-4 shadow-sm h-100 d-flex gap-4 align-items-start">
                    <div class="p-3 rounded-4 text-white flex-shrink-0" style="background: var(--primary-blue); font-size: 1.8rem;">
                        <i class="fas fa-plug"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-2">Venda de Materiais Elétricos Pesados e Equipamentos Informáticos</h4>
                        <p class="text-secondary fs-6 mb-0">
                            Fornecemos uma ampla gama de produtos de alta qualidade, desde materiais elétricos de alta/média tensão a computadores, servidores, consumíveis e equipamentos de infraestrutura tecnológica.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Serviço 2 -->
            <div class="col-lg-6">
                <div class="p-4 bg-white rounded-4 shadow-sm h-100 d-flex gap-4 align-items-start">
                    <div class="p-3 rounded-4 text-white flex-shrink-0" style="background: var(--primary-blue); font-size: 1.8rem;">
                        <i class="fas fa-sitemap"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-2">Consultoria Organizacional</h4>
                        <p class="text-secondary fs-6 mb-0">
                            Trabalhamos em estreita parceria com os nossos clientes para otimizar fluxos de trabalho, reestruturar organigramas, aumentar a produtividade e aprimorar os controlos financeiros internos.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Serviço 3 -->
            <div class="col-lg-6">
                <div class="p-4 bg-white rounded-4 shadow-sm h-100 d-flex gap-4 align-items-start">
                    <div class="p-3 rounded-4 text-white flex-shrink-0" style="background: var(--primary-blue); font-size: 1.8rem;">
                        <i class="fas fa-code"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-2">Desenvolvimento de Aplicações Tecnológicas</h4>
                        <p class="text-secondary fs-6 mb-0">
                            Desenvolvemos softwares e aplicações sob medida com foco na automação de processos, integração de sistemas Legados e acompanhamento em tempo real das métricas da empresa.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Serviço 4 -->
            <div class="col-lg-6">
                <div class="p-4 bg-white rounded-4 shadow-sm h-100 d-flex gap-4 align-items-start">
                    <div class="p-3 rounded-4 text-white flex-shrink-0" style="background: var(--primary-blue); font-size: 1.8rem;">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-2">Gestão de Projetos Empresariais</h4>
                        <p class="text-secondary fs-6 mb-0">
                            Oferecemos suporte técnico e de consultoria em todas as etapas dos seus projetos, assegurando o cumprimento rigoroso dos prazos, controlo de custos e entrega de resultados.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer Banner CTA -->
<section class="py-5 text-center text-white" style="background: var(--dark-navy);">
    <div class="container py-3">
        <h3 class="fw-bold mb-3 text-white">Pronto para começar a utilizar o ERP Consulvolt?</h3>
        <p class="lead text-slate-300 mb-4" style="max-width: 650px; margin: 0 auto;">Crie a sua conta de demonstração em menos de 2 minutos ou solicite a visita de um consultor.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('register') }}" class="btn btn-blue-primary btn-lg px-4">
                <i class="fas fa-rocket me-2"></i> Criar Conta Gratuita
            </a>
            <a href="{{ route('website.contact') }}" class="btn btn-outline-light btn-lg px-4" style="border-radius: 10px;">
                <i class="fas fa-envelope me-2"></i> Contactar Consultor
            </a>
        </div>
    </div>
</section>
@endsection

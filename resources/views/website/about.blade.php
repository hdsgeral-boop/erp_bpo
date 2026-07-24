@extends('layouts.website')

@section('title', 'Sobre Nós — Consulvolt Soluções (10 Anos de Experiência em Angola)')

@section('content')
<!-- Hero Header -->
<section style="background: linear-gradient(135deg, #090d16 0%, #0f172a 100%); padding: 5rem 0 4rem; color: #ffffff; position: relative;">
    <div class="container">
        <div class="max-w-3xl">
            <span class="badge bg-primary text-white fw-bold text-uppercase px-3 py-2 rounded-pill mb-3" style="background-color: var(--primary-blue) !important; font-size: 0.8rem;">
                <i class="fas fa-award me-1"></i> 10 Anos no Mercado Angolano
            </span>
            <h1 class="fw-extrabold display-4 mb-3 text-white" style="line-height: 1.2;">
                Consulvolt Soluções
            </h1>
            <p class="lead text-slate-300 fs-5 mb-0" style="max-width: 750px;">
                Soluções integradas de engenharia elétrica, equipamentos informáticos, consultoria organizacional e desenvolvimento tecnológico para acelerar o crescimento e o controlo interno das empresas em Angola.
            </p>
        </div>
    </div>
</section>

<!-- Carta de Apresentação Section -->
<section class="py-5" style="background: #ffffff;">
    <div class="container py-4">
        <div class="row g-5 align-items-center">
            <div class="col-lg-7">
                <span class="text-primary fw-bold text-uppercase fs-7" style="color: var(--primary-blue) !important;">Carta de Apresentação</span>
                <h2 class="fw-extrabold text-dark fs-2 mb-4 mt-1">Compromisso com a Eficiência, Inovação e Controlo Interno</h2>
                
                <div class="p-4 rounded-4 mb-4" style="background: #f8fafc; border-left: 4px solid var(--primary-blue);">
                    <p class="text-dark fs-6 fw-semibold mb-0" style="line-height: 1.7;">
                        "É com grande satisfação que apresentamos a <strong>Consulvolt Soluções</strong>, uma empresa com 10 anos de experiência consolidada no mercado angolano. A nossa atuação concentra-se na venda de materiais elétricos pesados, equipamentos informáticos e consumíveis, consultoria organizacional, desenvolvimento de aplicações tecnológicas para melhoria de processos e controlo interno, além da gestão integral de projetos."
                    </p>
                </div>

                <p class="text-secondary fs-6 mb-4" style="line-height: 1.7;">
                    Temos nos dedicado continuamente a oferecer soluções que não apenas atendem às necessidades imediatas dos nossos clientes, mas que também promovem a eficiência operacional, a inovação e a sustentabilidade das suas operações. A nossa equipa é composta por profissionais altamente qualificados, prontos para oferecer atendimento personalizado e criar soluções adaptadas a cada desafio do tecido empresarial angolano.
                </p>

                <div class="row g-3 pt-2">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background: #f1f5f9;">
                            <div class="fs-3 text-primary" style="color: var(--primary-blue) !important;"><i class="fas fa-building"></i></div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">NIF Registado</h6>
                                <small class="text-muted">5417213969</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background: #f1f5f9;">
                            <div class="fs-3 text-primary" style="color: var(--primary-blue) !important;"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Sede Principal</h6>
                                <small class="text-muted">Lar Patriota, Luanda</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-lg overflow-hidden" style="border-radius: 24px;">
                    <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=800&q=80" alt="Consulvolt Equipa" class="img-fluid">
                    <div class="card-body p-4 text-white" style="background: var(--dark-navy);">
                        <h5 class="fw-bold mb-2">Porquê Escolher a Consulvolt?</h5>
                        <ul class="list-unstyled mb-0 text-slate-300 fs-7">
                            <li class="mb-2"><i class="fas fa-check-circle text-primary me-2" style="color: #60a5fa !important;"></i> Experiência comprovada de 10 anos em Angola</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-primary me-2" style="color: #60a5fa !important;"></i> Soluções 100% adaptadas à legislação nacional</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-primary me-2" style="color: #60a5fa !important;"></i> Software ERP Certificado pela AGT (n.º 142/AGT/2019)</li>
                            <li class="mb-0"><i class="fas fa-check-circle text-primary me-2" style="color: #60a5fa !important;"></i> Suporte técnico local dedicado e especializado</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Os 4 Pilares da Consulvolt -->
<section class="py-5" style="background: #f8fafc;">
    <div class="container py-4">
        <div class="text-center max-w-2xl mx-auto mb-5">
            <span class="text-primary fw-bold text-uppercase fs-7" style="color: var(--primary-blue) !important;">Áreas de Atuação</span>
            <h2 class="fw-extrabold text-dark fs-2 mt-1">Serviços e Especialidades Institucionais</h2>
            <p class="text-muted">Conheça o portefólio completo de serviços prestados pela Consulvolt Soluções.</p>
        </div>

        <div class="row g-4">
            <!-- Pilar 1 -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm p-4 text-center" style="border-radius: 18px; transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-6px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center text-white rounded-4" style="width: 60px; height: 60px; background: linear-gradient(135deg, #0058E6, #2563eb); font-size: 1.5rem;">
                        <i class="fas fa-plug"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Materiais Elétricos & Informáticos</h5>
                    <p class="text-muted fs-7 mb-0">Fornecimento de equipamentos pesados de eletricidade, infraestrutura de rede, computadores e consumíveis de alta qualidade.</p>
                </div>
            </div>

            <!-- Pilar 2 -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm p-4 text-center" style="border-radius: 18px; transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-6px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center text-white rounded-4" style="width: 60px; height: 60px; background: linear-gradient(135deg, #0058E6, #2563eb); font-size: 1.5rem;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Consultoria Organizacional</h5>
                    <p class="text-muted fs-7 mb-0">Trabalhamos em parceria com a sua empresa para otimizar processos, aumentar a produtividade e aprimorar a gestão interna.</p>
                </div>
            </div>

            <!-- Pilar 3 -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm p-4 text-center" style="border-radius: 18px; transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-6px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center text-white rounded-4" style="width: 60px; height: 60px; background: linear-gradient(135deg, #0058E6, #2563eb); font-size: 1.5rem;">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Aplicações Tecnológicas & ERP</h5>
                    <p class="text-muted fs-7 mb-0">Criação de soluções digitais sob medida visando a automação de processos, faturação fiscal AGT e controlo interno apurado.</p>
                </div>
            </div>

            <!-- Pilar 4 -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm p-4 text-center" style="border-radius: 18px; transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-6px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center text-white rounded-4" style="width: 60px; height: 60px; background: linear-gradient(135deg, #0058E6, #2563eb); font-size: 1.5rem;">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Gestão de Projetos</h5>
                    <p class="text-muted fs-7 mb-0">Suporte integral em todas as etapas dos seus projetos, garantindo a entrega dentro dos prazos e orçamentos estipulados.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to action -->
<section class="py-5 text-center text-white" style="background: linear-gradient(135deg, #0058E6, #0047b3);">
    <div class="container py-3">
        <h3 class="fw-bold mb-3">Pronto para transformar as operações da sua empresa?</h3>
        <p class="lead text-white-50 mb-4" style="max-width: 650px; margin: 0 auto;">Entre em contacto com os nossos consultores e agende uma apresentação personalizada.</p>
        <a href="{{ route('website.contact') }}" class="btn btn-light text-primary fw-bold btn-lg px-5" style="border-radius: 12px; color: var(--primary-blue) !important;">
            Falar com a Consulvolt <i class="fas fa-arrow-right ms-2"></i>
        </a>
    </div>
</section>
@endsection

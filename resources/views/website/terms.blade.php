@extends('layouts.website')

@section('title', 'Termos & Certificação AGT — Consulvolt Soluções')

@section('content')
<!-- Hero Header -->
<section style="background: linear-gradient(135deg, #090d16 0%, #0f172a 100%); padding: 5rem 0 4rem; color: #ffffff;">
    <div class="container">
        <div class="max-w-3xl">
            <span class="badge bg-primary text-white fw-bold text-uppercase px-3 py-2 rounded-pill mb-3" style="background-color: var(--primary-blue) !important; font-size: 0.8rem;">
                <i class="fas fa-shield-alt me-1"></i> Conformidade & Legislação Angolana
            </span>
            <h1 class="fw-extrabold display-4 mb-3 text-white">
                Termos & Certificação AGT
            </h1>
            <p class="lead text-slate-300 fs-5 mb-0" style="max-width: 750px;">
                Informações de conformidade fiscal, políticas de segurança de dados e termos de utilização do ERP da Consulvolt Soluções.
            </p>
        </div>
    </div>
</section>

<section class="py-5" style="background: #ffffff;">
    <div class="container py-4">
        <div class="row g-5">
            <div class="col-lg-8">
                <h3 class="fw-bold text-dark mb-3">1. Certificação do Software de Faturação</h3>
                <p class="text-secondary fs-6 mb-4" style="line-height: 1.7;">
                    O ERP Consulvolt é um software de gestão empresarial certificado pela <strong>Administração Geral Tributária (AGT)</strong> sob o número de certificado <strong>142/AGT/2019</strong>. Todas as faturas, notas de crédito, notas de débito, recibos e guias emitidos cumprem rigorosamente com os requisitos legais de assinatura digital RSA 1024-bit e encriptação de Hash de validação.
                </p>

                <h3 class="fw-bold text-dark mb-3">2. Proteção e Privacidade dos Dados Empresariais</h3>
                <p class="text-secondary fs-6 mb-4" style="line-height: 1.7;">
                    A <strong>Consulvolt Soluções</strong> compromete-se a proteger a confidencialidade e a privacidade de todos os dados registados no ERP por parte dos seus clientes. As informações financeiras, fiscais, salariais e de stock são mantidas sob encriptação e acesso restrito, não sendo partilhadas com terceiros exceto sob obrigação legal estipulada pela legislação angolana.
                </p>

                <h3 class="fw-bold text-dark mb-3">3. Licenciamento e Utilização do ERP</h3>
                <p class="text-secondary fs-6 mb-4" style="line-height: 1.7;">
                    O acesso ao ERP Consulvolt é concedido através de licenças modulares. Cada empresa contratante possui isolamento completo de dados de *multi-tenant*, garantindo que apenas os utilizadores devidamente autorizados pela empresa possam aceder e gerir as suas informações operacionais.
                </p>

                <h3 class="fw-bold text-dark mb-3">4. Ficheiro SAF-T AO XML</h3>
                <p class="text-secondary fs-6 mb-4" style="line-height: 1.7;">
                    O sistema permite a geração mensal automatizada do ficheiro SAF-T AO (Standard Audit File for Tax Purposes - Angola) de acordo com a estrutura exigida pela AGT, facilitando a submissão fiscal do Imposto sobre o Valor Acrescentado (IVA) e impostos de selo.
                </p>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; background: #f8fafc; border-top: 4px solid var(--primary-blue) !important;">
                    <h5 class="fw-bold text-dark mb-3"><i class="fas fa-info-circle text-primary me-2" style="color: var(--primary-blue) !important;"></i> Identificação Fiscal</h5>
                    <ul class="list-unstyled text-slate-700 fs-7 mb-0">
                        <li class="mb-2"><strong>Razão Social:</strong> Consulvolt Soluções</li>
                        <li class="mb-2"><strong>NIF:</strong> 5417213969</li>
                        <li class="mb-2"><strong>Certificado AGT:</strong> n.º 142/AGT/2019</li>
                        <li class="mb-2"><strong>Sede:</strong> Lar Patriota, Luanda — Angola</li>
                        <li class="mb-2"><strong>Email:</strong> hdsgeral@gmail.com</li>
                        <li class="mb-0"><strong>Telefones:</strong> (244) 923 692 943 / 923 012 143</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

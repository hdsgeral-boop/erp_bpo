@extends('layouts.website')

@section('title', 'Contactos — Consulvolt Soluções | NIF: 5417213969')

@section('content')
<!-- Hero Header -->
<section style="background: linear-gradient(135deg, #090d16 0%, #0f172a 100%); padding: 5rem 0 4rem; color: #ffffff;">
    <div class="container">
        <div class="max-w-3xl">
            <span class="badge bg-primary text-white fw-bold text-uppercase px-3 py-2 rounded-pill mb-3" style="background-color: var(--primary-blue) !important; font-size: 0.8rem;">
                <i class="fas fa-headset me-1"></i> Atendimento Comercial & Suporte
            </span>
            <h1 class="fw-extrabold display-4 mb-3 text-white">
                Fale com a Consulvolt Soluções
            </h1>
            <p class="lead text-slate-300 fs-5 mb-0" style="max-width: 750px;">
                Estamos prontos para responder às suas dúvidas, agendar demonstrações do ERP Consulvolt ou apresentar propostas para materiais elétricos e consultoria.
            </p>
        </div>
    </div>
</section>

<section class="py-5" style="background: #ffffff;">
    <div class="container py-4">
        <div class="row g-5">
            <!-- Left Info Column -->
            <div class="col-lg-5">
                <span class="text-primary fw-bold text-uppercase fs-7" style="color: var(--primary-blue) !important;">Contactos Institucionais</span>
                <h2 class="fw-extrabold text-dark fs-2 mb-4 mt-1">Dados da Empresa</h2>
                
                <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 18px; background: #f8fafc; border-left: 4px solid var(--primary-blue) !important;">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="p-3 rounded-3 text-white fs-4" style="background: var(--primary-blue);"><i class="fas fa-id-card"></i></div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Contribuinte / NIF</h6>
                            <span class="text-primary fw-extrabold fs-5" style="color: var(--primary-blue) !important;">5417213969</span>
                        </div>
                    </div>
                    <p class="text-muted fs-7 mb-0">Empresa de Direito Angolano registada para fornecimento de equipamentos, tecnologia e serviços de consultoria.</p>
                </div>

                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="p-3 rounded-3 text-white fs-5 flex-shrink-0" style="background: var(--dark-navy);"><i class="fas fa-map-marker-alt"></i></div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Sede & Morada</h6>
                        <p class="text-secondary fs-6 mb-0">Angola - Luanda, Lar Patriota, Rua Ginásio Wanaka</p>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="p-3 rounded-3 text-white fs-5 flex-shrink-0" style="background: var(--dark-navy);"><i class="fas fa-envelope"></i></div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Correio Eletrónico</h6>
                        <a href="mailto:hdsgeral@gmail.com" class="text-primary fw-bold fs-6 text-decoration-none" style="color: var(--primary-blue) !important;">hdsgeral@gmail.com</a>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="p-3 rounded-3 text-white fs-5 flex-shrink-0" style="background: var(--dark-navy);"><i class="fas fa-phone-alt"></i></div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Telefones de Contacto</h6>
                        <p class="text-secondary fs-6 mb-0">
                            <strong>(244) 923 692 943</strong><br>
                            <strong>(244) 923 012 143</strong>
                        </p>
                    </div>
                </div>

                <div class="p-3 rounded-3 bg-light border d-flex align-items-center gap-3">
                    <div class="text-success fs-3"><i class="fab fa-whatsapp"></i></div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">WhatsApp Comercial</h6>
                        <a href="https://wa.me/244923692943" target="_blank" class="text-success fw-bold text-decoration-none fs-7">Iniciar conversa imediata &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Right Form Column -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-lg p-4 p-md-5" style="border-radius: 24px; background: #ffffff;">
                    <h3 class="fw-extrabold text-dark mb-2">Enviar Mensagem</h3>
                    <p class="text-muted fs-7 mb-4">Preencha os seus dados para agendar uma demonstração do ERP ou solicitar um orçamento de materiais/consultoria.</p>

                    @if(session('success'))
                        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4" style="background-color: #ecfdf5; color: #065f46;">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4" style="background-color: #fef2f2; color: #991b1b;">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('contact.submit') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark fs-7">Nome Completo <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control py-3" style="border-radius: 12px;" placeholder="Ex: Pascoal Paulo" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark fs-7">Endereço de E-mail <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control py-3" style="border-radius: 12px;" placeholder="exemplo@empresa.co.ao" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark fs-7">Telefone / WhatsApp</label>
                                <input type="text" name="phone" class="form-control py-3" style="border-radius: 12px;" placeholder="(244) 923 000 000">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark fs-7">Nome da Empresa</label>
                            <input type="text" name="company_name" class="form-control py-3" style="border-radius: 12px;" placeholder="Sua Empresa Lda">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark fs-7">Mensagem ou Assunto <span class="text-danger">*</span></label>
                            <textarea name="message" rows="4" class="form-control p-3" style="border-radius: 12px;" placeholder="Descreva brevemente o motivo do seu contacto..." required></textarea>
                        </div>

                        <button type="submit" class="btn btn-blue-primary w-100 py-3 fs-6">
                            <i class="fas fa-paper-plane me-2"></i> Enviar Mensagem
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

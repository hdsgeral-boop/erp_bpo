@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="margin: 0; font-weight: 800; color: #0f172a; font-size: 1.6rem; letter-spacing: -0.5px;">
                <i class="fas fa-shopping-cart text-primary me-2"></i> Checkout de Pagamento de Licença
            </h2>
            <p style="margin: 0; color: #64748b; font-size: 0.925rem;">
                Selecione a forma de pagamento conveniente para ativar a subscrição da sua empresa.
            </p>
        </div>
        <a href="{{ route('billing.plans') }}" class="btn btn-outline-secondary fw-bold" style="border-radius: 10px;">
            <i class="fas fa-arrow-left me-1"></i> Alterar Plano
        </a>
    </div>

    <div class="row g-4">
        <!-- Resumo da Ordem -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm" style="border-radius: 20px; background: #ffffff;">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-1">Resumo da Subscrição</h5>
                    <span class="text-muted fs-8">Faturação Comercial emitida segundo a AGT</span>
                </div>
                <div class="card-body p-4">
                    <div class="p-3 bg-light rounded-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-dark">{{ $plan->name }}</span>
                            <span class="badge bg-danger px-3 py-1">30 Dias</span>
                        </div>
                        <div class="fs-8 text-muted">Empresa: <strong>{{ $company->name }}</strong></div>
                        <div class="fs-8 text-muted">NIF: <strong>{{ $company->nif ?? 'N/A' }}</strong></div>
                    </div>

                    <div class="d-flex justify-content-between fs-7 mb-2">
                        <span class="text-muted">Valor da Licença (Incidência):</span>
                        <span class="fw-bold text-dark">{{ number_format($plan->price_monthly, 2, ',', '.') }} Kz</span>
                    </div>
                    <div class="d-flex justify-content-between fs-7 mb-3">
                        <span class="text-muted">Imposto (IVA 14%):</span>
                        <span class="fw-bold text-dark">{{ number_format($plan->price_monthly * 0.14, 2, ',', '.') }} Kz</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fs-5 fw-bold mb-4">
                        <span class="text-dark">Total a Pagar:</span>
                        <span class="text-danger">{{ number_format($plan->price_monthly * 1.14, 2, ',', '.') }} Kz</span>
                    </div>

                    <div class="alert alert-info border-0 fs-8 mb-0" style="border-radius: 12px; background: #eff6ff; color: #1e40af;">
                        <i class="fas fa-file-invoice me-1"></i> A fatura fiscal comercial será emitida automaticamente e disponibilizada para download em PDF.
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulário e Métodos de Pagamento -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm" style="border-radius: 20px; background: #ffffff;">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-1">Método de Pagamento</h5>
                    <span class="text-muted fs-8">Escolha a sua opção preferida</span>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('billing.store_payment') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                        <input type="hidden" name="reference_code" value="{{ $referenceCode }}">
                        <input type="hidden" name="entidade" value="{{ $entidade }}">
                        <input type="hidden" name="referencia" value="{{ $referencia }}">

                        <!-- Nav Tabs Método -->
                        <div class="nav nav-pills nav-fill mb-4 p-1 bg-light rounded-3" id="paymentTabs" role="tablist">
                            <button class="nav-link active fw-bold py-2" id="tab-ref" data-bs-toggle="tab" data-bs-target="#method-ref" type="button" onclick="document.getElementById('methodInput').value='multicaixa_ref';">
                                <i class="fas fa-barcode me-1"></i> Referência Multicaixa
                            </button>
                            <button class="nav-link fw-bold py-2" id="tab-express" data-bs-toggle="tab" data-bs-target="#method-express" type="button" onclick="document.getElementById('methodInput').value='express';">
                                <i class="fas fa-mobile-alt me-1"></i> Multicaixa Express
                            </button>
                            <button class="nav-link fw-bold py-2" id="tab-transfer" data-bs-toggle="tab" data-bs-target="#method-transfer" type="button" onclick="document.getElementById('methodInput').value='transfer';">
                                <i class="fas fa-university me-1"></i> Transferência Bancária
                            </button>
                        </div>

                        <input type="hidden" name="payment_method" id="methodInput" value="multicaixa_ref">

                        <div class="tab-content" id="paymentTabsContent">
                            <!-- 1. REFERÊNCIA MULTICAIXA -->
                            <div class="tab-pane fade show active p-3 border rounded-3 bg-light" id="method-ref">
                                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-info-circle text-primary me-2"></i> Pagamento por Referência Multicaixa</h6>
                                <p class="text-muted fs-8 mb-3">Efetue o pagamento em qualquer caixa Multicaixa ou no seu Multicaixa Express na opção <strong>Pagamentos por Referência</strong>.</p>
                                
                                <div class="row g-3 text-center mb-3">
                                    <div class="col-md-6">
                                        <div class="p-3 bg-white border rounded-3">
                                            <div class="text-muted fs-8">Entidade:</div>
                                            <div class="fw-bold fs-4 text-dark font-monospace">{{ $entidade }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-white border rounded-3">
                                            <div class="text-muted fs-8">Referência:</div>
                                            <div class="fw-bold fs-4 text-danger font-monospace">{{ $referencia }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center text-success fw-bold fs-8">
                                    <i class="fas fa-bolt me-1"></i> Licença ativada instantaneamente após o pagamento.
                                </div>
                            </div>

                            <!-- 2. MULTICAIXA EXPRESS DIRETO -->
                            <div class="tab-pane fade p-3 border rounded-3 bg-light" id="method-express">
                                <h6 class="fw-bold text-dark mb-2"><i class="fas fa-mobile-alt text-primary me-2"></i> Multicaixa Express Direto</h6>
                                <p class="text-muted fs-8 mb-3">Insira o seu número de telemóvel associado ao Multicaixa Express para receber a notificação push de pagamento.</p>

                                <div class="mb-3">
                                    <label class="form-label fw-bold fs-8">Número de Telemóvel Express <span class="text-danger">*</span></label>
                                    <input type="text" name="express_phone" class="form-control form-control-lg font-monospace" placeholder="923 000 000">
                                </div>
                                <div class="text-muted fs-8">
                                    <i class="fas fa-shield-alt text-success me-1"></i> Terá 5 minutos para autorizar o pagamento no seu telemóvel.
                                </div>
                            </div>

                            <!-- 3. TRANSFERÊNCIA BANCÁRIA -->
                            <div class="tab-pane fade p-3 border rounded-3 bg-light" id="method-transfer">
                                <h6 class="fw-bold text-dark mb-2"><i class="fas fa-university text-primary me-2"></i> Coordenadas Bancárias para Transferência</h6>
                                
                                <div class="p-3 bg-white border rounded-3 mb-3 fs-8">
                                    <div class="mb-2"><strong>Banco BFA:</strong> AO06.0006.0000.1234.5678.1011.1 (CONSULVOLT LDA)</div>
                                    <div><strong>Banco BAI:</strong> AO06.0040.0000.9876.5432.1011.2 (CONSULVOLT LDA)</div>
                                </div>

                                <div class="alert alert-warning border-0 fs-8 mb-3" style="border-radius: 10px; background: #fffbebfb; color: #92400e;">
                                    <i class="fas fa-exclamation-circle me-1"></i> <strong>AVISO IMPORTANTE:</strong> O método de transferência bancária requer validação manual pelo nosso departamento de BackOffice e pode levar entre <strong>24h e 72h</strong> úteis.
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold fs-8">Anexar Comprovativo de Transferência (PDF/Imagem)</label>
                                    <input type="file" name="proof_attachment" class="form-control">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-red w-100 py-3 mt-4 fs-6 fw-bold">
                            <i class="fas fa-check-circle me-2"></i> Confirmar e Concluir Pagamento
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="margin: 0; font-weight: 800; color: #0f172a; font-size: 1.6rem; letter-spacing: -0.5px;">
                <i class="fas fa-user-shield text-primary me-2"></i> Painel BackOffice — Gestão de Pagamentos e Licenças
            </h2>
            <p style="margin: 0; color: #64748b; font-size: 0.925rem;">
                Validação manual de transferências bancárias, aprovação de licenças e extensão de prazos.
            </p>
        </div>
        <div>
            <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#extendLicenseModal" style="border-radius: 10px; background: #2563eb;">
                <i class="fas fa-clock me-1"></i> Estender Licença Manualmente
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; background: #dcfce7; color: #15803d;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; background: #fef2f2; color: #991b1b;">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Tabela de Pagamentos -->
    <div class="card border-0 shadow-sm" style="border-radius: 20px; background: #ffffff;">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0">Pedidos de Pagamento Recebidos</h5>
            <div class="btn-group">
                <a href="{{ route('admin.payments.index', ['status' => 'all']) }}" class="btn btn-sm btn-outline-secondary {{ $status === 'all' ? 'active' : '' }}">Todos</a>
                <a href="{{ route('admin.payments.index', ['status' => 'PENDING']) }}" class="btn btn-sm btn-outline-warning {{ $status === 'PENDING' ? 'active' : '' }}">Pendentes</a>
                <a href="{{ route('admin.payments.index', ['status' => 'APPROVED']) }}" class="btn btn-sm btn-outline-success {{ $status === 'APPROVED' ? 'active' : '' }}">Aprovados</a>
            </div>
        </div>

        <div class="card-body p-4">
            @if($payments->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-check-circle text-success mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                    <p class="text-muted font-semibold mb-0">Nenhum pagamento pendente de validação neste momento.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light fs-8 text-uppercase">
                            <tr>
                                <th>Referência</th>
                                <th>Empresa</th>
                                <th>Plano / Valor</th>
                                <th>Método / Comprovativo</th>
                                <th>Data Pedido</th>
                                <th>Estado</th>
                                <th>Ações BackOffice</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td class="fw-bold text-dark font-monospace fs-7">{{ $payment->reference_code }}</td>
                                    <td>
                                        <div class="fw-bold text-dark fs-7">{{ $payment->company->name ?? 'Empresa N/A' }}</div>
                                        <span class="text-muted fs-8">NIF: {{ $payment->company->nif ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-7">{{ $payment->plan->name ?? 'Plano' }}</div>
                                        <span class="text-danger fw-bold fs-8">{{ number_format($payment->amount * 1.14, 2, ',', '.') }} Kz</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border fs-8 mb-1">
                                            {{ $payment->payment_details['payment_method_label'] ?? $payment->payment_method }}
                                        </span>
                                        @if($payment->proof_attachment)
                                            <div>
                                                <a href="{{ asset('storage/' . $payment->proof_attachment) }}" target="_blank" class="btn btn-sm btn-link p-0 text-primary fs-8 fw-bold">
                                                    <i class="fas fa-paperclip"></i> Ver Comprovativo
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="fs-8 text-muted">{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($payment->status === 'APPROVED')
                                            <span class="badge bg-success-subtle text-success fw-bold px-3 py-2">Aprovado</span>
                                        @elseif($payment->status === 'PENDING')
                                            <span class="badge bg-warning-subtle text-warning fw-bold px-3 py-2">Pendente</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger fw-bold px-3 py-2">Rejeitado</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->status === 'PENDING')
                                            <div class="d-flex gap-2">
                                                <form action="{{ route('admin.payments.approve', $payment->id) }}" method="POST" onsubmit="return confirm('Confirmar aprovação deste pagamento e ativação da licença?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success fw-bold px-3" style="border-radius: 8px;">
                                                        <i class="fas fa-check me-1"></i> Aprovar
                                                    </button>
                                                </form>

                                                <button type="button" class="btn btn-sm btn-outline-danger fw-bold" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $payment->id }}" style="border-radius: 8px;">
                                                    Rejeitar
                                                </button>
                                            </div>

                                            <!-- Modal Rejeitar -->
                                            <div class="modal fade" id="rejectModal{{ $payment->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                                                        <div class="modal-header border-bottom-0 pb-0">
                                                            <h5 class="modal-title fw-bold text-dark"><i class="fas fa-times-circle text-danger me-2"></i> Rejeitar Pagamento</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                                        </div>
                                                        <form action="{{ route('admin.payments.reject', $payment->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-body p-4">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-semibold text-dark">Motivo da Rejeição <span class="text-danger">*</span></label>
                                                                    <textarea name="rejection_reason" class="form-control" rows="3" placeholder="ex: Comprovativo ilegível ou valor incorreto" required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer border-top-0 pt-0">
                                                                <button type="button" class="btn btn-light border fw-bold" data-bs-dismiss="modal">Cancelar</button>
                                                                <button type="submit" class="btn btn-danger fw-bold"><i class="fas fa-times me-1"></i> Confirmar Rejeição</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted fs-8">Processado por {{ $payment->validator->name ?? 'Sistema' }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Estender Licença Manualmente -->
<div class="modal fade" id="extendLicenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-clock text-primary me-2"></i> Estender Licença de Empresa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form id="extendForm" method="POST" action="">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Selecionar Empresa <span class="text-danger">*</span></label>
                        <select id="companySelect" class="form-select" onchange="document.getElementById('extendForm').action = '/admin/companies/' + this.value + '/extend-license';" required>
                            <option value="">-- Escolha a empresa --</option>
                            @foreach($companies as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} (Dias restantes: {{ $c->remaining_days }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Dias a Adicionar ao Prazo Atual <span class="text-danger">*</span></label>
                        <input type="number" name="days" class="form-control" value="30" min="1" max="365" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light border fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold"><i class="fas fa-save me-1"></i> Aplicar Extensão</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

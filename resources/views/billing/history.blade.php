@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="margin: 0; font-weight: 800; color: #0f172a; font-size: 1.6rem; letter-spacing: -0.5px;">
                <i class="fas fa-history text-primary me-2"></i> Histórico de Pagamentos e Faturas AGT
            </h2>
            <p style="margin: 0; color: #64748b; font-size: 0.925rem;">
                Acompanhe os pagamentos da sua empresa e faça o download das faturas fiscais certificadas.
            </p>
        </div>
        <a href="{{ route('billing.plans') }}" class="btn btn-primary fw-bold" style="border-radius: 10px; background: #2563eb;">
            <i class="fas fa-plus me-1"></i> Nova Subscrição
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; background: #dcfce7; color: #15803d;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 20px; background: #ffffff;">
        <div class="card-body p-4">
            @if($payments->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-file-invoice-dollar text-muted mb-3" style="font-size: 3rem; opacity: 0.4;"></i>
                    <p class="text-muted font-semibold mb-0">Nenhum pagamento registado até ao momento.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light fs-8 text-uppercase">
                            <tr>
                                <th>Referência</th>
                                <th>Plano / Empresa</th>
                                <th>Método</th>
                                <th>Valor Total</th>
                                <th>Data</th>
                                <th>Estado</th>
                                <th>Fatura AGT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark font-monospace fs-7">{{ $payment->reference_code }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-7">{{ $payment->plan->name ?? 'Plano ERP' }}</div>
                                        <span class="text-muted fs-8">{{ $payment->company->name ?? '' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border fs-8">
                                            {{ $payment->payment_details['payment_method_label'] ?? $payment->payment_method }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-7">{{ number_format($payment->amount * 1.14, 2, ',', '.') }} Kz</div>
                                        <span class="text-muted fs-8">14% IVA incl.</span>
                                    </td>
                                    <td class="fs-8 text-muted">
                                        {{ $payment->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td>
                                        @if($payment->status === 'APPROVED')
                                            <span class="badge bg-success-subtle text-success fw-bold px-3 py-2" style="border-radius: 8px;">Aprovado</span>
                                        @elseif($payment->status === 'PENDING')
                                            <span class="badge bg-warning-subtle text-warning fw-bold px-3 py-2" style="border-radius: 8px;">Pendente (24h-72h)</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger fw-bold px-3 py-2" style="border-radius: 8px;">Rejeitado</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->invoice)
                                            <a href="{{ route('billing.invoice.pdf', $payment->id) }}" target="_blank" class="btn btn-sm btn-outline-danger fw-bold" style="border-radius: 8px;">
                                                <i class="fas fa-file-pdf me-1"></i> Fatura PDF
                                            </a>
                                        @else
                                            <span class="text-muted fs-8">Em validação</span>
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
@endsection

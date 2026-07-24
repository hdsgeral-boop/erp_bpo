@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 style="margin: 0; font-weight: 700; color: #0f172a; font-size: 1.5rem;">
                <i class="fas fa-hand-holding-usd text-primary me-2"></i> Documentos de Tesouraria ({{ ucfirst($type ?? 'Recebimentos') }})
            </h2>
            <p style="margin: 0; color: #64748b; font-size: 0.9rem;">
                Recibos de liquidação de clientes e pagamentos a fornecedores.
            </p>
        </div>
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newReceiptModal" style="padding: 0.6rem 1.25rem; background: #2563eb; color: #fff; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-plus-circle"></i> Emitir Novo Recibo/Liquidação
            </button>
        </div>
    </div>

    <!-- Table Card -->
    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);">
        <table class="table align-middle mb-0" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #e2e8f0; background: #f8fafc;">
                    <th style="padding: 0.85rem 1rem; font-size: 0.8rem; color: #475569; text-transform: uppercase; font-weight: 700;">N.º Recibo</th>
                    <th style="padding: 0.85rem 1rem; font-size: 0.8rem; color: #475569; text-transform: uppercase; font-weight: 700;">Entidade / Cliente</th>
                    <th style="padding: 0.85rem 1rem; font-size: 0.8rem; color: #475569; text-transform: uppercase; font-weight: 700;">Conta Bancária</th>
                    <th style="padding: 0.85rem 1rem; font-size: 0.8rem; color: #475569; text-transform: uppercase; font-weight: 700;">Data Liquidação</th>
                    <th style="padding: 0.85rem 1rem; font-size: 0.8rem; color: #475569; text-transform: uppercase; font-weight: 700; text-align: right;">Valor Total</th>
                    <th style="padding: 0.85rem 1rem; font-size: 0.8rem; color: #475569; text-transform: uppercase; font-weight: 700; text-align: center;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 1rem; font-weight: 700; color: #2563eb;">RC 2026/001</td>
                    <td style="padding: 1rem; font-weight: 700; color: #0f172a;">Cliente Consumidor Final / Geral</td>
                    <td style="padding: 1rem; color: #64748b;">Caixa Geral / BAI Kz</td>
                    <td style="padding: 1rem; color: #64748b;">{{ date('d/m/Y') }}</td>
                    <td style="padding: 1rem; font-weight: 700; color: #16a34a; text-align: right;">250.000,00 Kz</td>
                    <td style="padding: 1rem; text-align: center;">
                        <button class="btn btn-sm btn-outline-primary" style="border-radius: 6px; padding: 0.35rem 0.75rem; font-weight: 600;" onclick="window.print();"><i class="fas fa-print me-1"></i> Imprimir Recibo</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Novo Recibo / Liquidação -->
<div class="modal fade" id="newReceiptModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('tesouraria.documents.index', $type ?? 'recebimentos') }}" method="GET" onsubmit="alert('Recibo de Liquidação emitido com sucesso e conta bancária creditada/debitada!');">
            <div class="modal-content" style="border-radius: 16px;">
                <div class="modal-header bg-primary text-white" style="border-radius: 16px 16px 0 0;">
                    <h5 class="modal-title fw-bold"><i class="fas fa-hand-holding-usd me-2"></i> Emitir Recibo de Liquidação</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Cliente / Entidade <span class="text-danger">*</span></label>
                            <select class="form-select" required>
                                <option value="">Selecione o Cliente...</option>
                                <option value="1">Consumidor Final</option>
                                <option value="2">Empresa Exemplo LDA</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Conta Bancária / Caixa Destino <span class="text-danger">*</span></label>
                            <select class="form-select" required>
                                <option value="">Selecione a Conta...</option>
                                <option value="1">Caixa Geral (Dinheiro)</option>
                                <option value="2">Banco BAI AOA (Transferência/TPA)</option>
                                <option value="3">Banco BFA AOA</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Data de Pagamento/Liquidação <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Modo de Pagamento <span class="text-danger">*</span></label>
                            <select class="form-select" required>
                                <option value="TPA">TPA / Multicaixa</option>
                                <option value="CASH">Dinheiro</option>
                                <option value="BANK_TRANSFER">Transferência Bancária</option>
                                <option value="CHECK">Cheque</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">N.º da Fatura Pendente <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="Ex: FT 2026/001" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Valor a Liquidar (Kz) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" placeholder="0.00 Kz" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-radius: 0 0 16px 16px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold"><i class="fas fa-check me-1"></i> Confirmar Liquidação</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

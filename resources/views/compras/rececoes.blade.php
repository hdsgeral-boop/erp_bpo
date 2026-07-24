@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 style="margin: 0; font-weight: 700; color: #0f172a; font-size: 1.5rem;">
                <i class="fas fa-box-open text-primary me-2"></i> Receção de Mercadoria (Guias de Entrada)
            </h2>
            <p style="margin: 0; color: #64748b; font-size: 0.9rem;">
                Conferência de guias de remessa e entrada direta de stock por fornecedor.
            </p>
        </div>
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newRececaoModal" style="padding: 0.6rem 1.2rem; background: #2563eb; color: #fff; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-plus-circle"></i> Nova Receção de Stock
            </button>
        </div>
    </div>

    <!-- Table Card -->
    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);">
        <table class="table align-middle mb-0" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #e2e8f0; background: #f8fafc;">
                    <th style="padding: 0.85rem 1rem; font-size: 0.8rem; color: #475569; text-transform: uppercase; font-weight: 700;">N.º Guia Entrada</th>
                    <th style="padding: 0.85rem 1rem; font-size: 0.8rem; color: #475569; text-transform: uppercase; font-weight: 700;">Fornecedor</th>
                    <th style="padding: 0.85rem 1rem; font-size: 0.8rem; color: #475569; text-transform: uppercase; font-weight: 700;">Armazém Destino</th>
                    <th style="padding: 0.85rem 1rem; font-size: 0.8rem; color: #475569; text-transform: uppercase; font-weight: 700;">Data de Entrada</th>
                    <th style="padding: 0.85rem 1rem; font-size: 0.8rem; color: #475569; text-transform: uppercase; font-weight: 700;">Estado</th>
                    <th style="padding: 0.85rem 1rem; font-size: 0.8rem; color: #475569; text-transform: uppercase; font-weight: 700; text-align: right;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 1rem; font-weight: 700; color: #2563eb;">GE 2026/001</td>
                    <td style="padding: 1rem; font-weight: 700; color: #0f172a;">Fornecedor Geral LDA</td>
                    <td style="padding: 1rem; color: #64748b;">Armazém Central Luanda</td>
                    <td style="padding: 1rem; color: #64748b;">{{ date('d/m/Y') }}</td>
                    <td style="padding: 1rem;">
                        <span style="background: #dcfce7; color: #166534; padding: 0.25rem 0.65rem; border-radius: 6px; font-weight: 700; font-size: 0.75rem;">
                            <i class="fas fa-check-double me-1"></i> CONFERIDO & INTEGRADO
                        </span>
                    </td>
                    <td style="padding: 1rem; text-align: right;">
                        <button class="btn btn-sm btn-outline-primary" style="border-radius: 6px; padding: 0.35rem 0.75rem; font-weight: 600;"><i class="fas fa-eye me-1"></i> Ver Guia</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Nova Receção -->
<div class="modal fade" id="newRececaoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('compras.rececoes.index') }}" method="GET" onsubmit="alert('Receção de mercadoria registada e entrada de stock efetuada no armazém!');">
            <div class="modal-content" style="border-radius: 16px;">
                <div class="modal-header bg-primary text-white" style="border-radius: 16px 16px 0 0;">
                    <h5 class="modal-title fw-bold"><i class="fas fa-truck-loading me-2"></i> Nova Receção de Stock de Fornecedor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fornecedor <span class="text-danger">*</span></label>
                            <select class="form-select" required>
                                <option value="">Selecione o Fornecedor...</option>
                                <option value="1">Fornecedor Geral LDA</option>
                                <option value="2">Distribuidora Angolana SA</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Armazém de Entrada <span class="text-danger">*</span></label>
                            <select class="form-select" required>
                                <option value="">Selecione o Armazém...</option>
                                <option value="1">Armazém Central Luanda</option>
                                <option value="2">Armazém Viana</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">N.º Guia do Fornecedor <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="Ex: GR 99182/2026" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Data de Receção <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-12 mt-4">
                            <h6 class="fw-bold border-bottom pb-2">Artigos Recebidos</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Artigo</th>
                                            <th style="width: 120px;">Qtd Recebida</th>
                                            <th style="width: 150px;">Preço Custo Unit.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control" placeholder="Nome do Produto / Código" required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" value="1" min="1" required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control" placeholder="0.00 Kz" required>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-radius: 0 0 16px 16px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold"><i class="fas fa-check me-1"></i> Confirmar Entrada de Stock</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

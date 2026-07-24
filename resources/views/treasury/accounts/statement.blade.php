@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    .text-in { color: #10b981; font-weight: 700; }
    .text-out { color: #ef4444; font-weight: 700; }
    @media print {
        .no-print { display: none !important; }
        .card-premium { border: none !important; box-shadow: none !important; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom no-print">
        <div>
            <a href="{{ route('tesouraria.accounts.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
                <i class="fas fa-arrow-left me-1"></i> Voltar às Contas de Tesouraria
            </a>
            <h2 class="fw-extrabold text-dark mb-1">
                <i class="fas {{ str_contains(strtolower($account->name), 'caixa') ? 'fa-cash-register text-success' : 'fa-university text-primary' }} me-2"></i> Extrato de Conta: {{ $account->name }}
            </h2>
            <p class="text-muted small mb-0">Moeda: <span class="badge bg-secondary-subtle text-dark">{{ $account->currency }}</span> | Estado: {!! $account->is_active ? '<span class="badge bg-success-subtle text-success">Ativa</span>' : '<span class="badge bg-danger-subtle text-danger">Inativa</span>' !!}</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary fw-bold px-3 py-2" data-bs-toggle="modal" data-bs-target="#quickMovementModal" style="border-radius: 10px;">
                <i class="fas fa-plus-circle me-1"></i> Registar Movimento
            </button>
            <button class="btn btn-outline-secondary fw-bold px-3 py-2" onclick="window.print()" style="border-radius: 10px;">
                <i class="fas fa-print me-1"></i> Imprimir Extrato
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm no-print mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- KPI Cards Summary -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card card-body border-0 shadow-sm rounded-4 bg-white h-100">
                <span class="text-muted small fw-bold text-uppercase">Saldo Inicial da Conta</span>
                <h4 class="fw-extrabold text-dark mb-0 mt-2">{{ number_format($account->initial_balance, 2, ',', '.') }} {{ $account->currency }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-body border-0 shadow-sm rounded-4 bg-white h-100">
                <span class="text-muted small fw-bold text-uppercase text-success">Total Entradas (Créditos)</span>
                <h4 class="fw-extrabold text-success mb-0 mt-2">+ {{ number_format($totalIn, 2, ',', '.') }} {{ $account->currency }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-body border-0 shadow-sm rounded-4 bg-white h-100">
                <span class="text-muted small fw-bold text-uppercase text-danger">Total Saídas (Débitos)</span>
                <h4 class="fw-extrabold text-danger mb-0 mt-2">- {{ number_format($totalOut, 2, ',', '.') }} {{ $account->currency }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-body border-0 shadow-sm rounded-4 bg-primary text-white h-100">
                <span class="text-white-50 small fw-bold text-uppercase">Saldo Atual Disponível</span>
                <h3 class="fw-extrabold text-white mb-0 mt-2">{{ number_format($account->current_balance, 2, ',', '.') }} {{ $account->currency }}</h3>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card card-body border-0 shadow-sm rounded-4 mb-4 bg-white no-print">
        <form method="GET" action="{{ route('tesouraria.accounts.statement', $account->id) }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Data Inicial</label>
                <input type="date" name="start_date" class="form-control form-control-sm rounded-3" value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Data Final</label>
                <input type="date" name="end_date" class="form-control form-control-sm rounded-3" value="{{ $endDate }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Tipo de Movimento</label>
                <select name="doc_type" class="form-select form-select-sm rounded-3">
                    <option value="">Todos os Movimentos</option>
                    <option value="IN" {{ $docTypeFilter === 'IN' ? 'selected' : '' }}>Apenas Entradas (Créditos)</option>
                    <option value="OUT" {{ $docTypeFilter === 'OUT' ? 'selected' : '' }}>Apenas Saídas (Débitos)</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm rounded-3 flex-grow-1 fw-bold"><i class="fas fa-filter me-1"></i> Filtrar</button>
                <a href="{{ route('tesouraria.accounts.statement', $account->id) }}" class="btn btn-outline-secondary btn-sm rounded-3"><i class="fas fa-undo me-1"></i> Limpar</a>
            </div>
        </form>
    </div>

    <!-- Print Title -->
    <div class="d-none d-print-block text-center mb-4">
        <h3>EXTRATO DE CONTA DE TESOURARIA</h3>
        <h4>{{ $account->name }} ({{ $account->currency }})</h4>
        <p>Período: {{ date('d/m/Y', strtotime($startDate)) }} a {{ date('d/m/Y', strtotime($endDate)) }}</p>
    </div>

    <!-- Statement Table -->
    <div class="card-premium overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">#</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">DATA</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">DOCUMENTO / REF</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">TERCEIRO / DESCRIÇÃO</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">MÉTODO</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold text-end">ENTRADA (+)</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold text-end">SAÍDA (-)</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold text-end">SALDO ACUMULADO</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $runningBalance = $account->initial_balance;
                    @endphp
                    @forelse($receipts as $idx => $r)
                        @php
                            $isIn = in_array(strtoupper($r->doc_type), ['REC', 'RC', 'DEP']);
                            $val = (float)$r->total_amount;
                            if ($isIn) {
                                $runningBalance += $val;
                            } else {
                                $runningBalance -= $val;
                            }
                        @endphp
                        <tr>
                            <td class="py-3 px-4 text-muted small">{{ $idx + 1 }}</td>
                            <td class="py-3 px-4 fw-bold text-dark">{{ $r->date ? $r->date->format('d/m/Y') : '-' }}</td>
                            <td class="py-3 px-4 font-monospace">
                                <span class="badge {{ $isIn ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} me-1">
                                    {{ $r->doc_type }}
                                </span>
                                {{ $r->doc_number }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="fw-bold text-dark">{{ $r->thirdParty->name ?? $r->payment_reference ?? 'Movimento Tesouraria' }}</div>
                                @if($r->payment_reference && $r->thirdParty)
                                    <div class="small text-muted">{{ $r->payment_reference }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="badge bg-light text-dark border">{{ $r->payment_method ?? 'TRANSF' }}</span>
                            </td>
                            <td class="py-3 px-4 text-end text-in fs-6">
                                {{ $isIn ? '+ ' . number_format($val, 2, ',', '.') . ' Kz' : '-' }}
                            </td>
                            <td class="py-3 px-4 text-end text-out fs-6">
                                {{ !$isIn ? '- ' . number_format($val, 2, ',', '.') . ' Kz' : '-' }}
                            </td>
                            <td class="py-3 px-4 text-end fw-extrabold text-dark fs-6">
                                {{ number_format($runningBalance, 2, ',', '.') }} {{ $account->currency }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="fas fa-receipt fa-2x mb-2 d-block text-secondary opacity-50"></i>
                                Nenhum movimento registado para este período ou filtro.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Movimento Rápido -->
<div class="modal fade" id="quickMovementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-exchange-alt text-primary me-2"></i>Novo Movimento Rápido em Conta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('tesouraria.accounts.movement', $account->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tipo de Movimento <span class="text-danger">*</span></label>
                        <select name="movement_type" class="form-select fw-bold" required>
                            <option value="IN" class="text-success">➕ Entradas / Depósito (+)</option>
                            <option value="OUT" class="text-danger">➖ Saída / Levantamento / Despesa (-)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Valor <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="amount" class="form-control form-control-lg fw-bold" placeholder="0.00" required>
                            <span class="input-group-text bg-light fw-bold">{{ $account->currency }}</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Descrição / Motivo <span class="text-danger">*</span></label>
                        <input type="text" name="description" class="form-control" placeholder="Ex: Reforço de caixa, Pagamento fornecedor..." required>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Data <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Método Pagamento <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select" required>
                                <option value="CASH">Numerário / Caixa</option>
                                <option value="TRANSFER" selected>Transferência Bancária</option>
                                <option value="CARD">Cartão Multicaixa</option>
                                <option value="CHECK">Cheque</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                    <button type="button" class="btn btn-light border fw-bold px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Lançar Movimento</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

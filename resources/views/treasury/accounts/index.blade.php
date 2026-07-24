@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        background: #ffffff;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .card-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
    }
    .balance-positive {
        color: #10b981;
        font-weight: 800;
    }
    .balance-negative {
        color: #ef4444;
        font-weight: 800;
    }
    .balance-zero {
        color: #64748b;
        font-weight: 800;
    }
    .btn-add-new {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        color: white;
        border: none;
        padding: 0.6rem 1.2rem;
        border-radius: 10px;
        font-weight: 600;
    }
    .btn-add-new:hover {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-extrabold mb-0 text-dark">
                <i class="fas fa-university text-primary me-2"></i>Contas de Tesouraria
            </h2>
            <p class="text-muted mb-0">Gestão de Bancos e Caixas da Empresa</p>
        </div>
        <a href="{{ route('tesouraria.accounts.create') }}" class="btn btn-add-new shadow-sm">
            <i class="fas fa-plus me-2"></i> Adicionar Conta
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card card-body border-0 shadow-sm rounded-4 bg-primary text-white h-100">
                <span class="text-white-50 small fw-bold text-uppercase">Saldo Consolidado Global</span>
                <h3 class="fw-extrabold text-white mb-0 mt-2">{{ number_format($totalBalance, 2, ',', '.') }} Kz</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-body border-0 shadow-sm rounded-4 bg-white h-100">
                <span class="text-muted small fw-bold text-uppercase text-success">Entradas do Mês</span>
                <h4 class="fw-extrabold text-success mb-0 mt-2">+ {{ number_format($monthlyIn, 2, ',', '.') }} Kz</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-body border-0 shadow-sm rounded-4 bg-white h-100">
                <span class="text-muted small fw-bold text-uppercase text-danger">Saídas do Mês</span>
                <h4 class="fw-extrabold text-danger mb-0 mt-2">- {{ number_format($monthlyOut, 2, ',', '.') }} Kz</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-body border-0 shadow-sm rounded-4 bg-white h-100">
                <span class="text-muted small fw-bold text-uppercase">Contas Ativas</span>
                <h4 class="fw-extrabold text-dark mb-0 mt-2">{{ $activeAccountsCount }} Contas</h4>
            </div>
        </div>
    </div>

    <!-- Accounts Cards Grid -->
    <div class="row g-4">
        @forelse($accounts as $account)
            <div class="col-md-4">
                <div class="card-premium p-4 h-100 position-relative border">
                    @if(!$account->is_active)
                        <span class="position-absolute top-0 end-0 mt-3 me-3 badge bg-danger-subtle text-danger border border-danger-subtle">Inativa</span>
                    @endif
                    
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary-subtle p-3 rounded-circle text-primary">
                                <i class="fas {{ str_contains(strtolower($account->name), 'caixa') ? 'fa-cash-register' : 'fa-landmark' }} fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-dark">{{ $account->name }}</h5>
                                <small class="text-muted">Moeda: <span class="fw-bold">{{ $account->currency }}</span></small>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light text-muted border-0" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius: 10px;">
                                <li>
                                    <a class="dropdown-item fw-bold text-primary" href="{{ route('tesouraria.accounts.statement', $account->id) }}">
                                        <i class="fas fa-list-alt me-2"></i>Ver Extrato de Conta
                                    </a>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item text-success fw-bold" data-bs-toggle="modal" data-bs-target="#quickMovementModal{{ $account->id }}">
                                        <i class="fas fa-plus-circle me-2"></i>Registar Movimento
                                    </button>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('tesouraria.accounts.edit', $account->id) }}">
                                        <i class="fas fa-edit me-2 text-secondary"></i>Editar Conta
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('tesouraria.accounts.destroy', $account->id) }}" method="POST" onsubmit="return confirm('Tem a certeza que pretende alterar o estado desta conta?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="dropdown-item text-danger">
                                            <i class="fas fa-power-off me-2"></i>{{ $account->is_active ? 'Desativar Conta' : 'Reativar Conta' }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top">
                        <p class="text-muted small mb-1 text-uppercase fw-bold">Saldo Atual</p>
                        <h3 class="mb-0 {{ $account->current_balance > 0 ? 'balance-positive' : ($account->current_balance < 0 ? 'balance-negative' : 'balance-zero') }}">
                            {{ number_format($account->current_balance, 2, ',', '.') }} <span class="fs-6 text-muted">{{ $account->currency }}</span>
                        </h3>
                    </div>

                    <div class="mt-3 pt-2 d-flex gap-2">
                        <a href="{{ route('tesouraria.accounts.statement', $account->id) }}" class="btn btn-outline-primary btn-sm flex-grow-1 fw-bold" style="border-radius: 8px;">
                            <i class="fas fa-receipt me-1"></i> Extrato
                        </a>
                        <button type="button" class="btn btn-success btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#quickMovementModal{{ $account->id }}" style="border-radius: 8px;">
                            <i class="fas fa-exchange-alt me-1"></i> Movimento
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal Movimento Rápido para esta Conta -->
            <div class="modal fade" id="quickMovementModal{{ $account->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                        <div class="modal-header border-bottom">
                            <h5 class="modal-title fw-bold text-dark"><i class="fas fa-exchange-alt text-primary me-2"></i>Novo Movimento: {{ $account->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('tesouraria.accounts.movement', $account->id) }}" method="POST">
                            @csrf
                            <div class="modal-body p-4 text-start">
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
        @empty
            <div class="col-12 text-center py-5 bg-white shadow-sm" style="border-radius: 16px; border: 1px dashed #cbd5e1;">
                <div class="text-muted mb-3"><i class="fas fa-university fa-3x"></i></div>
                <h5 class="fw-bold text-dark">Nenhuma Conta Encontrada</h5>
                <p class="text-muted">Ainda não configurou nenhuma conta bancária ou caixa para a tesouraria.</p>
                <a href="{{ route('tesouraria.accounts.create') }}" class="btn btn-primary px-4 mt-2 fw-bold" style="border-radius: 10px;">Criar a Primeira Conta</a>
            </div>
        @endforelse
    </div>
</div>
@endsection

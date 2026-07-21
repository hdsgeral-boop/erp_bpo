@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        background: #ffffff;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .card-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .balance-positive {
        color: #10b981;
        font-weight: 700;
    }
    .balance-negative {
        color: #ef4444;
        font-weight: 700;
    }
    .balance-zero {
        color: #64748b;
        font-weight: 700;
    }
    .btn-add-new {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        color: white;
        border: none;
        padding: 0.6rem 1.2rem;
        border-radius: 8px;
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-university text-primary me-2"></i>Contas de Tesouraria</h2>
            <p class="text-muted mb-0">Gestão de Bancos e Caixas da Empresa</p>
        </div>
        <a href="{{ route('tesouraria.accounts.create') }}" class="btn btn-add-new">
            <i class="fas fa-plus me-2"></i> Adicionar Conta
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm" style="border-radius: 10px;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">
        @forelse($accounts as $account)
            <div class="col-md-4">
                <div class="card-premium p-4 h-100 position-relative">
                    @if(!$account->is_active)
                        <span class="position-absolute top-0 end-0 mt-3 me-3 badge bg-danger opacity-75">Inativa</span>
                    @endif
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-light p-3 rounded-circle text-primary">
                                <i class="fas {{ str_contains(strtolower($account->name), 'caixa') ? 'fa-cash-register' : 'fa-landmark' }} fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-dark">{{ $account->name }}</h5>
                                <small class="text-muted">Moeda: {{ $account->currency }}</small>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light text-muted border-0" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius: 8px;">
                                <li><a class="dropdown-item" href="{{ route('tesouraria.accounts.edit', $account->id) }}"><i class="fas fa-edit me-2 text-primary"></i>Editar Conta</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-list-alt me-2 text-secondary"></i>Ver Extrato (Brevemente)</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top">
                        <p class="text-muted small mb-1 text-uppercase fw-bold">Saldo Atual</p>
                        <h3 class="mb-0 {{ $account->current_balance > 0 ? 'balance-positive' : ($account->current_balance < 0 ? 'balance-negative' : 'balance-zero') }}">
                            {{ number_format($account->current_balance, 2, ',', '.') }} <span class="fs-6 text-muted">{{ $account->currency }}</span>
                        </h3>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 bg-white" style="border-radius: 12px; border: 1px dashed #cbd5e1;">
                <div class="text-muted mb-3"><i class="fas fa-university fa-3x"></i></div>
                <h5 class="fw-bold text-dark">Nenhuma Conta Encontrada</h5>
                <p class="text-muted">Ainda não configurou nenhuma conta bancária ou caixa para a tesouraria.</p>
                <a href="{{ route('tesouraria.accounts.create') }}" class="btn btn-primary px-4 mt-2" style="border-radius: 8px;">Criar a Primeira Conta</a>
            </div>
        @endforelse
    </div>
</div>
@endsection

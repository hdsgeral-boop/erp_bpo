@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        background: #ffffff;
    }
    .balance-positive {
        color: #10b981; /* Verde - Dinheiro a receber / Crédito a nosso favor */
        font-weight: 700;
    }
    .balance-negative {
        color: #ef4444; /* Vermelho - Dinheiro a pagar / Débito nosso */
        font-weight: 700;
    }
    .balance-zero {
        color: #64748b;
        font-weight: 700;
    }
    .nav-pills .nav-link {
        color: #64748b;
        border-radius: 8px;
        padding: 0.5rem 1.5rem;
        font-weight: 600;
    }
    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-users-cog text-primary me-2"></i>Contas Correntes</h2>
            <p class="text-muted mb-0">Gestão de saldos pendentes e extratos de Clientes e Fornecedores.</p>
        </div>
    </div>

    <div class="card-premium">
        <div class="p-4 border-bottom bg-light">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <ul class="nav nav-pills gap-2">
                        <li class="nav-item">
                            <a class="nav-link {{ $type === 'customer' ? 'active' : '' }}" href="{{ route('tesouraria.current_accounts.index', ['type' => 'customer']) }}">
                                <i class="fas fa-user-tag me-1"></i> Clientes (Dívida a Receber)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $type === 'supplier' ? 'active' : '' }}" href="{{ route('tesouraria.current_accounts.index', ['type' => 'supplier']) }}">
                                <i class="fas fa-truck-loading me-1"></i> Fornecedores (Dívida a Pagar)
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6 mt-3 mt-md-0">
                    <form action="{{ route('tesouraria.current_accounts.index') }}" method="GET" class="d-flex gap-2">
                        <input type="hidden" name="type" value="{{ $type }}">
                        <input type="text" name="search" class="form-control" placeholder="Pesquisar por Nome ou NIF..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px;">Pesquisar</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Entidade</th>
                        <th>NIF</th>
                        <th>Contactos</th>
                        <th class="text-end">Saldo Pendente (AOA)</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entities as $entity)
                    @php
                        // Para clientes, saldo pendente positivo é bom (eles devem-nos) => Verde
                        // Para fornecedores, saldo pendente positivo é mau (nós devemos) => Vermelho
                        $balanceClass = 'balance-zero';
                        if ($entity->pending_balance > 0) {
                            $balanceClass = $type === 'customer' ? 'balance-positive' : 'balance-negative';
                        }
                    @endphp
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ $entity->name }}</div>
                            <div class="small text-muted">{{ $entity->code ?? '' }}</div>
                        </td>
                        <td>{{ $entity->tax_id ?? 'N/D' }}</td>
                        <td>
                            <div class="small"><i class="fas fa-envelope text-muted me-1"></i> {{ $entity->email ?? '-' }}</div>
                            <div class="small"><i class="fas fa-phone text-muted me-1"></i> {{ $entity->phone ?? '-' }}</div>
                        </td>
                        <td class="text-end">
                            <span class="{{ $balanceClass }} fs-5">{{ number_format($entity->pending_balance, 2, ',', '.') }}</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('tesouraria.current_accounts.show', ['id' => $entity->id, 'type' => $type]) }}" class="btn btn-sm btn-light text-primary border rounded-pill px-3 fw-bold" title="Ver Extrato">
                                <i class="fas fa-list-alt me-1"></i> Extrato
                            </a>
                            @if($entity->pending_balance > 0)
                                @if($type === 'customer')
                                    <a href="{{ route('tesouraria.documentos.create', ['category' => 'recebimentos', 'entity_id' => $entity->id]) }}" class="btn btn-sm btn-success rounded-pill px-3 fw-bold ms-1" title="Liquidar Dívida">
                                        <i class="fas fa-hand-holding-usd me-1"></i> Receber
                                    </a>
                                @else
                                    <a href="{{ route('tesouraria.documentos.create', ['category' => 'pagamentos', 'entity_id' => $entity->id]) }}" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold ms-1" title="Pagar Dívida">
                                        <i class="fas fa-money-check-alt me-1"></i> Pagar
                                    </a>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            Nenhuma entidade encontrada.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">
            {{ $entities->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection

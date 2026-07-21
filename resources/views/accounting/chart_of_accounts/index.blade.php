@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-auto">
            <div class="bg-primary text-white p-3 rounded-3 shadow-sm">
                <i class="fas fa-sitemap fa-2x"></i>
            </div>
        </div>
        <div class="col">
            <h2 class="mb-0 fw-bold">Plano de Contas</h2>
            <p class="text-muted mb-0">Gestão e configuração do Plano Geral de Contabilidade (PGC)</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('contabilidade.chart_of_accounts.create') }}" class="btn btn-primary px-4 py-2 fw-bold shadow-sm">
                <i class="fas fa-plus me-2"></i>Nova Conta
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 text-muted text-uppercase" style="font-size: 0.85rem">Código</th>
                            <th class="text-muted text-uppercase" style="font-size: 0.85rem">Descrição / Nome da Conta</th>
                            <th class="text-muted text-uppercase" style="font-size: 0.85rem">Tipo</th>
                            <th class="text-muted text-uppercase" style="font-size: 0.85rem">Estado</th>
                            <th class="text-end pe-4 text-muted text-uppercase" style="font-size: 0.85rem">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $account)
                        <tr>
                            <td class="ps-4 fw-bold text-primary" style="font-family: monospace; font-size: 1.1rem;">
                                {{ $account->code }}
                            </td>
                            <td>
                                @if($account->type == 'I')
                                    <strong class="text-dark">{{ $account->description }}</strong>
                                @else
                                    <span class="text-secondary ps-3"><i class="fas fa-level-up-alt fa-rotate-90 me-2 text-muted"></i>{{ $account->description }}</span>
                                @endif
                            </td>
                            <td>
                                @if($account->type == 'I')
                                    <span class="badge bg-secondary">Integradora</span>
                                @else
                                    <span class="badge bg-info">Movimento</span>
                                @endif
                            </td>
                            <td>
                                @if($account->is_master_data)
                                    <span class="badge bg-success-subtle text-success border border-success"><i class="fas fa-lock me-1"></i>Sistema</span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary border border-primary"><i class="fas fa-user-edit me-1"></i>Manual</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('contabilidade.chart_of_accounts.edit', $account->id) }}" class="btn btn-sm btn-outline-primary rounded-circle" data-bs-toggle="tooltip" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i>
                                <h5>O Plano de Contas está vazio</h5>
                                <p>Crie as contas ou execute uma importação do PGC oficial.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

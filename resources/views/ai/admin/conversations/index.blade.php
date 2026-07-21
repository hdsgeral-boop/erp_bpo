@extends('layouts.app')

@push('styles')
<style>
    .card-premium { background: #ffffff; border: none; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); }
    .table-custom thead th { background-color: #ffffff; color: #475569; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; padding: 1rem 1.5rem; border-bottom: 2px solid #e2e8f0; }
    .table-custom tbody td { padding: 1rem 1.5rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-comment-dots text-primary me-2"></i>Histórico de Conversas (IA)</h2>
            <p class="text-muted mb-0">Auditoria a todas as interações dos utilizadores com os Agentes de Inteligência Artificial.</p>
        </div>
    </div>

    <div class="card-premium">
        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th>Sessão / Tópico</th>
                        <th>Utilizador</th>
                        <th>Agente</th>
                        <th class="text-center">Mensagens</th>
                        <th>Última Atividade</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($conversations as $conv)
                    <tr>
                        <td class="fw-bold text-dark">{{ $conv->title ?? 'Nova Conversa #' . $conv->id }}</td>
                        <td>
                            @if($conv->user)
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1"><i class="fas fa-user me-1"></i> {{ $conv->user->name }}</span>
                            @else
                                <span class="text-muted">Sistema</span>
                            @endif
                        </td>
                        <td>{{ $conv->agent ? $conv->agent->name : 'N/A' }}</td>
                        <td class="text-center fw-bold">{{ $conv->messages()->count() }}</td>
                        <td>{{ $conv->updated_at->diffForHumans() }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-light text-primary border" title="Ver Histórico">
                                <i class="fas fa-eye"></i> Ler
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-comment-slash fa-2x mb-3 d-block opacity-50"></i>
                            Ainda não existem conversas registadas com a Inteligência Artificial.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($conversations->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $conversations->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection

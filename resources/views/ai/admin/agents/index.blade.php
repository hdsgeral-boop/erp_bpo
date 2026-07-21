@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h2 class="view-title mb-0"><i class="fas fa-robot text-primary"></i> Agentes IA Especializados</h2>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Definição de perfis, prompts e permissões por departamento.</p>
        </div>
        <button class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-2"></i> Novo Agente
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Nome / Perfil</th>
                            <th class="py-3">Provider & Modelo</th>
                            <th class="py-3">Temperatura</th>
                            <th class="py-3">Tools Associadas</th>
                            <th class="py-3 text-center">Estado</th>
                            <th class="px-4 py-3 text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agents as $agent)
                        <tr>
                            <td class="px-4">
                                <div class="fw-bold">{{ $agent->name }}</div>
                                <small class="text-muted text-truncate d-inline-block" style="max-width: 250px;">
                                    {{ $agent->description }}
                                </small>
                            </td>
                            <td>
                                <div><i class="fas fa-server text-muted me-1"></i> {{ $agent->provider->name ?? 'N/A' }}</div>
                                <div class="small"><i class="fas fa-cube text-muted me-1"></i> {{ $agent->aiModel->name ?? 'N/A' }}</div>
                            </td>
                            <td>{{ $agent->temperature }}</td>
                            <td>
                                <span class="badge bg-primary rounded-pill">{{ $agent->tools ? count($agent->tools) : 0 }} Ferramentas</span>
                            </td>
                            <td class="text-center">
                                @if($agent->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fas fa-check-circle me-1"></i> Ativo</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="fas fa-times-circle me-1"></i> Inativo</span>
                                @endif
                            </td>
                            <td class="px-4 text-end">
                                <button class="btn btn-sm btn-outline-info" title="Ver Prompt">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-primary ms-1" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fs-2 mb-3 text-light"></i><br>
                                Nenhum agente configurado.
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

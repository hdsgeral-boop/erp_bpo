@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h2 class="view-title mb-0"><i class="fas fa-cubes text-primary"></i> Catálogo de Modelos IA</h2>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Gestão de capacidades, LLMs e Context Windows.</p>
        </div>
        <button class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-2"></i> Registar Modelo
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Fornecedor</th>
                            <th class="py-3">Modelo</th>
                            <th class="py-3">Identificador</th>
                            <th class="py-3">Context Window</th>
                            <th class="py-3">Capabilities</th>
                            <th class="px-4 py-3 text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($models as $model)
                        <tr>
                            <td class="px-4 fw-bold text-primary">{{ $model->provider->name ?? 'N/A' }}</td>
                            <td class="fw-bold">{{ $model->name }}</td>
                            <td><code class="bg-light px-2 py-1 rounded text-dark">{{ $model->identifier }}</code></td>
                            <td>{{ number_format($model->context_window, 0, ',', '.') }} <small class="text-muted">tokens</small></td>
                            <td>
                                @if($model->supports_chat) <span class="badge bg-secondary-subtle text-secondary" title="Chat">💬</span> @endif
                                @if($model->supports_vision) <span class="badge bg-secondary-subtle text-secondary" title="Vision">👁️</span> @endif
                                @if($model->supports_tool_calling) <span class="badge bg-secondary-subtle text-secondary" title="Tool Calling">🛠️</span> @endif
                                @if($model->supports_embeddings) <span class="badge bg-secondary-subtle text-secondary" title="Embeddings">🧩</span> @endif
                                @if($model->supports_json_mode) <span class="badge bg-secondary-subtle text-secondary" title="JSON Mode">{' '}</span> @endif
                            </td>
                            <td class="px-4 text-end">
                                <button class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger ms-1" title="Apagar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fs-2 mb-3 text-light"></i><br>
                                Nenhum modelo registado.
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

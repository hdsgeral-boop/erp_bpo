@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h2 class="view-title mb-0"><i class="fas fa-server text-primary"></i> Fornecedores IA (Providers)</h2>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Gestão de endpoints, Fallbacks e Chaves de Acesso Seguras.</p>
        </div>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#wizardModal">
            <i class="fas fa-magic me-2"></i> Adicionar Provider (Wizard)
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Prioridade</th>
                            <th class="py-3">Nome / Driver</th>
                            <th class="py-3">Timeout (s)</th>
                            <th class="py-3">Fallback</th>
                            <th class="py-3 text-center">Estado</th>
                            <th class="px-4 py-3 text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($providers as $provider)
                        <tr>
                            <td class="px-4"><span class="badge bg-secondary rounded-pill px-3">{{ $provider->priority }}</span></td>
                            <td>
                                <div class="fw-bold">{{ $provider->name }}</div>
                                <small class="text-muted text-uppercase">{{ $provider->driver }}</small>
                            </td>
                            <td>{{ $provider->timeout }}s</td>
                            <td>
                                @if($provider->fallback)
                                    <span class="badge bg-info"><i class="fas fa-share ms-1"></i> {{ $provider->fallback->name }}</span>
                                @else
                                    <span class="text-muted small">Sem Fallback</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($provider->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fas fa-check-circle me-1"></i> Ativo</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="fas fa-times-circle me-1"></i> Inativo</span>
                                @endif
                            </td>
                            <td class="px-4 text-end">
                                <button class="btn btn-sm btn-outline-success btn-test-connection" data-id="{{ $provider->id }}" title="Testar Conectividade">
                                    <i class="fas fa-wifi"></i>
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
                                Nenhum fornecedor configurado.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Wizard de Configuração -->
<div class="modal fade" id="wizardModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-magic text-primary"></i> Assistente de Configuração AI</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted">Selecione um fornecedor IA para iniciar a configuração com parâmetros recomendados.</p>
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <div class="card h-100 border-primary text-center cursor-pointer hover-shadow transition-all">
                            <div class="card-body py-4">
                                <i class="fas fa-brain fs-1 text-primary mb-3"></i>
                                <h6 class="fw-bold">OpenAI</h6>
                                <span class="badge bg-primary-subtle text-primary mt-2">Standard</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-info text-center cursor-pointer hover-shadow transition-all">
                            <div class="card-body py-4">
                                <i class="fas fa-robot fs-1 text-info mb-3"></i>
                                <h6 class="fw-bold">DeepSeek</h6>
                                <span class="badge bg-info-subtle text-info mt-2">Programação</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-success text-center cursor-pointer hover-shadow transition-all">
                            <div class="card-body py-4">
                                <i class="fas fa-cube fs-1 text-success mb-3"></i>
                                <h6 class="fw-bold">Gemini (Google)</h6>
                                <span class="badge bg-success-subtle text-success mt-2">Multimodal</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-warning text-center cursor-pointer hover-shadow transition-all">
                            <div class="card-body py-4">
                                <i class="fas fa-comment fs-1 text-warning mb-3"></i>
                                <h6 class="fw-bold">Claude (Anthropic)</h6>
                                <span class="badge bg-warning-subtle text-warning mt-2">Longo Contexto</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-secondary text-center cursor-pointer hover-shadow transition-all">
                            <div class="card-body py-4">
                                <i class="fas fa-hdd fs-1 text-secondary mb-3"></i>
                                <h6 class="fw-bold">Ollama</h6>
                                <span class="badge bg-secondary-subtle text-secondary mt-2">Local/Privado</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<style>
.cursor-pointer { cursor: pointer; }
.hover-shadow:hover { box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; transform: translateY(-2px); }
.transition-all { transition: all .3s ease; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const testButtons = document.querySelectorAll('.btn-test-connection');
    testButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const providerId = this.getAttribute('data-id');
            const originalIcon = this.innerHTML;
            
            // Loading state
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            this.disabled = true;

            fetch('{{ route("ai.admin.providers.test") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ provider_id: providerId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Sucesso! ${data.message} (Tempo: ${data.time})`);
                } else {
                    alert(`Erro de Conectividade: ${data.message}`);
                }
            })
            .catch(error => {
                alert('Erro de rede ao tentar contactar o servidor.');
            })
            .finally(() => {
                this.innerHTML = originalIcon;
                this.disabled = false;
            });
        });
    });
});
</script>
@endsection

@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <!-- Page Title & Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="margin: 0; font-weight: 800; color: #0f172a; font-size: 1.6rem; letter-spacing: -0.5px;">
                <i class="fas fa-plug text-primary me-2"></i> Módulo de Integrações & APIs
            </h2>
            <p style="margin: 0; color: #64748b; font-size: 0.925rem;">
                Conecte o ERP Consulvolt ao PowerBI, ERPs parceiros, CRM e aplicações externas com segurança total.
            </p>
        </div>
        <div>
            <button class="btn btn-primary fw-bold px-3 py-2" data-bs-toggle="modal" data-bs-target="#generateTokenModal" style="border-radius: 10px; background: #2563eb;">
                <i class="fas fa-key me-1"></i> Nova Chave de API
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; background: #dcfce7; color: #15803d;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('new_api_token'))
        <div class="alert alert-warning border-0 shadow-sm mb-4" style="border-radius: 12px; background: #fef3c7; color: #92400e;">
            <div class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-2"></i> ATENÇÃO: Guarde a sua Chave de API agora!</div>
            <p class="mb-2 fs-7">Esta chave só será mostrada uma única vez por motivos de segurança.</p>
            <div class="input-group">
                <input type="text" id="newTokenInput" class="form-control fw-bold font-monospace bg-white" value="{{ session('new_api_token') }}" readonly>
                <button class="btn btn-dark fw-bold" onclick="navigator.clipboard.writeText(document.getElementById('newTokenInput').value); alert('Chave copiada para a área de transferência!');">
                    <i class="fas fa-copy me-1"></i> Copiar
                </button>
            </div>
        </div>
    @endif

    <div class="row g-4">
        <!-- PowerBI Integration Guide Card -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; background: #ffffff;">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 48px; height: 48px; border-radius: 12px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Integração PowerBI (JSON / OData Feed)</h5>
                            <span class="text-muted fs-8">Relatórios em Tempo Real e Dashboards BI</span>
                        </div>
                    </div>
                    <span class="badge bg-success-subtle text-success fw-bold px-3 py-2" style="border-radius: 8px;">Ativo</span>
                </div>

                <div class="card-body px-4 py-3">
                    <p class="text-secondary fs-7 mb-3">
                        Sincronize as suas vendas, salários e fluxo de tesouraria diretamente no <strong>Microsoft PowerBI Desktop / Service</strong> via Bearer Token.
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-bold fs-8 text-uppercase text-muted">URL do Feed de Vendas (Sales Feed)</label>
                        <div class="input-group">
                            <input type="text" class="form-control fs-7 font-monospace bg-light" value="{{ $baseUrl }}/sales" readonly id="pbiSalesUrl">
                            <button class="btn btn-outline-secondary fs-7" onclick="navigator.clipboard.writeText(document.getElementById('pbiSalesUrl').value); alert('URL de Vendas copiada!');">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold fs-8 text-uppercase text-muted">URL do Feed Financeiro / Tesouraria</label>
                        <div class="input-group">
                            <input type="text" class="form-control fs-7 font-monospace bg-light" value="{{ $baseUrl }}/financials" readonly id="pbiFinUrl">
                            <button class="btn btn-outline-secondary fs-7" onclick="navigator.clipboard.writeText(document.getElementById('pbiFinUrl').value); alert('URL Financeira copiada!');">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold fs-8 text-uppercase text-muted">URL do Feed de Recursos Humanos (Payroll)</label>
                        <div class="input-group">
                            <input type="text" class="form-control fs-7 font-monospace bg-light" value="{{ $baseUrl }}/hr" readonly id="pbiHrUrl">
                            <button class="btn btn-outline-secondary fs-7" onclick="navigator.clipboard.writeText(document.getElementById('pbiHrUrl').value); alert('URL de RH copiada!');">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 border">
                        <div class="fw-bold fs-8 text-dark mb-1"><i class="fas fa-info-circle text-primary me-1"></i> Como Conectar no PowerBI:</div>
                        <ol class="mb-0 fs-8 text-secondary ps-3">
                            <li>Abra o PowerBI Desktop &rarr; <strong>Obter Dados</strong> &rarr; <strong>Web</strong>.</li>
                            <li>Cole a URL do Feed acima.</li>
                            <li>Escolha o cabeçalho HTTP: <code>Authorization: Bearer [SUA_CHAVE_API]</code>.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Managed API Tokens List Card -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; background: #ffffff;">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 48px; height: 48px; border-radius: 12px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Chaves de API Ativas</h5>
                            <span class="text-muted fs-8">Gestão de Acessos Externos Sanctum</span>
                        </div>
                    </div>
                </div>

                <div class="card-body px-4 py-3">
                    @if($tokens->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-key text-muted mb-3" style="font-size: 2.5rem; opacity: 0.4;"></i>
                            <p class="text-muted mb-2 font-semibold">Nenhuma chave de API ativa para a sua conta.</p>
                            <button class="btn btn-sm btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#generateTokenModal">
                                Criar Primeira Chave
                            </button>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light fs-8 text-uppercase">
                                    <tr>
                                        <th>Nome da Aplicação</th>
                                        <th>Última Utilização</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tokens as $token)
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark fs-7">{{ $token->name }}</div>
                                                <span class="badge bg-light text-secondary border font-monospace fs-8">ID: {{ $token->id }}</span>
                                            </td>
                                            <td class="fs-8 text-muted">
                                                {{ $token->last_used_at ? $token->last_used_at->diffForHumans() : 'Nunca utilizada' }}
                                            </td>
                                            <td>
                                                <form action="{{ route('admin.integrations.keys.destroy', $token->id) }}" method="POST" onsubmit="return confirm('Tem a certeza que deseja revogar esta chave de API?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Revogar Chave">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Gerar Token -->
<div class="modal fade" id="generateTokenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-key text-primary me-2"></i> Gerar Nova Chave de API</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form action="{{ route('admin.integrations.keys.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Nome da Aplicação / Finalidade <span class="text-danger">*</span></label>
                        <input type="text" name="token_name" class="form-control" placeholder="ex: PowerBI Desktop - Financeiro" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Permissões da Chave</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="abilities[]" value="*" id="permAll" checked>
                            <label class="form-check-label fs-7" for="permAll">Acesso Total (Leitura & Escrita)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="abilities[]" value="powerbi:read" id="permPbi">
                            <label class="form-check-label fs-7" for="permPbi">Apenas Leitura PowerBI</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light border fw-bold" data-bs-dismiss="modal" style="border-radius:8px;">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold" style="border-radius:8px;"><i class="fas fa-check me-1"></i> Gerar Chave</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

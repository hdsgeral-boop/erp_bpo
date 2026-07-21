@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    .info-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    .info-value {
        font-size: 1.05rem;
        color: #1e293b;
        font-weight: 500;
        margin-bottom: 1.5rem;
    }
    .btn-edit {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border-radius: 10px;
        padding: 0.6rem 2rem;
        font-weight: 600;
        border: none;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
    }
    .btn-edit:hover { color: white; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4); }
    .role-badge { font-size: 0.9rem; padding: 0.5em 1em; border-radius: 8px; font-weight: 600; }
    .attachment-card {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 1rem;
        transition: all 0.2s;
        height: 100%;
        background: #f8fafc;
        display: flex;
        align-items: center;
    }
    .attachment-card:hover { border-color: #cbd5e1; background: #f1f5f9; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('entidades.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
                <i class="fas fa-arrow-left me-1"></i> Voltar à Listagem
            </a>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-id-card text-primary me-2"></i>Perfil: {{ $entidade->name }}</h2>
        </div>
        <a href="{{ route('entidades.edit', $entidade->id) }}" class="btn btn-edit">
            <i class="fas fa-edit me-2"></i> Editar Entidade
        </a>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card-premium p-4 text-center h-100 d-flex flex-column align-items-center">
                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 100px; height: 100px;">
                    <i class="fas fa-building text-primary fa-3x"></i>
                </div>
                <h4 class="fw-bold text-dark mb-1">{{ $entidade->name }}</h4>
                <p class="text-muted mb-3 font-monospace">NIF: {{ $entidade->nif ?: 'Não Definido' }}</p>
                
                <div class="d-flex justify-content-center gap-2 mb-4 flex-wrap">
                    @if($entidade->is_customer)
                        <span class="badge bg-primary role-badge">Cliente</span>
                    @endif
                    @if($entidade->is_supplier)
                        <span class="badge bg-info text-dark role-badge">Fornecedor</span>
                    @endif
                    @if(!$entidade->is_customer && !$entidade->is_supplier)
                        <span class="badge bg-secondary role-badge">Sem Papel Atribuído</span>
                    @endif
                </div>

                <div class="w-100 border-top pt-3 text-start">
                    <div class="info-label text-center">Estado da Entidade</div>
                    <div class="text-center mt-2">
                        @if($entidade->is_active)
                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2" style="border-radius: 8px;"><i class="fas fa-check-circle me-1"></i> Entidade Ativa no Sistema</span>
                        @else
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-3 py-2" style="border-radius: 8px;"><i class="fas fa-times-circle me-1"></i> Entidade Inativa</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card-premium p-4 p-md-5 h-100">
                <ul class="nav nav-pills mb-4 border-bottom pb-3" id="profileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold" id="geral-tab" data-bs-toggle="pill" data-bs-target="#geral" type="button" role="tab">Dados Gerais</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="anexos-tab" data-bs-toggle="pill" data-bs-target="#anexos" type="button" role="tab">
                            Documentos <span class="badge bg-secondary ms-1">{{ $entidade->attachments->count() }}</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="profileTabsContent">
                    <!-- Separador Dados Gerais -->
                    <div class="tab-pane fade show active" id="geral" role="tabpanel">
                        <div class="row">
                            <div class="col-12 mb-3"><h6 class="fw-bold text-dark border-bottom pb-2">Contactos</h6></div>
                            <div class="col-md-6">
                                <div class="info-label">Email Principal</div>
                                <div class="info-value">
                                    @if($entidade->email)
                                        <a href="mailto:{{ $entidade->email }}"><i class="fas fa-envelope text-primary me-2"></i>{{ $entidade->email }}</a>
                                    @else
                                        <span class="text-muted fst-italic">Não preenchido</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Telefone / Telemóvel</div>
                                <div class="info-value">
                                    @if($entidade->phone)
                                        <a href="tel:{{ $entidade->phone }}"><i class="fas fa-phone-alt text-primary me-2"></i>{{ $entidade->phone }}</a>
                                    @else
                                        <span class="text-muted fst-italic">Não preenchido</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="info-label">Website</div>
                                <div class="info-value">
                                    @if($entidade->website)
                                        <a href="{{ $entidade->website }}" target="_blank"><i class="fas fa-globe text-primary me-2"></i>{{ $entidade->website }}</a>
                                    @else
                                        <span class="text-muted fst-italic">Não preenchido</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12 mt-3 mb-3"><h6 class="fw-bold text-dark border-bottom pb-2">Morada Faturação</h6></div>
                            <div class="col-12">
                                <div class="info-label">Morada Completa</div>
                                <div class="info-value">{{ $entidade->address ?: 'Não preenchido' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Código Postal</div>
                                <div class="info-value">{{ $entidade->postal_code ?: '-' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Cidade</div>
                                <div class="info-value">{{ $entidade->city ?: '-' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">País</div>
                                <div class="info-value">{{ $entidade->country ?: '-' }}</div>
                            </div>

                            <div class="col-12 mt-3 mb-3"><h6 class="fw-bold text-dark border-bottom pb-2">Contabilidade</h6></div>
                            <div class="col-md-6">
                                <div class="info-label">Conta SNC Base</div>
                                <div class="info-value"><span class="badge bg-light text-dark border font-monospace px-3 py-2">{{ $entidade->account_code ?: 'Não preenchida' }}</span></div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Observações Internas</div>
                                <div class="info-value" style="white-space: pre-line;">{{ $entidade->observations ?: 'Sem observações.' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Separador Anexos -->
                    <div class="tab-pane fade" id="anexos" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="fw-bold text-dark m-0">Documentos e Anexos</h6>
                            <a href="{{ route('entidades.edit', $entidade->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Adicionar Novo</a>
                        </div>
                        
                        @if($entidade->attachments->count() > 0)
                            <div class="row g-3">
                                @foreach($entidade->attachments as $attachment)
                                    <div class="col-md-6">
                                        <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="text-decoration-none text-dark">
                                            <div class="attachment-card">
                                                <div class="bg-primary bg-opacity-10 rounded p-3 me-3">
                                                    <i class="fas fa-file-alt text-primary fa-2x"></i>
                                                </div>
                                                <div class="overflow-hidden">
                                                    <div class="fw-bold text-truncate mb-1" title="{{ $attachment->file_name }}">{{ $attachment->file_name }}</div>
                                                    <div class="text-muted small">
                                                        {{ number_format($attachment->file_size / 1024, 2) }} KB &bull; {{ $attachment->created_at->format('d/m/Y') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-folder-open text-muted fa-3x mb-3 opacity-50"></i>
                                <h5 class="text-muted">Sem Documentos</h5>
                                <p class="text-muted mb-0">Não existem ficheiros associados a esta entidade.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

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
    .avatar-circle-large {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background-color: #e2e8f0;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 3rem;
        margin: 0 auto;
    }
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
            <a href="{{ route('rh.funcionarios.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
                <i class="fas fa-arrow-left me-1"></i> Voltar à Listagem
            </a>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-id-badge text-primary me-2"></i>Perfil: {{ $employee->name }}</h2>
        </div>
        <a href="{{ route('rh.funcionarios.edit', $employee->id) }}" class="btn btn-edit">
            <i class="fas fa-edit me-2"></i> Editar Colaborador
        </a>
    </div>

    <div class="row g-4">
        <!-- Sidebar Profile -->
        <div class="col-md-4">
            <div class="card-premium p-4 text-center h-100 d-flex flex-column align-items-center">
                <div class="avatar-circle-large mb-4 shadow-sm border border-white border-4">
                    {{ strtoupper(substr($employee->name, 0, 1)) }}
                </div>
                <h4 class="fw-bold text-dark mb-1">{{ $employee->name }}</h4>
                <p class="text-primary fw-bold mb-1">{{ $employee->position ? $employee->position->title : 'Cargo não definido' }}</p>
                <p class="text-muted mb-4"><i class="fas fa-building me-1"></i> {{ $employee->department ? $employee->department->name : 'Sem Departamento' }}</p>
                
                <div class="d-flex justify-content-center gap-2 mb-4 flex-wrap">
                    @if($employee->is_active)
                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2"><i class="fas fa-check-circle me-1"></i>Ativo</span>
                    @else
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-3 py-2"><i class="fas fa-ban me-1"></i>Inativo</span>
                    @endif
                    @if($employee->is_retired)
                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 py-2"><i class="fas fa-bed me-1"></i>Reformado</span>
                    @endif
                </div>

                <div class="w-100 border-top pt-3 text-start mt-auto">
                    <div class="info-label text-center">Data de Admissão</div>
                    <div class="text-center mt-1 fw-bold text-dark">
                        {{ $employee->admission_date ? $employee->admission_date->format('d/m/Y') : 'Não Definida' }}
                        @if($employee->admission_date)
                            <div class="small text-muted fw-normal mt-1">{{ $employee->admission_date->diffForHumans() }} na empresa</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content area -->
        <div class="col-md-8">
            <div class="card-premium p-4 p-md-5 h-100">
                <ul class="nav nav-pills mb-4 border-bottom pb-3" id="profileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold" id="geral-tab" data-bs-toggle="pill" data-bs-target="#geral" type="button" role="tab">Dados Gerais</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="financas-tab" data-bs-toggle="pill" data-bs-target="#financas" type="button" role="tab">Informação Financeira</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="anexos-tab" data-bs-toggle="pill" data-bs-target="#anexos" type="button" role="tab">
                            Documentos <span class="badge bg-secondary ms-1">{{ $employee->attachments->count() }}</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="profileTabsContent">
                    <!-- Separador Dados Gerais -->
                    <div class="tab-pane fade show active" id="geral" role="tabpanel">
                        <div class="row">
                            <div class="col-12 mb-3"><h6 class="fw-bold text-dark border-bottom pb-2">Identificação e Contactos</h6></div>
                            <div class="col-md-6">
                                <div class="info-label">NIF</div>
                                <div class="info-value font-monospace">{{ $employee->nif ?: 'Não preenchido' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Nº Segurança Social (INSS)</div>
                                <div class="info-value font-monospace">{{ $employee->inss ?: 'Não preenchido' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Email Principal</div>
                                <div class="info-value">
                                    @if($employee->email)
                                        <a href="mailto:{{ $employee->email }}"><i class="fas fa-envelope text-primary me-2"></i>{{ $employee->email }}</a>
                                    @else
                                        <span class="text-muted fst-italic">Não preenchido</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Telefone / Telemóvel</div>
                                <div class="info-value">
                                    @if($employee->phone)
                                        <a href="tel:{{ $employee->phone }}"><i class="fas fa-phone-alt text-primary me-2"></i>{{ $employee->phone }}</a>
                                    @else
                                        <span class="text-muted fst-italic">Não preenchido</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="info-label">Morada</div>
                                <div class="info-value">{{ $employee->address ?: 'Não preenchida' }}</div>
                            </div>

                            <div class="col-12 mt-3 mb-3"><h6 class="fw-bold text-dark border-bottom pb-2">Acesso ao Sistema</h6></div>
                            <div class="col-12">
                                <div class="info-label">Perfil / Role</div>
                                <div class="info-value">
                                    @if($employee->role)
                                        <span class="badge bg-dark text-white border"><i class="fas fa-shield-alt me-1"></i>{{ $employee->role->name }}</span>
                                    @else
                                        <span class="text-muted fst-italic">Sem acesso ao sistema ERP (Sem Perfil)</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Separador Financeiro -->
                    <div class="tab-pane fade" id="financas" role="tabpanel">
                        <div class="row">
                            <div class="col-12 mb-3"><h6 class="fw-bold text-dark border-bottom pb-2">Remuneração Base Mensal</h6></div>
                            <div class="col-md-4">
                                <div class="info-label">Salário Base</div>
                                <div class="info-value text-success fw-bold">{{ number_format($employee->base_salary, 2, ',', '.') }} AOA</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Subsídio Alimentação</div>
                                <div class="info-value">{{ number_format($employee->subsidy_meal, 2, ',', '.') }} AOA</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Subsídio Transporte</div>
                                <div class="info-value">{{ number_format($employee->subsidy_transport, 2, ',', '.') }} AOA</div>
                            </div>
                            <div class="col-12">
                                <div class="info-label">Dias úteis previstos/mês</div>
                                <div class="info-value">{{ $employee->work_days }} dias</div>
                            </div>

                            <div class="col-12 mt-3 mb-3"><h6 class="fw-bold text-dark border-bottom pb-2">Dados Bancários</h6></div>
                            <div class="col-md-6">
                                <div class="info-label">Nome do Banco</div>
                                <div class="info-value">{{ $employee->bank_name ?: 'Não preenchido' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">IBAN</div>
                                <div class="info-value font-monospace">{{ $employee->iban ?: 'Não preenchido' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Separador Anexos -->
                    <div class="tab-pane fade" id="anexos" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="fw-bold text-dark m-0">Documentos e Contratos</h6>
                            <a href="{{ route('rh.funcionarios.edit', $employee->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Adicionar Novo</a>
                        </div>
                        
                        @if($employee->attachments->count() > 0)
                            <div class="row g-3">
                                @foreach($employee->attachments as $attachment)
                                    <div class="col-md-6">
                                        <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="text-decoration-none text-dark">
                                            <div class="attachment-card">
                                                <div class="bg-primary bg-opacity-10 rounded p-3 me-3">
                                                    <i class="fas fa-file-contract text-primary fa-2x"></i>
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
                                <p class="text-muted mb-0">Não existem ficheiros (ex: contratos, BI) associados a este colaborador.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

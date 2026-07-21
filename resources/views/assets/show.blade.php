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
    .asset-icon-large {
        width: 120px;
        height: 120px;
        border-radius: 20px;
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        color: #3b82f6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3.5rem;
        margin: 0 auto;
        box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
    }
    .timeline {
        border-left: 2px solid #e2e8f0;
        padding-left: 20px;
        margin-left: 10px;
        position: relative;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -27px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: #3b82f6;
        border: 2px solid #ffffff;
        box-shadow: 0 0 0 2px #bfdbfe;
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
            <a href="{{ route('ativos.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
                <i class="fas fa-arrow-left me-1"></i> Voltar à Listagem
            </a>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-box-open text-primary me-2"></i>Ativo: {{ $asset->code }}</h2>
        </div>
        <a href="{{ route('ativos.edit', $asset->id) }}" class="btn btn-edit">
            <i class="fas fa-edit me-2"></i> Editar Ativo
        </a>
    </div>

    <div class="row g-4">
        <!-- Sidebar Summary -->
        <div class="col-md-4">
            <div class="card-premium p-4 text-center h-100 d-flex flex-column align-items-center">
                <div class="asset-icon-large mb-4 border border-white border-4 shadow-sm">
                    <i class="fas fa-desktop"></i>
                </div>
                <h4 class="fw-bold text-dark mb-1">{{ $asset->name }}</h4>
                <p class="text-primary font-monospace fw-bold mb-1">{{ $asset->code }}</p>
                <p class="text-muted mb-4">{{ $asset->category->name }}</p>
                
                <div class="d-flex justify-content-center gap-2 mb-4">
                    @if($asset->status === 'active')
                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2"><i class="fas fa-check-circle me-1"></i>Em Utilização</span>
                    @elseif($asset->status === 'sold')
                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 py-2"><i class="fas fa-handshake me-1"></i>Vendido</span>
                    @elseif($asset->status === 'written_off')
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2"><i class="fas fa-times-circle me-1"></i>Abatido</span>
                    @endif
                </div>

                <div class="w-100 border-top pt-4 text-start mt-auto">
                    <div class="p-3 bg-light rounded border border-info mb-3">
                        <div class="info-label text-info"><i class="fas fa-map-marker-alt me-1"></i> Localização Atual</div>
                        <div class="fw-bold text-dark mb-1">{{ $asset->location ?: 'Não especificada' }}</div>
                        @if($asset->department)
                            <div class="small text-muted mb-1"><i class="fas fa-building me-1"></i> {{ $asset->department->name }}</div>
                        @endif
                        @if($asset->employee)
                            <div class="small text-primary fw-bold"><i class="fas fa-user-tie me-1"></i> Responsável: {{ $asset->employee->name }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content area -->
        <div class="col-md-8">
            <div class="card-premium p-4 p-md-5 h-100">
                <ul class="nav nav-pills mb-4 border-bottom pb-3" id="assetTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold" id="geral-tab" data-bs-toggle="pill" data-bs-target="#geral" type="button" role="tab">Detalhes Financeiros</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="historico-tab" data-bs-toggle="pill" data-bs-target="#historico" type="button" role="tab">Histórico de Movimentações</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="anexos-tab" data-bs-toggle="pill" data-bs-target="#anexos" type="button" role="tab">
                            Documentos <span class="badge bg-secondary ms-1">{{ $asset->attachments->count() }}</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="assetTabsContent">
                    <!-- Separador Geral/Finanças -->
                    <div class="tab-pane fade show active" id="geral" role="tabpanel">
                        <div class="row">
                            <div class="col-12 mb-3"><h6 class="fw-bold text-dark border-bottom pb-2">Aquisição</h6></div>
                            <div class="col-md-6">
                                <div class="info-label">Fornecedor</div>
                                <div class="info-value">
                                    @if($asset->vendor)
                                        <a href="{{ route('entidades.show', $asset->vendor->id) }}"><i class="fas fa-truck text-primary me-2"></i>{{ $asset->vendor->name }}</a>
                                    @else
                                        <span class="text-muted fst-italic">Não preenchido</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Data de Aquisição</div>
                                <div class="info-value">{{ $asset->purchase_date ? $asset->purchase_date->format('d/m/Y') : 'Desconhecida' }}</div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="info-label">Valor de Compra (Bruto)</div>
                                <div class="info-value text-primary fw-bold">{{ number_format($asset->purchase_value, 2, ',', '.') }} AOA</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Valor Residual (Atual)</div>
                                <div class="info-value text-success fw-bold">{{ number_format($asset->residual_value, 2, ',', '.') }} AOA</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Vida Útil (Anos)</div>
                                <div class="info-value">{{ $asset->useful_life_years ? $asset->useful_life_years . ' Anos' : 'Não definida' }}</div>
                            </div>

                            <div class="col-12 mt-3 mb-3"><h6 class="fw-bold text-dark border-bottom pb-2">Informação Contabilística (Categoria)</h6></div>
                            <div class="col-md-4">
                                <div class="info-label">Conta de Ativo</div>
                                <div class="info-value">{{ $asset->category->account_asset ?: '-' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Conta Amortizações Acum.</div>
                                <div class="info-value">{{ $asset->category->account_depreciation ?: '-' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Conta de Gastos (Amort.)</div>
                                <div class="info-value">{{ $asset->category->account_expense ?: '-' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Taxa Anual (%)</div>
                                <div class="info-value">{{ number_format($asset->category->depreciation_rate, 2, ',', '.') }} %</div>
                            </div>
                            
                            <div class="col-12 mt-3 mb-3"><h6 class="fw-bold text-dark border-bottom pb-2">Observações</h6></div>
                            <div class="col-12">
                                <p class="text-muted bg-light p-3 rounded border">{{ $asset->notes ?: 'Sem observações registadas.' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Separador Histórico -->
                    <div class="tab-pane fade" id="historico" role="tabpanel">
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark mb-3">Ciclo de Vida do Ativo</h6>
                            
                            @if($asset->movements->count() > 0)
                                <div class="timeline">
                                    @foreach($asset->movements as $movement)
                                        <div class="timeline-item">
                                            <div class="fw-bold text-dark">{{ $movement->movement_date->format('d/m/Y') }}</div>
                                            <div class="small text-primary mb-1">
                                                @if($movement->type === 'allocation')
                                                    <i class="fas fa-exchange-alt me-1"></i> Nova Alocação
                                                @elseif($movement->type === 'status_change')
                                                    <i class="fas fa-tags me-1"></i> Mudança de Estado
                                                @else
                                                    {{ ucfirst($movement->type) }}
                                                @endif
                                            </div>
                                            
                                            <div class="bg-light p-3 rounded border mt-2">
                                                @if($movement->type === 'allocation')
                                                    <div class="row g-2 small">
                                                        <div class="col-sm-6 border-end">
                                                            <div class="text-muted fw-bold mb-1">De:</div>
                                                            <div>Dep: {{ $movement->fromDepartment ? $movement->fromDepartment->name : '-' }}</div>
                                                            <div>Fun: {{ $movement->fromEmployee ? $movement->fromEmployee->name : '-' }}</div>
                                                            <div>Loc: {{ $movement->from_location ?: '-' }}</div>
                                                        </div>
                                                        <div class="col-sm-6 ps-sm-3">
                                                            <div class="text-success fw-bold mb-1">Para:</div>
                                                            <div>Dep: {{ $movement->toDepartment ? $movement->toDepartment->name : '-' }}</div>
                                                            <div>Fun: {{ $movement->toEmployee ? $movement->toEmployee->name : '-' }}</div>
                                                            <div>Loc: {{ $movement->to_location ?: '-' }}</div>
                                                        </div>
                                                    </div>
                                                @elseif($movement->type === 'status_change')
                                                    <div class="small">
                                                        O estado do ativo passou de <span class="badge bg-secondary">{{ $movement->from_status }}</span> para <span class="badge bg-info">{{ $movement->to_status }}</span>
                                                    </div>
                                                @endif
                                                
                                                @if($movement->notes)
                                                    <div class="mt-2 pt-2 border-top small text-muted">
                                                        <i class="fas fa-comment-alt me-1"></i> {{ $movement->notes }}
                                                    </div>
                                                @endif
                                                
                                                @if($movement->creator)
                                                    <div class="text-end mt-2">
                                                        <small class="text-muted fst-italic">Por: {{ $movement->creator->name }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-history text-muted fa-3x mb-3 opacity-50"></i>
                                    <p class="text-muted">Sem histórico de movimentações.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Separador Anexos -->
                    <div class="tab-pane fade" id="anexos" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="fw-bold text-dark m-0">Faturas, Garantias e Manuais</h6>
                            <a href="{{ route('ativos.edit', $asset->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Adicionar Novo</a>
                        </div>
                        
                        @if($asset->attachments->count() > 0)
                            <div class="row g-3">
                                @foreach($asset->attachments as $attachment)
                                    <div class="col-md-6">
                                        <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="text-decoration-none text-dark">
                                            <div class="attachment-card">
                                                <div class="bg-primary bg-opacity-10 rounded p-3 me-3">
                                                    <i class="fas fa-file-pdf text-primary fa-2x"></i>
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
                                <p class="text-muted mb-0">Não existem faturas, contratos ou manuais anexados a este equipamento.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

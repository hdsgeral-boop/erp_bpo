@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        background: #ffffff;
    }
    .audit-created { color: #10b981; }
    .audit-updated { color: #3b82f6; }
    .audit-deleted { color: #ef4444; }
    .audit-restored { color: #8b5cf6; }
    
    .table-audit th {
        background-color: #f8fafc;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: #64748b;
        font-weight: 700;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-history text-primary me-2"></i>Logs de Auditoria</h2>
        <p class="text-muted mt-1">Rastreabilidade completa de todas as operações realizadas no sistema.</p>
    </div>

    <div class="card-premium">
        <div class="p-4 border-bottom bg-light">
            <form action="{{ route('admin.logs.index') }}" method="GET" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label text-muted small fw-bold mb-1">Pesquisar Utilizador ou Entidade</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold mb-1">Tipo de Evento</label>
                    <select name="event" class="form-select">
                        <option value="">Todos os Eventos</option>
                        <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Criação</option>
                        <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Atualização</option>
                        <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Eliminação</option>
                        <option value="restored" {{ request('event') == 'restored' ? 'selected' : '' }}>Restauração</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1 fw-bold" style="border-radius: 8px;">Filtrar</button>
                    <a href="{{ route('admin.logs.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">Limpar</a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-audit align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Data / Hora</th>
                        <th>Utilizador</th>
                        <th>Evento</th>
                        <th>Módulo / Entidade</th>
                        <th>ID Entidade</th>
                        <th>Endereço IP</th>
                        <th class="text-center">Detalhes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    @php
                        $eventColor = 'text-muted';
                        $eventIcon = 'fa-info-circle';
                        $eventText = $log->event;
                        
                        if ($log->event === 'created') {
                            $eventColor = 'audit-created';
                            $eventIcon = 'fa-plus-circle';
                            $eventText = 'Criado';
                        } elseif ($log->event === 'updated') {
                            $eventColor = 'audit-updated';
                            $eventIcon = 'fa-edit';
                            $eventText = 'Atualizado';
                        } elseif ($log->event === 'deleted') {
                            $eventColor = 'audit-deleted';
                            $eventIcon = 'fa-trash-alt';
                            $eventText = 'Eliminado';
                        } elseif ($log->event === 'restored') {
                            $eventColor = 'audit-restored';
                            $eventIcon = 'fa-undo';
                            $eventText = 'Restaurado';
                        }
                    @endphp
                    <tr>
                        <td class="ps-4 text-nowrap">
                            <div class="fw-bold">{{ $log->created_at->format('d/m/Y') }}</div>
                            <div class="small text-muted">{{ $log->created_at->format('H:i:s') }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $log->user->name ?? 'Sistema' }}</div>
                            <div class="small text-muted">{{ $log->user->email ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <span class="fw-bold {{ $eventColor }}"><i class="fas {{ $eventIcon }} me-1"></i> {{ $eventText }}</span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ class_basename($log->auditable_type) }}</span>
                        </td>
                        <td class="fw-bold text-muted">#{{ $log->auditable_id }}</td>
                        <td class="small text-muted">{{ $log->ip_address }}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-light border text-primary" data-bs-toggle="modal" data-bs-target="#auditModal{{ $log->id }}">
                                <i class="fas fa-eye"></i> Ver
                            </button>
                        </td>
                    </tr>

                    <!-- Modal Detalhes Auditoria -->
                    <div class="modal fade" id="auditModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-light">
                                    <h5 class="modal-title fw-bold">
                                        <i class="fas fa-history me-2 text-primary"></i> Detalhes da Operação
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row mb-4">
                                        <div class="col-sm-6">
                                            <div class="small text-muted text-uppercase fw-bold">Utilizador</div>
                                            <div class="fw-medium">{{ $log->user->name ?? 'Sistema' }} ({{ $log->ip_address }})</div>
                                            <div class="small text-muted mt-1" style="word-break: break-all;">User Agent: {{ $log->user_agent }}</div>
                                        </div>
                                        <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                                            <div class="small text-muted text-uppercase fw-bold">Módulo (URL)</div>
                                            <div class="fw-medium">{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}</div>
                                            <div class="small text-muted mt-1">{{ $log->url }}</div>
                                        </div>
                                    </div>

                                    <div class="row g-4">
                                        @if(!empty($log->old_values))
                                        <div class="col-md-6">
                                            <h6 class="fw-bold text-danger"><i class="fas fa-minus-circle me-1"></i> Valores Anteriores</h6>
                                            <div class="bg-light p-3 rounded border" style="max-height: 400px; overflow-y: auto;">
                                                <pre class="mb-0" style="font-size: 0.85rem;">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @if(!empty($log->new_values))
                                        <div class="col-md-{{ empty($log->old_values) ? '12' : '6' }}">
                                            <h6 class="fw-bold text-success"><i class="fas fa-plus-circle me-1"></i> Valores Novos</h6>
                                            <div class="bg-light p-3 rounded border" style="max-height: 400px; overflow-y: auto;">
                                                <pre class="mb-0" style="font-size: 0.85rem;">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="modal-footer bg-light">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            Nenhum registo de auditoria encontrado com os filtros indicados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">
            {{ $logs->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection

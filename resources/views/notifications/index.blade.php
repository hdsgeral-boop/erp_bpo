@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header da Página -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                <i class="fas fa-bell text-warning"></i> Centro Centralizado de Notificações
            </h3>
            <p class="text-muted small mb-0">Gestão integrada de alertas, avisos fiscais e tarefas de todos os módulos do ERP.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <form action="{{ route('notifications.read_all') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-success btn-sm font-monospace fw-bold" title="Marcar todas as notificações como lidas">
                    <i class="fas fa-check-double me-1"></i> Marcar Todas como Lidas
                </button>
            </form>
        </div>
    </div>

    <!-- Cards Informativos -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 14px; background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-primary small fw-bold uppercase">Não Lidas</span>
                        <h3 class="fw-bold text-primary mb-0 mt-1" id="pageUnreadCount">{{ $unreadCount }}</h3>
                    </div>
                    <div class="p-3 bg-primary text-white rounded-circle">
                        <i class="fas fa-envelope-open-text fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 14px; background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-danger small fw-bold uppercase">Alertas Críticos</span>
                        <h3 class="fw-bold text-danger mb-0 mt-1">
                            {{ $recipients->getCollection()->filter(fn($r) => in_array($r->notification?->priority, ['CRITICAL', 'HIGH']))->count() }}
                        </h3>
                    </div>
                    <div class="p-3 bg-danger text-white rounded-circle">
                        <i class="fas fa-exclamation-triangle fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 14px; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-success small fw-bold uppercase">Total Registadas</span>
                        <h3 class="fw-bold text-success mb-0 mt-1">{{ $recipients->total() }}</h3>
                    </div>
                    <div class="p-3 bg-success text-white rounded-circle">
                        <i class="fas fa-layer-group fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3" style="border-radius: 14px; background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-purple small fw-bold uppercase" style="color: #7e22ce;">Canais Ativos</span>
                        <h3 class="fw-bold mb-0 mt-1" style="color: #7e22ce;">In-App & Email</h3>
                    </div>
                    <div class="p-3 text-white rounded-circle" style="background: #7e22ce;">
                        <i class="fas fa-rss fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros e Pesquisa -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('notifications.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-0" placeholder="Pesquisar mensagens..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <select name="status" class="form-select bg-light border-0" onchange="this.form.submit()">
                        <option value="">Status: Todos</option>
                        <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Não Lidas</option>
                        <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Lidas</option>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <select name="category" class="form-select bg-light border-0" onchange="this.form.submit()">
                        <option value="">Categoria: Todas</option>
                        <option value="Vendas" {{ request('category') === 'Vendas' ? 'selected' : '' }}>Vendas</option>
                        <option value="Stock" {{ request('category') === 'Stock' ? 'selected' : '' }}>Stock & Logística</option>
                        <option value="Compras" {{ request('category') === 'Compras' ? 'selected' : '' }}>Compras</option>
                        <option value="Financeiro" {{ request('category') === 'Financeiro' ? 'selected' : '' }}>Financeiro</option>
                        <option value="RH" {{ request('category') === 'RH' ? 'selected' : '' }}>Recursos Humanos</option>
                        <option value="Segurança" {{ request('category') === 'Segurança' ? 'selected' : '' }}>Segurança</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="priority" class="form-select bg-light border-0" onchange="this.form.submit()">
                        <option value="">Prioridade: Todas</option>
                        <option value="CRITICAL" {{ request('priority') === 'CRITICAL' ? 'selected' : '' }}>Crítica</option>
                        <option value="HIGH" {{ request('priority') === 'HIGH' ? 'selected' : '' }}>Alta</option>
                        <option value="NORMAL" {{ request('priority') === 'NORMAL' ? 'selected' : '' }}>Normal</option>
                        <option value="LOW" {{ request('priority') === 'LOW' ? 'selected' : '' }}>Baixa</option>
                    </select>
                </div>
                <div class="col-6 col-md-2 text-end">
                    <a href="{{ route('notifications.index') }}" class="btn btn-light w-100 fw-bold"><i class="fas fa-undo me-1"></i> Limpar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabela / Lista de Notificações -->
    <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
        <div class="card-body p-0">
            @if($recipients->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash text-muted fs-1 mb-3"></i>
                    <h5 class="fw-bold text-dark">Nenhuma notificação encontrada</h5>
                    <p class="text-muted small">Você está em dia! Não há notificações pendentes com os filtros selecionados.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light text-muted small uppercase">
                            <tr>
                                <th class="ps-4" style="width: 50px;">Tipo</th>
                                <th>Notificação</th>
                                <th>Categoria</th>
                                <th>Prioridade</th>
                                <th>Data / Hora</th>
                                <th class="text-end pe-4">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recipients as $r)
                                @php
                                    $n = $r->notification;
                                    $bgClass = $r->is_read ? 'bg-white' : 'bg-light font-weight-bold';
                                    $priorityBadge = match($n?->priority ?? 'NORMAL') {
                                        'CRITICAL' => 'bg-danger text-white',
                                        'HIGH' => 'bg-warning text-dark',
                                        'NORMAL' => 'bg-info text-dark',
                                        default => 'bg-secondary text-white',
                                    };
                                    $iconClass = match($n?->type ?? 'INFO') {
                                        'ALERT', 'ERROR' => 'fas fa-exclamation-triangle text-danger',
                                        'WARNING' => 'fas fa-exclamation-circle text-warning',
                                        'SUCCESS' => 'fas fa-check-circle text-success',
                                        'TASK', 'APPROVAL' => 'fas fa-tasks text-purple',
                                        default => 'fas fa-info-circle text-primary',
                                    };
                                @endphp
                                <tr class="{{ $bgClass }} border-bottom">
                                    <td class="ps-4 text-center">
                                        <i class="{{ $iconClass }} fs-4"></i>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if(!$r->is_read)
                                                <span class="badge bg-primary rounded-circle p-1" title="Não lida"> </span>
                                            @endif
                                            <div>
                                                <strong class="text-dark d-block">{{ $n?->title }}</strong>
                                                <span class="text-muted small">{{ $n?->message }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace">{{ $n?->category ?? 'Geral' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $priorityBadge }} font-monospace" style="font-size: 0.75rem;">{{ $n?->priority ?? 'NORMAL' }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted font-monospace">{{ $r->created_at ? $r->created_at->format('d/m/Y H:i') : '' }}</small>
                                        <br><small class="text-muted" style="font-size: 0.7rem;">{{ $r->created_at ? $r->created_at->diffForHumans() : '' }}</small>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            @if($n?->action_url)
                                                <a href="{{ $n->action_url }}" class="btn btn-sm btn-outline-primary" title="Abrir recurso">
                                                    <i class="fas fa-external-link-alt me-1"></i> Ver
                                                </a>
                                            @endif

                                            @if(!$r->is_read)
                                                <button class="btn btn-sm btn-outline-success" onclick="markSingleAsRead({{ $r->id }}, this)" title="Marcar como lida">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif

                                            <form action="{{ route('notifications.destroy', $r->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Deseja remover esta notificação?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3 d-flex justify-content-end">
                    {{ $recipients->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function markSingleAsRead(id, btnElement) {
    fetch(`/api/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
@endsection

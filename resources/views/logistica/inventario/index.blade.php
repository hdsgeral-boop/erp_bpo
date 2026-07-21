@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-tasks"></i> Sessões de Inventário</h2>
            <p class="text-muted">Gestão do processo de contagem física e regularização de stocks.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSessionModal">
            <i class="fas fa-plus"></i> Nova Sessão
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Data</th>
                            <th>Armazém</th>
                            <th>Responsável</th>
                            <th>Status</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $session)
                        <tr>
                            <td>#{{ $session->id }}</td>
                            <td>{{ \Carbon\Carbon::parse($session->date)->format('d/m/Y') }}</td>
                            <td>{{ $session->warehouse->name ?? 'N/D' }}</td>
                            <td>{{ $session->responsible_name }}</td>
                            <td>
                                @if($session->status == 'OPEN')
                                    <span class="badge bg-primary">ABERTO (Contagem)</span>
                                @elseif($session->status == 'REVIEW')
                                    <span class="badge bg-warning text-dark">EM REVISÃO</span>
                                @else
                                    <span class="badge bg-success">FECHADO</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($session->status == 'OPEN')
                                    <a href="{{ route('logistica.inventario.contagem', $session->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-list-ol"></i> Continuar Contagem
                                    </a>
                                @elseif($session->status == 'REVIEW')
                                    <a href="{{ route('logistica.inventario.review', $session->id) }}" class="btn btn-sm btn-outline-warning text-dark">
                                        <i class="fas fa-balance-scale"></i> Rever Diferenças
                                    </a>
                                @else
                                    <a href="{{ route('logistica.inventario.review', $session->id) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-eye"></i> Ver Resumo
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Nenhuma sessão de inventário registada.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Nova Sessão -->
    <div class="modal fade" id="createSessionModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('logistica.inventario.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Iniciar Sessão de Inventário</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small">Isto irá capturar o stock atual para comparar com as contagens físicas.</p>
                        <div class="mb-3">
                            <label class="form-label">Armazém a Inventariar</label>
                            <select name="warehouse_id" class="form-select" required>
                                <option value="">-- Selecione o Armazém --</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Data de Contagem</label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Responsável (Opcional)</label>
                            <input type="text" name="responsible_name" class="form-control" placeholder="Nome do responsável pela equipa de contagem">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Gerar Folha de Contagem</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

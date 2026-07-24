@extends('layouts.app')

@push('styles')
<style>
    .card-premium { border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .table-custom th { background-color: #f8f9fa; color: #475569; font-weight: 600; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-cash-register text-primary me-2"></i>Terminais POS e Impressoras</h2>
            <p class="text-muted mb-0">Associe Terminais e Impressoras Físicas a utilizadores de Caixa.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTerminalModal">
            <i class="fas fa-plus me-2"></i> Novo Terminal
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm"><i class="fas fa-check-circle me-2"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger shadow-sm"><i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}</div>
    @endif

    <div class="card card-premium">
        <div class="card-body p-0">
            <table class="table table-custom mb-0 table-hover align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">Nome do Registo/Caixa</th>
                        <th>Armazém Associado</th>
                        <th>Identificação do Terminal</th>
                        <th>Impressora (Tipo)</th>
                        <th>Endereço IP/MAC</th>
                        <th>Estado</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($terminals as $t)
                    <tr>
                        <td class="ps-4 fw-bold text-dark">{{ $t->name }}</td>
                        <td>{{ $t->warehouse->name ?? 'Global' }}</td>
                        <td>{{ $t->terminal_id ?? 'Auto' }}</td>
                        <td>
                            @if($t->printer_type == 'network') <i class="fas fa-network-wired text-info me-1"></i> Rede
                            @elseif($t->printer_type == 'usb') <i class="fab fa-usb text-secondary me-1"></i> USB
                            @elseif($t->printer_type == 'bluetooth') <i class="fab fa-bluetooth text-primary me-1"></i> Bluetooth
                            @else <i class="fas fa-print text-muted me-1"></i> Browser @endif
                        </td>
                        <td class="font-monospace text-muted">{{ $t->printer_address ?? 'N/A' }}</td>
                        <td>
                            @if($t->is_active)
                                <span class="badge bg-success">Ativo</span>
                            @else
                                <span class="badge bg-secondary">Inativo</span>
                            @endif
                            @if($t->status === 'OPEN')
                                <span class="badge bg-warning text-dark"><i class="fas fa-lock-open"></i> Aberto</span>
                            @else
                                <span class="badge bg-dark"><i class="fas fa-lock"></i> Fechado</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editTerminalModal{{ $t->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="/api/v1/admin/pos-settings/{{ $t->id }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tem a certeza que deseja apagar este Terminal POS?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editTerminalModal{{ $t->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form action="/api/v1/admin/pos-settings/{{ $t->id }}" method="POST">
                                @csrf @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Editar Terminal: {{ $t->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Nome da Caixa <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control" value="{{ $t->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Armazém/Loja</label>
                                            <select name="warehouse_id" class="form-select">
                                                <option value="">(Global) Qualquer armazém</option>
                                                @foreach($warehouses as $w)
                                                    <option value="{{ $w->id }}" {{ $t->warehouse_id == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Identificação Física (Terminal ID)</label>
                                            <input type="text" name="terminal_id" class="form-control" value="{{ $t->terminal_id }}">
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Tipo de Impressora</label>
                                                <select name="printer_type" class="form-select">
                                                    <option value="browser" {{ $t->printer_type == 'browser' ? 'selected' : '' }}>Navegador (PDF)</option>
                                                    <option value="network" {{ $t->printer_type == 'network' ? 'selected' : '' }}>Rede (IP/Epson)</option>
                                                    <option value="usb" {{ $t->printer_type == 'usb' ? 'selected' : '' }}>USB Direto</option>
                                                    <option value="bluetooth" {{ $t->printer_type == 'bluetooth' ? 'selected' : '' }}>Bluetooth</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Endereço IP/MAC</label>
                                                <input type="text" name="printer_address" class="form-control" value="{{ $t->printer_address }}" placeholder="192.168.1.50">
                                            </div>
                                        </div>
                                        <div class="form-check form-switch mt-3">
                                            <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $t->is_active ? 'checked' : '' }}>
                                            <label class="form-check-label">Terminal Ativo</label>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-primary">Guardar Alterações</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-cash-register fa-2x mb-3 d-block opacity-50"></i>
                            Não existem Terminais POS configurados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createTerminalModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="/api/v1/admin/pos-settings" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Novo Terminal POS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome da Caixa <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Caixa 1 - Principal" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Armazém/Loja</label>
                        <select name="warehouse_id" class="form-select">
                            <option value="">(Global) Qualquer armazém</option>
                            @foreach($warehouses as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Identificação Física (Terminal ID)</label>
                        <input type="text" name="terminal_id" class="form-control" placeholder="T01">
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tipo de Impressora</label>
                            <select name="printer_type" class="form-select">
                                <option value="browser">Navegador (PDF)</option>
                                <option value="network">Rede (IP/Epson)</option>
                                <option value="usb">USB Direto</option>
                                <option value="bluetooth">Bluetooth</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Endereço IP/MAC</label>
                            <input type="text" name="printer_address" class="form-control" placeholder="192.168.1.50">
                        </div>
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                        <label class="form-check-label">Terminal Ativo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Criar Terminal</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

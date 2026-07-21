@extends('layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    .card-premium {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    .nav-tabs-custom {
        border-bottom: 2px solid #e2e8f0;
        padding: 0 1.5rem;
        background: #f8fafc;
    }
    .nav-tabs-custom .nav-link {
        color: #64748b;
        font-weight: 600;
        border: none;
        padding: 1rem 1.5rem;
        border-bottom: 3px solid transparent;
        transition: all 0.2s;
    }
    .nav-tabs-custom .nav-link:hover {
        color: #3b82f6;
    }
    .nav-tabs-custom .nav-link.active {
        color: #3b82f6;
        background: transparent;
        border-bottom: 3px solid #3b82f6;
    }
    .table-custom {
        margin-bottom: 0;
    }
    .table-custom thead th {
        background-color: #ffffff;
        color: #475569;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 1rem 1.5rem;
        border-bottom: 2px solid #e2e8f0;
    }
    .table-custom tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-custom tbody tr:hover {
        background-color: #f8fafc;
    }
    .badge-custom {
        padding: 0.5em 1em;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.75rem;
    }
    .badge-success-custom { background: #dcfce7; color: #166534; }
    .badge-warning-custom { background: #fef3c7; color: #b45309; }
    .badge-danger-custom { background: #fee2e2; color: #b91c1c; }
    .btn-action {
        border-radius: 8px;
        padding: 0.4rem 0.8rem;
        transition: all 0.2s;
    }
    .btn-action:hover {
        background: #f1f5f9;
        transform: translateY(-2px);
    }
    .btn-add-new {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border-radius: 10px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
        transition: all 0.2s;
    }
    .btn-add-new:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4);
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-file-invoice-dollar text-primary me-2"></i>Faturação</h2>
            <p class="text-muted mb-0">Gestão de Documentos de Venda e Transporte.</p>
        </div>
        <a href="{{ route('vendas.pos') }}" class="btn btn-add-new"><i class="fas fa-desktop me-2"></i> Novo Documento (POS)</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm" style="border-radius: 10px; border-left: 4px solid #10b981;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger shadow-sm" style="border-radius: 10px; border-left: 4px solid #ef4444;">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card-premium">
        <!-- Tabs -->
        <ul class="nav nav-tabs nav-tabs-custom" id="docsTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="faturas-tab" data-bs-toggle="tab" data-bs-target="#faturas" type="button" role="tab"><i class="fas fa-file-invoice me-1"></i> Faturas</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="orcamentos-tab" data-bs-toggle="tab" data-bs-target="#orcamentos" type="button" role="tab"><i class="fas fa-file-signature me-1"></i> Orçamentos & Pró-formas</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="guias-tab" data-bs-toggle="tab" data-bs-target="#guias" type="button" role="tab"><i class="fas fa-truck me-1"></i> Guias</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="encomendas-tab" data-bs-toggle="tab" data-bs-target="#encomendas" type="button" role="tab"><i class="fas fa-box-open me-1"></i> Encomendas</button>
            </li>
        </ul>

        <div class="tab-content p-0" id="docsTabContent">
            <!-- Faturas (FR, FS, FT) -->
            <div class="tab-pane fade show active" id="faturas" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Documento</th>
                                <th>Cliente</th>
                                <th>NIF</th>
                                <th>Total (Kz)</th>
                                <th>Estado</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales->whereIn('doc_type', ['FR', 'FS', 'FT']) as $sale)
                            <tr class="{{ $sale->status == 'CANCELADO' ? 'opacity-50' : '' }}">
                                <td>{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</td>
                                <td class="fw-bold text-primary">{{ $sale->doc_number }}</td>
                                <td>{{ $sale->customer->name ?? 'Consumidor Final' }}</td>
                                <td class="text-muted">{{ $sale->customer->nif ?? '999999999' }}</td>
                                <td class="fw-bold">{{ number_format($sale->total_amount, 2, ',', '.') }}</td>
                                <td>
                                    @if($sale->status == 'CONCLUIDO')
                                        <span class="badge-custom badge-success-custom"><i class="fas fa-check-circle me-1"></i> PAGO</span>
                                    @elseif($sale->status == 'PENDENTE_PAGAMENTO')
                                        <span class="badge-custom badge-warning-custom"><i class="fas fa-clock me-1"></i> POR PAGAR</span>
                                    @elseif($sale->status == 'CANCELADO')
                                        <span class="badge-custom badge-danger-custom"><i class="fas fa-ban me-1"></i> ANULADO</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-action" type="button" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius: 12px; border: none;">
                                            <li><a class="dropdown-item py-2" href="{{ route('vendas.pdf', $sale->id) }}" target="_blank"><i class="fas fa-print text-primary me-2"></i> Imprimir</a></li>
                                            @if($sale->status != 'CANCELADO')
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('vendas.cancel', $sale->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-danger py-2" onclick="return confirm('Tem certeza absoluta que deseja anular este documento? O stock será revertido.')"><i class="fas fa-times-circle me-2"></i> Anular Documento</button>
                                                </form>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @if($sales->whereIn('doc_type', ['FR', 'FS', 'FT'])->count() == 0)
                                <tr><td colspan="7" class="text-center py-5 text-muted"><i class="fas fa-folder-open fa-2x mb-2 d-block"></i> Nenhum documento encontrado.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Orçamentos e Proformas (OR, PP) -->
            <div class="tab-pane fade" id="orcamentos" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Documento</th>
                                <th>Cliente</th>
                                <th>Total (Kz)</th>
                                <th>Estado</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales->whereIn('doc_type', ['OR', 'PP']) as $sale)
                            <tr class="{{ $sale->status == 'CANCELADO' ? 'opacity-50' : '' }}">
                                <td>{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</td>
                                <td class="fw-bold text-primary">{{ $sale->doc_number }}</td>
                                <td>{{ $sale->customer->name ?? 'Consumidor Final' }}</td>
                                <td class="fw-bold">{{ number_format($sale->total_amount, 2, ',', '.') }}</td>
                                <td>
                                    @if($sale->status == 'CANCELADO')
                                        <span class="badge-custom badge-danger-custom">ANULADO</span>
                                    @else
                                        <span class="badge-custom badge-success-custom">ATIVO</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-action" type="button" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius: 12px; border: none;">
                                            <li><a class="dropdown-item py-2" href="{{ route('vendas.pdf', $sale->id) }}" target="_blank"><i class="fas fa-print text-primary me-2"></i> Imprimir</a></li>
                                            @if($sale->status != 'CANCELADO')
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('vendas.cancel', $sale->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-danger py-2" onclick="return confirm('Tem certeza que deseja anular?')"><i class="fas fa-times-circle me-2"></i> Anular</button>
                                                </form>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Guias (GT) -->
            <div class="tab-pane fade" id="guias" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Documento</th>
                                <th>Cliente</th>
                                <th>Total (Kz)</th>
                                <th>Estado</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales->whereIn('doc_type', ['GT']) as $sale)
                            <tr class="{{ $sale->status == 'CANCELADO' ? 'opacity-50' : '' }}">
                                <td>{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</td>
                                <td class="fw-bold text-primary">{{ $sale->doc_number }}</td>
                                <td>{{ $sale->customer->name ?? 'Consumidor Final' }}</td>
                                <td class="fw-bold">{{ number_format($sale->total_amount, 2, ',', '.') }}</td>
                                <td>
                                    @if($sale->status == 'CANCELADO')
                                        <span class="badge-custom badge-danger-custom">ANULADO</span>
                                    @else
                                        <span class="badge-custom badge-success-custom">CONCLUÍDO</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-action" type="button" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius: 12px; border: none;">
                                            <li><a class="dropdown-item py-2" href="{{ route('vendas.pdf', $sale->id) }}" target="_blank"><i class="fas fa-print text-primary me-2"></i> Imprimir</a></li>
                                            @if($sale->status != 'CANCELADO')
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('vendas.cancel', $sale->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-danger py-2" onclick="return confirm('Tem certeza que deseja anular?')"><i class="fas fa-times-circle me-2"></i> Anular</button>
                                                </form>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Encomendas (EN) -->
            <div class="tab-pane fade" id="encomendas" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Documento</th>
                                <th>Cliente</th>
                                <th>Total (Kz)</th>
                                <th>Estado</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales->whereIn('doc_type', ['EN']) as $sale)
                            <tr class="{{ $sale->status == 'CANCELADO' ? 'opacity-50' : '' }}">
                                <td>{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</td>
                                <td class="fw-bold text-primary">{{ $sale->doc_number }}</td>
                                <td>{{ $sale->customer->name ?? 'Consumidor Final' }}</td>
                                <td class="fw-bold">{{ number_format($sale->total_amount, 2, ',', '.') }}</td>
                                <td>
                                    @if($sale->status == 'PENDENTE')
                                        <span class="badge-custom badge-warning-custom">PENDENTE</span>
                                    @elseif($sale->status == 'CANCELADO')
                                        <span class="badge-custom badge-danger-custom">ANULADO</span>
                                    @else
                                        <span class="badge-custom badge-success-custom">{{ $sale->status }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-action" type="button" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius: 12px; border: none;">
                                            <li><a class="dropdown-item py-2" href="{{ route('vendas.pdf', $sale->id) }}" target="_blank"><i class="fas fa-print text-primary me-2"></i> Imprimir</a></li>
                                            @if($sale->status != 'CANCELADO')
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('vendas.cancel', $sale->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-danger py-2" onclick="return confirm('Tem certeza que deseja anular?')"><i class="fas fa-times-circle me-2"></i> Anular</button>
                                                </form>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Add Bootstrap Tabs Script if not already loaded in layout -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
@endsection

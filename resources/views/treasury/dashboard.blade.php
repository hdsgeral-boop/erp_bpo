@extends('layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    .card-premium {
        background: #ffffff; border: none; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); padding: 1.5rem; height: 100%;
    }
    .stat-value { font-size: 2rem; font-weight: 700; color: #1e293b; }
    .stat-label { font-size: 0.85rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
    .table-custom { margin-bottom: 0; }
    .table-custom thead th { background-color: #f8fafc; color: #475569; font-weight: 600; font-size: 0.8rem; padding: 1rem; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; }
    .table-custom tbody td { padding: 1rem; vertical-align: middle; color: #1e293b; border-bottom: 1px solid #f1f5f9; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h2 class="fw-bold text-dark"><i class="fas fa-wallet text-primary me-2"></i>Dashboard de Tesouraria</h2>
        <p class="text-muted">Resumo de fluxos de caixa e posições bancárias.</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card-premium d-flex align-items-center">
                <div class="rounded-circle bg-success-subtle text-success d-flex justify-content-center align-items-center me-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div>
                    <div class="stat-label">Total Recebimentos</div>
                    <div class="stat-value text-success">{{ number_format($stats['total_receipts'], 2, ',', '.') }} <small class="text-muted fs-6">Kz</small></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-premium d-flex align-items-center">
                <div class="rounded-circle bg-danger-subtle text-danger d-flex justify-content-center align-items-center me-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <div>
                    <div class="stat-label">Total Pagamentos</div>
                    <div class="stat-value text-danger">{{ number_format($stats['total_payments'], 2, ',', '.') }} <small class="text-muted fs-6">Kz</small></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-premium d-flex align-items-center">
                <div class="rounded-circle bg-primary-subtle text-primary d-flex justify-content-center align-items-center me-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                    <i class="fas fa-balance-scale"></i>
                </div>
                <div>
                    <div class="stat-label">Saldo Bancário (Extratos)</div>
                    <div class="stat-value text-primary">{{ number_format($stats['balance'], 2, ',', '.') }} <small class="text-muted fs-6">Kz</small></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card-premium">
                <h5 class="fw-bold mb-4">Últimos Documentos Emitidos</h5>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Tipo</th>
                                <th>Referência</th>
                                <th class="text-end">Valor (Kz)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentDocs as $doc)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($doc->doc_date)->format('d/m/Y') }}</td>
                                <td>
                                    @if($doc->type == 'PG')
                                        <span class="badge bg-danger-subtle text-danger border">Pagamento</span>
                                    @else
                                        <span class="badge bg-success-subtle text-success border">Recebimento</span>
                                    @endif
                                </td>
                                <td>{{ $doc->reference ?? '-' }}</td>
                                <td class="text-end fw-bold">{{ number_format($doc->total_value, 2, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">Sem documentos recentes.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 text-end">
                    <a href="{{ route('tesouraria.documents.index') }}" class="btn btn-sm btn-light border">Ver Todos <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card-premium">
                <h5 class="fw-bold mb-4">Últimos Movimentos Bancários</h5>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Descrição</th>
                                <th>D/C</th>
                                <th class="text-end">Valor (Kz)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentStatements as $stmt)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($stmt->date)->format('d/m/Y') }}</td>
                                <td>{{ Str::limit($stmt->description, 30) }}</td>
                                <td>
                                    @if($stmt->type_dc == 'D')
                                        <span class="text-success fw-bold">D</span>
                                    @else
                                        <span class="text-danger fw-bold">C</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold">{{ number_format($stmt->value, 2, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">Sem movimentos recentes.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 text-end">
                    <a href="{{ route('tesouraria.bank_statements.index') }}" class="btn btn-sm btn-light border">Ver Todos <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

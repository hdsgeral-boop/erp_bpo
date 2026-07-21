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
        <h2 class="fw-bold text-dark"><i class="fas fa-chart-bar text-warning me-2"></i>Resumo Contabilístico</h2>
        <p class="text-muted">Apuramento de balancetes, proveitos e custos.</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card-premium d-flex align-items-center">
                <div class="rounded-circle bg-info-subtle text-info d-flex justify-content-center align-items-center me-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div>
                    <div class="stat-label">Total Débitos</div>
                    <div class="stat-value text-info">{{ number_format($stats['total_debits'], 2, ',', '.') }} <small class="text-muted fs-6">Kz</small></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-premium d-flex align-items-center">
                <div class="rounded-circle bg-warning-subtle text-warning d-flex justify-content-center align-items-center me-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <div>
                    <div class="stat-label">Total Créditos</div>
                    <div class="stat-value text-warning">{{ number_format($stats['total_credits'], 2, ',', '.') }} <small class="text-muted fs-6">Kz</small></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-premium d-flex align-items-center">
                <div class="rounded-circle bg-primary-subtle text-primary d-flex justify-content-center align-items-center me-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                    <i class="fas fa-list-ol"></i>
                </div>
                <div>
                    <div class="stat-label">Contas no Plano</div>
                    <div class="stat-value text-primary">{{ $stats['accounts_count'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="card-premium">
                <h5 class="fw-bold mb-4">Últimos Lançamentos (Diário Geral)</h5>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Data Lanc.</th>
                                <th>Conta</th>
                                <th>Descrição / Movimento</th>
                                <th>Doc / Ref</th>
                                <th class="text-end">Débito (D)</th>
                                <th class="text-end">Crédito (C)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentEntries as $entry)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($entry->entry_date)->format('d/m/Y') }}</td>
                                <td class="fw-bold">{{ $entry->account_code }}</td>
                                <td>{{ Str::limit($entry->description, 40) }}</td>
                                <td>{{ $entry->doc_number }}</td>
                                <td class="text-end fw-bold text-info">
                                    {{ $entry->type_dc == 'D' ? number_format($entry->value, 2, ',', '.') : '' }}
                                </td>
                                <td class="text-end fw-bold text-warning">
                                    {{ $entry->type_dc == 'C' ? number_format($entry->value, 2, ',', '.') : '' }}
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-3">Sem lançamentos recentes.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 text-end">
                    <a href="{{ route('contabilidade.journals.index') }}" class="btn btn-sm btn-light border">Ver Todos Diários <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

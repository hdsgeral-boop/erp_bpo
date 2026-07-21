@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    .table-custom thead th {
        background-color: #ffffff;
        color: #475569;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        padding: 1rem 1.5rem;
        border-bottom: 2px solid #e2e8f0;
    }
    .table-custom tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-file-invoice-dollar text-primary me-2"></i>Faturas de Fornecedores</h2>
            <p class="text-muted mb-0">Gestão de faturas de compras, despesas e aprovisionamento.</p>
        </div>
        <div>
            <a href="{{ route('compras.faturas.create') }}" class="btn btn-primary shadow-sm" style="border-radius: 8px;">
                <i class="fas fa-plus me-2"></i>Registar Fatura
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm" style="border-radius: 10px;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card-premium">
        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th>Nº Fatura</th>
                        <th>Fornecedor</th>
                        <th>Data de Emissão</th>
                        <th class="text-end">Total (AKZ)</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                    <tr>
                        <td class="fw-bold text-primary">{{ $inv->invoice_number }}</td>
                        <td class="fw-bold text-dark">{{ $inv->supplier ? $inv->supplier->name : 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($inv->date)->format('d/m/Y') }}</td>
                        <td class="text-end fw-bold">{{ number_format($inv->total_amount, 2, ',', '.') }}</td>
                        <td class="text-center">
                            @if($inv->status == 'CONCLUDED')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">Registada</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-2 rounded-pill">{{ $inv->status }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-light text-primary border" title="Ver Detalhes (Em breve)">
                                <i class="fas fa-eye"></i> Analisar
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-file-invoice-dollar fa-2x mb-3 d-block opacity-50"></i>
                            Nenhuma fatura de fornecedor registada.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

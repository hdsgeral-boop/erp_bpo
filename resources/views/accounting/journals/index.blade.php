@extends('layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    .card-premium {
        background: #ffffff; border: none; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); padding: 1.5rem;
    }
    .table-custom { margin-bottom: 0; }
    .table-custom thead th { background-color: #f8fafc; color: #475569; font-weight: 600; font-size: 0.85rem; padding: 1rem 1.5rem; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; }
    .table-custom tbody td { padding: 1rem 1.5rem; vertical-align: middle; color: #1e293b; border-bottom: 1px solid #f1f5f9; }
    .btn-primary-custom { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; border-radius: 10px; padding: 0.6rem 1.5rem; font-weight: 600; border: none; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-book text-warning me-2"></i>Lançamentos / Diários</h2>
            <p class="text-muted mb-0">Consulta de entradas no diário geral e apuramentos.</p>
        </div>
        <a href="{{ route('contabilidade.journals.create') }}" class="btn btn-primary-custom"><i class="fas fa-plus me-2"></i> Novo Lançamento Manual</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card-premium">
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Data Lanc.</th>
                        <th>Diário</th>
                        <th>Nº Doc</th>
                        <th>Conta</th>
                        <th>Descrição</th>
                        <th class="text-end">Débito (D)</th>
                        <th class="text-end">Crédito (C)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lines as $line)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($line->entry_date)->format('d/m/Y') }}</td>
                        <td>{{ $line->journal->code ?? 'N/A' }}</td>
                        <td>{{ $line->doc_number }}</td>
                        <td class="fw-bold">{{ $line->account_code }}</td>
                        <td>{{ Str::limit($line->description, 30) }}</td>
                        <td class="text-end text-info fw-bold">
                            {{ $line->type_dc == 'D' ? number_format($line->value, 2, ',', '.') : '' }}
                        </td>
                        <td class="text-end text-warning fw-bold">
                            {{ $line->type_dc == 'C' ? number_format($line->value, 2, ',', '.') : '' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Nenhum lançamento contabilístico registado.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

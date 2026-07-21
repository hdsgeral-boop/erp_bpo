@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-percent text-primary me-2"></i>Impostos e Taxas (Taxes)</h2>
            <p class="text-muted mb-0">Gestão centralizada de regras fiscais (IVA, Retenções, Isenções).</p>
        </div>
        <a href="{{ route('config.taxes.create') }}" class="btn btn-primary fw-bold" style="border-radius: 10px;">
            <i class="fas fa-plus me-2"></i> Adicionar Regra Fiscal
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm" style="border-radius: 10px;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Código</th>
                        <th>Nome / Descrição</th>
                        <th class="text-center">Tipo</th>
                        <th class="text-center">Taxa (%)</th>
                        <th>Motivo de Isenção</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($taxes as $tax)
                    <tr class="{{ !$tax->is_active ? 'table-secondary text-muted' : '' }}">
                        <td class="ps-4 fw-bold">{{ $tax->code }}</td>
                        <td>
                            <div class="fw-bold">{{ $tax->name }}</div>
                            <small class="text-muted">{{ $tax->company->name ?? 'Global' }}</small>
                        </td>
                        <td class="text-center">
                            @if($tax->type === 'VAT')
                                <span class="badge bg-info text-dark">IVA</span>
                            @elseif($tax->type === 'RETENTION')
                                <span class="badge bg-warning text-dark">Retenção</span>
                            @elseif($tax->type === 'STAMP')
                                <span class="badge bg-secondary">Selo</span>
                            @else
                                <span class="badge bg-light text-dark">{{ $tax->type }}</span>
                            @endif
                        </td>
                        <td class="text-center fw-bold fs-5 {{ $tax->rate == 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($tax->rate, 2, ',', '.') }}%
                        </td>
                        <td class="small fst-italic">
                            {{ $tax->exemption_reason ?: '-' }}
                        </td>
                        <td class="text-center">
                            @if($tax->is_active)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="fas fa-check"></i> Ativo</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger"><i class="fas fa-times"></i> Inativo</span>
                            @endif
                        </td>
                        <td class="text-center pe-4">
                            <a href="{{ route('config.taxes.edit', $tax->id) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($tax->is_active)
                            <form action="{{ route('config.taxes.destroy', $tax->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Desativar" onclick="return confirm('Desativar este imposto?')">
                                    <i class="fas fa-power-off"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-calculator fa-3x mb-3 opacity-50"></i>
                            <p>Ainda não foram configuradas taxas de imposto.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

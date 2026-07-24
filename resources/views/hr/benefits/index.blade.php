@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    .badge-benefit { background-color: #d1fae5; color: #047857; font-weight: 700; border: 1px solid #a7f3d0; }
    .badge-deduction { background-color: #fee2e2; color: #b91c1c; font-weight: 700; border: 1px solid #fca5a5; }
    .badge-tax-free { background-color: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-extrabold text-dark mb-1">
                <i class="fas fa-gift text-primary me-2"></i> Benefícios e Deduções de Processamento
            </h2>
            <p class="text-muted small mb-0">Subsídios de alimentação, transporte, residência e descontos diversos.</p>
        </div>
        <button type="button" class="btn btn-primary fw-bold px-4 py-2" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#createBenefitModal">
            <i class="fas fa-plus me-2"></i> Adicionar Benefício/Dedução
        </button>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Table Card -->
    <div class="card-premium overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">DESIGNAÇÃO</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">TIPO</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">INCIDÊNCIA IRT</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">INCIDÊNCIA INSS</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">VALOR PADRÃO</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold text-end">AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Exemplo Padrão 1 -->
                    <tr>
                        <td class="py-3 px-4 fw-bold text-dark">Subsídio de Alimentação (Isento até limite)</td>
                        <td class="py-3 px-4"><span class="badge badge-benefit px-3 py-1">Provento / Subsídio</span></td>
                        <td class="py-3 px-4"><span class="badge badge-tax-free px-3 py-1">Isento até 30.000 Kz</span></td>
                        <td class="py-3 px-4"><span class="badge badge-tax-free px-3 py-1">Isento</span></td>
                        <td class="py-3 px-4 fw-extrabold text-dark">30.000,00 Kz</td>
                        <td class="py-3 px-4 text-end">
                            <span class="badge bg-secondary-subtle text-secondary px-2 py-1">Padrão do Sistema</span>
                        </td>
                    </tr>
                    <!-- Exemplo Padrão 2 -->
                    <tr>
                        <td class="py-3 px-4 fw-bold text-dark">Subsídio de Transporte (Isento até limite)</td>
                        <td class="py-3 px-4"><span class="badge badge-benefit px-3 py-1">Provento / Subsídio</span></td>
                        <td class="py-3 px-4"><span class="badge badge-tax-free px-3 py-1">Isento até 30.000 Kz</span></td>
                        <td class="py-3 px-4"><span class="badge badge-tax-free px-3 py-1">Isento</span></td>
                        <td class="py-3 px-4 fw-extrabold text-dark">30.000,00 Kz</td>
                        <td class="py-3 px-4 text-end">
                            <span class="badge bg-secondary-subtle text-secondary px-2 py-1">Padrão do Sistema</span>
                        </td>
                    </tr>

                    @foreach($benefits as $b)
                    <tr>
                        <td class="py-3 px-4 fw-bold text-dark">{{ $b->name }}</td>
                        <td class="py-3 px-4">
                            @if(strtolower($b->type) === 'benefit' || strtolower($b->type) === 'provento')
                                <span class="badge badge-benefit px-3 py-1">Provento / Subsídio</span>
                            @else
                                <span class="badge badge-deduction px-3 py-1">Desconto / Retenção</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <span class="badge {{ $b->is_taxable ? 'bg-primary-subtle text-primary border' : 'badge-tax-free' }} px-3 py-1">
                                {{ $b->is_taxable ? 'Sujeito a IRT' : 'Isento' }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="badge {{ $b->is_taxable ? 'bg-primary-subtle text-primary border' : 'badge-tax-free' }} px-3 py-1">
                                {{ $b->is_taxable ? 'Sujeito a INSS' : 'Isento' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 fw-extrabold text-dark">
                            {{ number_format($b->amount, 2, ',', '.') }} {{ $b->is_percentage ? '%' : 'Kz' }}
                        </td>
                        <td class="py-3 px-4 text-end">
                            <form action="{{ route('rh.beneficios.destroy', $b->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Eliminar este benefício/dedução?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger border-0"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($benefits->hasPages())
        <div class="p-3 border-top">
            {{ $benefits->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Adicionar Benefício/Dedução -->
<div class="modal fade" id="createBenefitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-gift text-primary me-2"></i>Adicionar Benefício ou Dedução</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('rh.beneficios.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Colaborador <span class="text-danger">*</span></label>
                        <select name="employee_id" class="form-select" required style="border-radius: 10px;">
                            <option value="">Selecione o Colaborador...</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->nif ?? 'Sem NIF' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Designação / Nome <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Ex: Subsídio de Residência, Abono de Família" required style="border-radius: 10px;">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Tipo <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required style="border-radius: 10px;">
                                <option value="benefit" selected>Provento / Subsídio</option>
                                <option value="deduction">Desconto / Retenção</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Valor / Montante <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount" class="form-control" value="15000" required min="0" style="border-radius: 10px;">
                        </div>
                    </div>

                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_taxable" id="is_taxable" value="1" checked>
                        <label class="form-check-label small fw-bold text-dark" for="is_taxable">Sujeito a Incidência Fiscal (IRT e INSS)</label>
                    </div>

                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="is_percentage" id="is_percentage" value="1">
                        <label class="form-check-label small fw-bold text-dark" for="is_percentage">Valor em Percentagem (%) sobre o Salário Base</label>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal" style="border-radius: 10px;">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4" style="border-radius: 10px;">Salvar Benefício</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

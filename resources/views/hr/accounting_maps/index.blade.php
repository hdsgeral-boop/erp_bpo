@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-project-diagram text-primary me-2"></i>Mapeamento Contabilístico (Salarial)</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">Novo Mapeamento</button>
    </div>

    <div class="card card-premium">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Tipo Lançamento</th>
                        <th>Rubrica Associada</th>
                        <th>Conta a Debitar</th>
                        <th>Conta a Creditar</th>
                        <th>Descrição do Lançamento</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($maps as $map)
                    <tr>
                        <td class="fw-bold">{{ $map->type }}</td>
                        <td>{{ $map->payrollItem ? $map->payrollItem->name : 'N/A (Líquido/Global)' }}</td>
                        <td class="text-danger fw-bold">{{ $map->debit_account ?? '-' }}</td>
                        <td class="text-success fw-bold">{{ $map->credit_account ?? '-' }}</td>
                        <td><small class="text-muted">{{ $map->description }}</small></td>
                        <td><span class="badge bg-{{ $map->is_active ? 'primary' : 'secondary' }}">{{ $map->is_active ? 'Ativo' : 'Inativo' }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Create -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('rh.accounting-maps.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Novo Mapeamento Contabilístico</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tipo de Lançamento</label>
                            <select name="type" class="form-select" required>
                                <option value="ITEM">Específico de Rubrica</option>
                                <option value="NET_PAY">Total Líquido a Pagar</option>
                                <option value="INSS_EMP">INSS Trabalhador (Total)</option>
                                <option value="INSS_COMP">INSS Empresa (Total)</option>
                                <option value="IRT">IRT (Total)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Rubrica (Se for tipo ITEM)</label>
                            <select name="payroll_item_id" class="form-select">
                                <option value="">-- Selecione (Opcional) --</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->code }} - {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Conta a Debitar (ex: 621...)</label>
                            <input type="text" name="debit_account" class="form-control" placeholder="Deixe em branco se não debita">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Conta a Creditar (ex: 244...)</label>
                            <input type="text" name="credit_account" class="form-control" placeholder="Deixe em branco se não credita">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Descrição base para Diário</label>
                            <input type="text" name="description" class="form-control" placeholder="Ex: Processamento de Vencimentos - Rubrica X">
                        </div>
                        <input type="hidden" name="is_active" value="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Gravar Mapeamento</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

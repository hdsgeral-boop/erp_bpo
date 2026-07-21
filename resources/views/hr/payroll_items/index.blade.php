@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-list-ul text-primary me-2"></i>Gestão de Rubricas Salariais</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createItemModal">Nova Rubrica</button>
    </div>

    <div class="card card-premium">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Cód.</th>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Natureza</th>
                        <th>Ordem</th>
                        <th>IRT</th>
                        <th>INSS</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td class="fw-bold">{{ $item->code }}</td>
                        <td>{{ $item->name }}</td>
                        <td><span class="badge bg-{{ $item->type == 'PROVENTO' ? 'success' : 'danger' }}">{{ $item->type }}</span></td>
                        <td>{{ $item->nature }}</td>
                        <td>{{ $item->calculation_order }}</td>
                        <td>{{ $item->is_subject_to_irt ? 'Sim' : 'Não' }}</td>
                        <td>{{ $item->is_subject_to_inss ? 'Sim' : 'Não' }}</td>
                        <td><span class="badge bg-{{ $item->is_active ? 'primary' : 'secondary' }}">{{ $item->is_active ? 'Ativo' : 'Inativo' }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Create -->
<div class="modal fade" id="createItemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('rh.payroll-items.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nova Rubrica Salarial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Código</label>
                            <input type="text" name="code" class="form-control" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Nome da Rubrica</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tipo</label>
                            <select name="type" class="form-select">
                                <option value="PROVENTO">Provento (+)</option>
                                <option value="DESCONTO">Desconto (-)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Natureza</label>
                            <select name="nature" class="form-select">
                                <option value="FIXED">Valor Fixo</option>
                                <option value="PERCENTAGE">Percentagem</option>
                                <option value="FORMULA">Fórmula</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ordem de Cálculo</label>
                            <input type="number" name="calculation_order" class="form-control" value="100" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Fórmula Matemática (ex: BASE * 0.10)</label>
                            <input type="text" name="formula" class="form-control">
                        </div>
                        <div class="col-md-6 mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_subject_to_irt" checked>
                                <label class="form-check-label">Sujeito a IRT?</label>
                            </div>
                        </div>
                        <div class="col-md-6 mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_subject_to_inss" checked>
                                <label class="form-check-label">Sujeito a INSS?</label>
                            </div>
                        </div>
                        <input type="hidden" name="is_active" value="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Gravar Rubrica</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

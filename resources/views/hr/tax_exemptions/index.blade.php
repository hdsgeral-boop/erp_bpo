@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-shield-alt text-primary me-2"></i>Códigos de Isenção Fiscal</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">Novo Código</button>
    </div>

    <div class="card card-premium">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Cód. Isenção</th>
                        <th>Descrição</th>
                        <th>Base Legal</th>
                        <th>Tipo Imposto</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($exemptions as $ex)
                    <tr>
                        <td class="fw-bold">{{ $ex->code }}</td>
                        <td>{{ $ex->description }}</td>
                        <td><small class="text-muted">{{ $ex->legal_basis }}</small></td>
                        <td><span class="badge bg-secondary">{{ $ex->tax_type ?? 'Geral' }}</span></td>
                        <td><span class="badge bg-{{ $ex->is_active ? 'primary' : 'secondary' }}">{{ $ex->is_active ? 'Ativo' : 'Inativo' }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Create -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('rh.tax-exemptions.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Novo Código de Isenção</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Código</label>
                            <input type="text" name="code" class="form-control" required placeholder="Ex: M10">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Tipo de Imposto</label>
                            <input type="text" name="tax_type" class="form-control" placeholder="IRT, INSS, IVA, ou deixar em branco">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Descrição da Isenção</label>
                            <input type="text" name="description" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Base Legal (Lei/Artigo)</label>
                            <textarea name="legal_basis" class="form-control" rows="2"></textarea>
                        </div>
                        <input type="hidden" name="is_active" value="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Gravar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

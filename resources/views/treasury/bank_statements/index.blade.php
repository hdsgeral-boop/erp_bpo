@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-auto">
            <div class="bg-primary text-white p-3 rounded-3 shadow-sm">
                <i class="fas fa-file-invoice-dollar fa-2x"></i>
            </div>
        </div>
        <div class="col">
            <h2 class="mb-0 fw-bold">Extratos Bancários</h2>
            <p class="text-muted mb-0">Gestão de movimentos importados ou inseridos manualmente</p>
        </div>
        <div class="col-auto d-flex gap-2">
            <a href="{{ route('tesouraria.bank_statements.create') }}" class="btn btn-primary px-4 py-2 fw-bold shadow-sm">
                <i class="fas fa-plus me-2"></i>Nova Linha
            </a>
            <button class="btn btn-outline-success px-4 py-2 fw-bold shadow-sm" onclick="alert('Funcionalidade de importação de CSV/MT940 a desenvolver.')">
                <i class="fas fa-file-import me-2"></i>Importar Extrato
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 text-muted text-uppercase py-3">Data</th>
                            <th class="text-muted text-uppercase py-3">Conta Bancária</th>
                            <th class="text-muted text-uppercase py-3">Descrição / Movimento</th>
                            <th class="text-muted text-uppercase py-3">Referência</th>
                            <th class="text-end text-muted text-uppercase py-3">Valor</th>
                            <th class="text-end pe-4 text-muted text-uppercase py-3">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($statements as $line)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">
                                {{ \Carbon\Carbon::parse($line->date)->format('d/m/Y') }}
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary">
                                    <i class="fas fa-university me-1"></i> {{ $line->account_code }}
                                </span>
                            </td>
                            <td>{{ $line->description }}</td>
                            <td><span class="text-muted">{{ $line->reference ?? '-' }}</span></td>
                            <td class="text-end fw-bold {{ $line->type_dc == 'C' ? 'text-success' : 'text-danger' }}">
                                {{ $line->type_dc == 'C' ? '+' : '-' }}{{ number_format($line->value, 2) }}
                            </td>
                            <td class="text-end pe-4">
                                @if($line->status == 'PENDING')
                                    <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pendente</span>
                                @else
                                    <span class="badge bg-success"><i class="fas fa-check-double me-1"></i>Reconciliado</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-file-invoice-dollar fa-3x mb-3 opacity-25"></i>
                                <h5>Nenhuma linha de extrato encontrada</h5>
                                <p>Importe um ficheiro do banco ou adicione linhas manualmente.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

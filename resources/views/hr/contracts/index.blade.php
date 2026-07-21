@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h2 class="view-title mb-0"><i class="fas fa-file-contract text-primary"></i> Contratos e Vínculos</h2>
            <p class="text-muted mb-0">Gestão de remunerações base e outros vínculos contratuais.</p>
        </div>
        <div>
            <a href="{{ route('rh.contratos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Novo Contrato
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Colaborador</th>
                            <th>Tipo de Remuneração (Infotipo)</th>
                            <th class="text-end">Valor (AKZ)</th>
                            <th>Data Início</th>
                            <th>Data Fim</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contracts as $contract)
                        <tr>
                            <td class="text-muted">#{{ $contract->id }}</td>
                            <td class="fw-bold">{{ $contract->employee ? $contract->employee->name : 'N/A' }}</td>
                            <td>{{ $contract->infotype ? $contract->infotype->name : 'N/A' }}</td>
                            <td class="text-end fw-bold text-success">{{ number_format($contract->value, 2, ',', '.') }}</td>
                            <td>{{ \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') }}</td>
                            <td>{{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') : 'Sem Termo' }}</td>
                            <td class="text-end">
                                <a href="{{ route('rh.contratos.edit', $contract->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('rh.contratos.destroy', $contract->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Deseja eliminar este contrato?')"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Não existem contratos registados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

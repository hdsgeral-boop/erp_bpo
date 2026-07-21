@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-sitemap"></i> Plano de Contas SNC</h2>
        <a href="{{ route('config.accounts.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nova Conta</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Código</th>
                            <th>Descrição</th>
                            <th>Tipo</th>
                            <th>Dados Mestre</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accounts as $acc)
                        <tr>
                            <td><strong>{{ $acc->code }}</strong></td>
                            <td style="{{ strlen($acc->code) == 2 ? 'font-weight:bold; font-size:1.1em;' : '' }}">{{ $acc->description }}</td>
                            <td>
                                @if($acc->type == 'I')
                                    <span class="badge bg-secondary">Integrador</span>
                                @else
                                    <span class="badge bg-success">Movimento</span>
                                @endif
                            </td>
                            <td>
                                @if($acc->is_master_data)
                                    <i class="fas fa-check text-success"></i>
                                @else
                                    <i class="fas fa-times text-muted"></i>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('config.accounts.edit', $acc->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

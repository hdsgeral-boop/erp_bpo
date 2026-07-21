@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-truck-moving"></i> Guias de Saída</h2>
        <a href="{{ route('logistica.guias.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nova Guia</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nº Documento</th>
                            <th>Data</th>
                            <th>Cliente</th>
                            <th>Armazém Origem</th>
                            <th>Motorista/Viatura</th>
                            <th>Estado</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($waybills as $wb)
                        <tr>
                            <td><strong>{{ $wb->document_number }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($wb->date)->format('d/m/Y') }}</td>
                            <td>{{ $wb->customer->name ?? 'N/A' }}</td>
                            <td>{{ $wb->warehouse->name ?? 'N/A' }}</td>
                            <td>{{ $wb->driver_name }} ({{ $wb->vehicle_plate }})</td>
                            <td>
                                <span class="badge bg-success">{{ $wb->status }}</span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-print"></i></button>
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

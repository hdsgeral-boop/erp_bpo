@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-history text-primary me-2"></i> Histórico de Movimentos de Stock</h2>
            <p class="text-muted mb-0">Registo de entradas, saídas e transferências de inventário.</p>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 12px; padding: 2rem; background: #fff;">
        <div class="text-center py-5">
            <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
            <h4 class="fw-bold">Histórico de Movimentação</h4>
            <p class="text-muted">Consulte os níveis de stock e histórico de entradas por armazém.</p>
            <a href="{{ route('logistica.stock') }}" class="btn btn-primary mt-2">
                <i class="fas fa-boxes me-1"></i> Ver Níveis de Stock
            </a>
        </div>
    </div>
</div>
@endsection

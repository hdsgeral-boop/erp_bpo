@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="margin: 0; font-weight: 800; color: #0f172a; font-size: 1.6rem; letter-spacing: -0.5px;">
                <i class="fas fa-percent text-primary me-2"></i> Nova Taxa Salarial / INSS
            </h2>
        </div>
        <a href="{{ route('rh.taxas-salariais.index') }}" class="btn btn-outline-secondary fw-bold" style="border-radius: 10px;">Voltar</a>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
        <div class="card-body p-4">
            <form action="{{ route('rh.taxas-salariais.index') }}" method="GET">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nome da Taxa</label>
                    <input type="text" class="form-control" placeholder="ex: INSS Trabalhador (3%)" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Percentagem (%)</label>
                    <input type="number" step="0.01" class="form-control" placeholder="3.00" required>
                </div>
                <button type="submit" class="btn btn-primary fw-bold" style="border-radius: 10px;">Guardar Taxa</button>
            </form>
        </div>
    </div>
</div>
@endsection

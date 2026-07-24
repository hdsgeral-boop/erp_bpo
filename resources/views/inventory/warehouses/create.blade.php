@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('inventario.armazens.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="fas fa-arrow-left me-1"></i> Voltar à Listagem
        </a>
        <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-warehouse text-primary me-2"></i>Novo Armazém</h2>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm" style="border-radius: 10px;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0" style="border-radius: 16px; background: #fff; padding: 2rem;">
        <form action="{{ route('inventario.armazens.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Nome do Armazém <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Ex: Armazém Central Luanda" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Localização / Cidade <span class="text-danger">*</span></label>
                    <input type="text" name="location" class="form-control" placeholder="Ex: Viana / Luanda" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Capacidade Máxima (M3)</label>
                    <input type="number" name="capacity" class="form-control" placeholder="Ex: 5000">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Nome do Responsável</label>
                    <input type="text" name="manager_name" class="form-control" placeholder="Ex: Manuel Silva">
                </div>
                <div class="col-12 mt-4 text-end">
                    <a href="{{ route('inventario.armazens.index') }}" class="btn btn-light border me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary fw-bold"><i class="fas fa-save me-1"></i> Criar Armazém</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

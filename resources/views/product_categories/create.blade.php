@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    .form-control {
        border-radius: 10px;
        padding: 0.75rem 1rem;
        border: 1px solid #cbd5e1;
        font-size: 0.95rem;
    }
    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }
    .form-label {
        font-weight: 700;
        color: #334155;
        font-size: 0.9rem;
    }
    .btn-save {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white;
        border-radius: 10px;
        padding: 0.7rem 2rem;
        font-weight: 700;
        border: none;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
        transition: all 0.2s;
    }
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(37, 99, 235, 0.35);
        color: white;
    }
    .btn-cancel {
        background: #f1f5f9;
        color: #475569;
        border-radius: 10px;
        padding: 0.7rem 1.8rem;
        font-weight: 700;
        border: none;
        text-decoration: none;
    }
    .btn-cancel:hover {
        background: #e2e8f0;
        color: #1e293b;
    }
    .section-header {
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 1rem;
        margin-bottom: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('logistica.categories.index') }}" class="btn btn-cancel btn-sm mb-3 d-inline-flex align-items-center gap-2">
            <i class="fas fa-arrow-left"></i> Voltar à Listagem
        </a>
        <h2 class="fw-extrabold mb-1 text-dark">
            <i class="fas fa-folder-plus text-primary me-2"></i> Nova Categoria de Artigos
        </h2>
        <p class="text-muted small mb-0">Crie uma nova classificação para estruturar e organizar o catálogo de produtos e serviços.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm rounded-4 mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-premium p-4 p-md-5">
                <div class="section-header">
                    <h5 class="fw-bold text-dark mb-1">Informação da Categoria</h5>
                    <small class="text-muted">Preencha o código único e a denominação da categoria.</small>
                </div>

                <form action="{{ route('logistica.categories.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-5">
                            <label class="form-label">Código da Categoria <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0" style="border-radius: 10px 0 0 10px;">
                                    <i class="fas fa-barcode"></i>
                                </span>
                                <input type="text" name="code" class="form-control border-start-0 font-monospace text-uppercase fw-bold" placeholder="Ex: CAT-CABOS" value="{{ old('code') }}" required autofocus>
                            </div>
                            <div class="form-text small">Identificador único (ex: CAT01, ELEC, SERV).</div>
                        </div>

                        <div class="col-md-7">
                            <label class="form-label">Nome da Categoria <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0" style="border-radius: 10px 0 0 10px;">
                                    <i class="fas fa-tag"></i>
                                </span>
                                <input type="text" name="name" class="form-control border-start-0 fw-semibold" placeholder="Ex: Cabos Elétricos e Iluminação" value="{{ old('name') }}" required>
                            </div>
                            <div class="form-text small">Nome descritivo exibido nas listagens e no POS.</div>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 border mb-4 d-flex align-items-center gap-3">
                        <div class="bg-primary-subtle text-primary p-2 rounded-circle">
                            <i class="fas fa-lightbulb fs-5"></i>
                        </div>
                        <div class="small text-secondary">
                            <strong>Dica Útil:</strong> As categorias criadas aparecem automaticamente como abas de filtro rápido no ecrã do <strong>POS (Frente de Caixa)</strong> para acelerar o processo de venda.
                        </div>
                    </div>

                    <div class="d-flex justify-content-end align-items-center gap-3 pt-3 border-top">
                        <a href="{{ route('logistica.categories.index') }}" class="btn btn-cancel">Cancelar</a>
                        <button type="submit" class="btn btn-save">
                            <i class="fas fa-save me-2"></i> Guardar Categoria
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

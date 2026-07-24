@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">
                <i class="fas fa-tags text-primary me-2"></i> Categorias de Artigos
            </h2>
            <p class="text-muted small mb-0">
                Gestão e classificação de artigos, matérias-primas e serviços da empresa.
            </p>
        </div>
        <div>
            <a href="{{ route('logistica.categories.create') }}" class="btn btn-primary px-3 py-2 fw-semibold rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                <i class="fas fa-plus"></i> Nova Categoria
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning border-0 shadow-sm rounded-3 mb-4">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('warning') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Stats Grid -->
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <span class="text-muted small fw-bold text-uppercase">Total Categorias</span>
                <h3 class="fw-bold text-dark mb-0 mt-1">{{ $categories->count() }}</h3>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <span class="text-muted small fw-bold text-uppercase">Artigos Registados</span>
                <h3 class="fw-bold text-success mb-0 mt-1">
                    {{ \App\Models\Product::where('company_id', session('company_id') ?? auth()->user()?->company_id ?? 1)->count() }}
                </h3>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">Código</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">Nome da Categoria</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">Artigos Associados</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold">Estado</th>
                        <th class="py-3 px-4 text-muted small text-uppercase fw-bold text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                    <tr>
                        <td class="py-3 px-4 fw-bold text-secondary">
                            <code>{{ $cat->code ?? '#'.str_pad($cat->id, 3, '0', STR_PAD_LEFT) }}</code>
                        </td>
                        <td class="py-3 px-4 font-weight-bold text-dark">
                            <i class="fas fa-folder text-primary me-2"></i> {{ $cat->name }}
                        </td>
                        <td class="py-3 px-4 text-muted">
                            <span class="badge bg-light text-dark border fw-normal px-2 py-1">
                                {{ $cat->products()->count() }} artigos
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Ativo</span>
                        </td>
                        <td class="py-3 px-4 text-end">
                            <a href="{{ route('logistica.categories.edit', $cat->id) }}" class="btn btn-sm btn-outline-primary rounded-2 px-3">
                                <i class="fas fa-edit me-1"></i> Editar
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="py-4">
                                <i class="fas fa-folder-open text-muted fa-3x mb-3"></i>
                                <h5 class="fw-bold text-dark">Nenhuma categoria registada</h5>
                                <p class="text-muted small mb-3">Primeiro deve criar uma Categoria antes de associar artigos.</p>
                                <a href="{{ route('logistica.categories.create') }}" class="btn btn-primary btn-sm px-3 py-2 fw-semibold">
                                    <i class="fas fa-plus me-1"></i> Criar Primeira Categoria
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

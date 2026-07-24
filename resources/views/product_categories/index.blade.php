@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="margin: 0; font-weight: 700; color: #0f172a; font-size: 1.5rem;">
                <i class="fas fa-tags text-primary me-2"></i> Categorias de Produtos
            </h2>
            <p style="margin: 0; color: #64748b; font-size: 0.9rem;">
                Organização e classificação de artigos, matérias-primas e serviços.
            </p>
        </div>
        <div>
            <button class="btn btn-primary" style="padding: 0.6rem 1.2rem; background: #2563eb; color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-plus"></i> Nova Categoria
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem;">
            <span style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Total Categorias</span>
            <h3 style="margin: 0.25rem 0 0; font-weight: 700; color: #0f172a;">{{ $categories->count() ?? 4 }}</h3>
        </div>
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem;">
            <span style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Artigos Associados</span>
            <h3 style="margin: 0.25rem 0 0; font-weight: 700; color: #16a34a;">{{ \App\Models\Product::count() }} Produtos</h3>
        </div>
    </div>

    <!-- Table Card -->
    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #e2e8f0; background: #f8fafc;">
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Código / ID</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Nome da Categoria</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Descrição</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Estado</th>
                    <th style="padding: 0.75rem 1rem; font-size: 0.85rem; color: #475569; text-transform: uppercase; text-align: right;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 1rem; font-weight: 600; color: #475569;">#{{ str_pad($cat->id, 3, '0', STR_PAD_LEFT) }}</td>
                    <td style="padding: 1rem; font-weight: 700; color: #0f172a;">
                        <i class="fas fa-folder text-primary me-2"></i> {{ $cat->name }}
                    </td>
                    <td style="padding: 1rem; color: #64748b;">{{ $cat->description ?? 'Geral' }}</td>
                    <td style="padding: 1rem;">
                        <span style="background: #dcfce7; color: #166534; padding: 0.25rem 0.6rem; border-radius: 4px; font-weight: 600; font-size: 0.75rem;">Ativo</span>
                    </td>
                    <td style="padding: 1rem; text-align: right;">
                        <button class="btn btn-sm btn-outline-primary" style="padding: 0.4rem 0.8rem; border: 1px solid #2563eb; color: #2563eb; background: none; border-radius: 6px; cursor: pointer;">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 1rem; font-weight: 600; color: #475569;">#001</td>
                    <td style="padding: 1rem; font-weight: 700; color: #0f172a;"><i class="fas fa-folder text-primary me-2"></i> Serviços Principais SPA</td>
                    <td style="padding: 1rem; color: #64748b;">Serviços técnicos e tratamentos de estética e bem-estar</td>
                    <td style="padding: 1rem;"><span style="background: #dcfce7; color: #166534; padding: 0.25rem 0.6rem; border-radius: 4px; font-weight: 600; font-size: 0.75rem;">Ativo</span></td>
                    <td style="padding: 1rem; text-align: right;"><button class="btn btn-sm btn-outline-primary" style="padding: 0.4rem 0.8rem; border: 1px solid #2563eb; color: #2563eb; background: none; border-radius: 6px;"><i class="fas fa-edit"></i></button></td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 1rem; font-weight: 600; color: #475569;">#002</td>
                    <td style="padding: 1rem; font-weight: 700; color: #0f172a;"><i class="fas fa-folder text-primary me-2"></i> Artigos de Consumo & Cosmética</td>
                    <td style="padding: 1rem; color: #64748b;">Produtos cosméticos e consumíveis de balcão</td>
                    <td style="padding: 1rem;"><span style="background: #dcfce7; color: #166534; padding: 0.25rem 0.6rem; border-radius: 4px; font-weight: 600; font-size: 0.75rem;">Ativo</span></td>
                    <td style="padding: 1rem; text-align: right;"><button class="btn btn-sm btn-outline-primary" style="padding: 0.4rem 0.8rem; border: 1px solid #2563eb; color: #2563eb; background: none; border-radius: 6px;"><i class="fas fa-edit"></i></button></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
@php
    $title = match($category) {
        'faturas' => 'Faturas e Faturas-Recibo',
        'orcamentos' => 'Orçamentos e Pró-formas',
        'guias' => 'Guias de Remessa e Transporte',
        'notas' => 'Notas de Crédito e Débito',
        default => 'Documentos Comerciais'
    };
    $icon = match($category) {
        'faturas' => 'fa-file-invoice-dollar',
        'orcamentos' => 'fa-file-signature',
        'guias' => 'fa-truck-moving',
        'notas' => 'fa-file-invoice',
        default => 'fa-file-alt'
    };
    $desc = match($category) {
        'faturas' => 'Gestão e emissão de faturas (FT) e faturas-recibo (FR) com assinatura AGT.',
        'orcamentos' => 'Emissão e conversão de propostas comerciais e orçamentos.',
        'guias' => 'Gestão e emissão de guias de transporte (GT) e remessa (GR).',
        'notas' => 'Emissão de notas de crédito (NC) e débito (ND) de retificação.',
        default => 'Gestão centralizada de documentos de vendas.'
    };
@endphp

<div class="container-fluid" style="padding: 1.5rem;">
    <!-- Header Page Banner -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #eff6ff, #dbeafe); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas {{ $icon }}" style="font-size: 1.5rem; color: #2563eb;"></i>
            </div>
            <div>
                <h2 style="margin: 0; font-weight: 700; color: #0f172a; font-size: 1.5rem; letter-spacing: -0.5px;">{{ $title }}</h2>
                <p style="margin: 0; color: #64748b; font-size: 0.9rem;">{{ $desc }}</p>
            </div>
        </div>
        <div>
            <a href="{{ route('vendas.documentos.create', $category) }}" class="btn btn-primary" style="padding: 0.6rem 1.25rem; background: #2563eb; color: #fff; border: none; border-radius: 10px; font-weight: 600; font-size: 0.9rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25); text-decoration: none;">
                <i class="fas fa-plus-circle"></i> Emitir Novo Documento
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center mb-4" role="alert" style="border-radius: 12px; background: #dcfce7; border: 1px solid #bbf7d0; color: #166534; padding: 1rem;">
            <i class="fas fa-check-circle me-2" style="font-size: 1.2rem;"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center mb-4" role="alert" style="border-radius: 12px; background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 1rem;">
            <i class="fas fa-exclamation-triangle me-2" style="font-size: 1.2rem;"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <!-- Statistics Quick Summary -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #64748b; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Total Emitidos</span>
                <span style="background: #eff6ff; color: #2563eb; border-radius: 6px; padding: 4px 8px; font-size: 0.75rem; font-weight: 700;">{{ $invoices->total() ?? count($invoices) }}</span>
            </div>
            <h3 style="margin: 0.5rem 0 0; font-weight: 700; color: #0f172a; font-size: 1.5rem;">{{ $invoices->total() ?? count($invoices) }} <span style="font-size: 0.85rem; color: #94a3b8; font-weight: 500;">Docs</span></h3>
        </div>
        
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #64748b; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Volume Total S/ IVA</span>
                <i class="fas fa-coins text-warning" style="font-size: 1rem;"></i>
            </div>
            <h3 style="margin: 0.5rem 0 0; font-weight: 700; color: #16a34a; font-size: 1.5rem;">
                {{ number_format($invoices->sum('total_amount') ?? 0, 2, ',', '.') }} <span style="font-size: 0.85rem; color: #94a3b8; font-weight: 500;">Kz</span>
            </h3>
        </div>

        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #64748b; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Total IVA Líquido</span>
                <i class="fas fa-percentage text-info" style="font-size: 1rem;"></i>
            </div>
            <h3 style="margin: 0.5rem 0 0; font-weight: 700; color: #2563eb; font-size: 1.5rem;">
                {{ number_format($invoices->sum('total_tax') ?? 0, 2, ',', '.') }} <span style="font-size: 0.85rem; color: #94a3b8; font-weight: 500;">Kz</span>
            </h3>
        </div>

        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #64748b; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Estado Assinatura AGT</span>
                <span style="background: #dcfce7; color: #166534; border-radius: 6px; padding: 4px 8px; font-size: 0.75rem; font-weight: 700;">RSA 1024-bit</span>
            </div>
            <h3 style="margin: 0.5rem 0 0; font-weight: 700; color: #0f172a; font-size: 1.25rem; display: flex; align-items: center; gap: 6px;">
                <i class="fas fa-shield-alt text-success" style="font-size: 1.1rem;"></i> 100% Conforme AGT
            </h3>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); overflow: hidden;">
        <!-- Filters Toolbar -->
        <div style="padding: 1.25rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
            <form action="{{ route('vendas.documentos.index', $category) }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-5 col-sm-12">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #475569; margin-bottom: 0.35rem; text-transform: uppercase; letter-spacing: 0.5px;">Pesquisar N.º Documento / Cliente</label>
                    <div style="position: relative;">
                        <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                        <input type="text" name="search" class="form-control" placeholder="Ex: FT 2026/001 ou Nome do Cliente..." value="{{ request('search') }}" style="padding-left: 36px; height: 42px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.9rem;">
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #475569; margin-bottom: 0.35rem; text-transform: uppercase; letter-spacing: 0.5px;">Estado do Documento</label>
                    <select name="status" class="form-select" style="height: 42px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.9rem;">
                        <option value="">Todos os Estados</option>
                        <option value="ISSUED" {{ request('status') == 'ISSUED' ? 'selected' : '' }}>Emitido / Válido</option>
                        <option value="CANCELLED" {{ request('status') == 'CANCELLED' ? 'selected' : '' }}>Anulado</option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100" style="height: 42px; border-radius: 8px; background: #2563eb; font-weight: 600; border: none;">
                        <i class="fas fa-filter me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('vendas.documentos.index', $category) }}" class="btn btn-outline-secondary" style="height: 42px; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; width: 80px;">
                        Limpar
                    </a>
                </div>
            </form>
        </div>

        <!-- Table Content -->
        <div class="table-responsive">
            <table class="table align-middle mb-0" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <th style="padding: 1rem 1.25rem; font-size: 0.8rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">N.º Fatura</th>
                        <th style="padding: 1rem 1.25rem; font-size: 0.8rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Cliente</th>
                        <th style="padding: 1rem 1.25rem; font-size: 0.8rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Data Emissão</th>
                        <th style="padding: 1rem 1.25rem; font-size: 0.8rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; text-align: right;">Valor S/ IVA</th>
                        <th style="padding: 1rem 1.25rem; font-size: 0.8rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; text-align: right;">Imposto (IVA)</th>
                        <th style="padding: 1rem 1.25rem; font-size: 0.8rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Estado / AGT</th>
                        <th style="padding: 1rem 1.25rem; font-size: 0.8rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; text-align: center;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s ease;">
                        <td style="padding: 1rem 1.25rem; font-weight: 700; color: #2563eb; font-size: 0.95rem;">
                            <i class="fas fa-file-invoice me-1 text-primary" style="opacity: 0.7;"></i> {{ $inv->doc_number }}
                        </td>
                        <td style="padding: 1rem 1.25rem;">
                            <div style="font-weight: 700; color: #0f172a;">{{ $inv->customer ? $inv->customer->name : 'Consumidor Final' }}</div>
                            <span style="font-size: 0.75rem; color: #64748b;">NIF: {{ $inv->customer->nif ?? '999999999' }}</span>
                        </td>
                        <td style="padding: 1rem 1.25rem; color: #475569; font-weight: 500;">
                            {{ $inv->date ? \Carbon\Carbon::parse($inv->date)->format('d/m/Y') : date('d/m/Y') }}
                        </td>
                        <td style="padding: 1rem 1.25rem; text-align: right; font-weight: 700; color: #0f172a;">
                            {{ number_format($inv->total_amount, 2, ',', '.') }} <span style="font-size: 0.75rem; color: #94a3b8;">Kz</span>
                        </td>
                        <td style="padding: 1rem 1.25rem; text-align: right; font-weight: 600; color: #64748b;">
                            {{ number_format($inv->total_tax, 2, ',', '.') }} <span style="font-size: 0.75rem; color: #94a3b8;">Kz</span>
                        </td>
                        <td style="padding: 1rem 1.25rem;">
                            @if($inv->status === 'ISSUED')
                                <span style="background: #dcfce7; color: #166534; padding: 0.25rem 0.65rem; border-radius: 6px; font-weight: 700; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-check"></i> EMITIDO
                                </span>
                            @elseif($inv->status === 'CANCELLED')
                                <span style="background: #fee2e2; color: #991b1b; padding: 0.25rem 0.65rem; border-radius: 6px; font-weight: 700; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-times"></i> ANULADO
                                </span>
                            @else
                                <span style="background: #f1f5f9; color: #475569; padding: 0.25rem 0.65rem; border-radius: 6px; font-weight: 700; font-size: 0.75rem;">
                                    {{ $inv->status }}
                                </span>
                            @endif
                        </td>
                        <td style="padding: 1rem 1.25rem; text-align: center;">
                            <a href="{{ route('vendas.documentos.show', ['category' => $category, 'id' => $inv->id]) }}" class="btn btn-sm btn-outline-primary" style="border-radius: 6px; padding: 0.35rem 0.75rem; font-weight: 600; text-decoration: none;">
                                <i class="fas fa-eye me-1"></i> Ver PDF
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="padding: 4rem 1.5rem; text-align: center;">
                            <div style="max-width: 320px; margin: 0 auto;">
                                <div style="width: 64px; height: 64px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                                    <i class="fas fa-folder-open" style="font-size: 1.75rem; color: #94a3b8;"></i>
                                </div>
                                <h5 style="margin: 0 0 0.5rem; font-weight: 700; color: #0f172a;">Nenhum documento encontrado</h5>
                                <p style="margin: 0 0 1.25rem; color: #64748b; font-size: 0.85rem;">Não existem registos de {{ strtolower($title) }} emitidos com os filtros selecionados.</p>
                                <a href="{{ route('vendas.documentos.create', $category) }}" class="btn btn-sm btn-primary" style="padding: 0.5rem 1rem; background: #2563eb; border-radius: 8px; font-weight: 600; text-decoration: none;">
                                    <i class="fas fa-plus me-1"></i> Emitir Primeiro Documento
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
        <div style="padding: 1rem 1.5rem; border-top: 1px solid #e2e8f0; background: #f8fafc; display: flex; justify-content: flex-end;">
            {{ $invoices->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection

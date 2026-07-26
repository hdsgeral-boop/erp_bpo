@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="h4 font-weight-bold text-gray-800 mb-1">
                <i class="fas fa-boxes text-primary me-2"></i> Inventário Anual Normativo (AGT)
            </h3>
            <p class="text-muted small mb-0">Ficheiro normativo de fecho de exercício do stock exigido pela Administração Geral Tributária (Angola)</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inventory.annual.export.xml', ['year' => $year, 'warehouse_id' => $warehouseId]) }}" class="btn btn-primary btn-sm px-3">
                <i class="fas fa-file-code me-1"></i> Exportar XML (AGT)
            </a>
            <a href="{{ route('inventory.annual.export.csv', ['year' => $year, 'warehouse_id' => $warehouseId]) }}" class="btn btn-success btn-sm px-3">
                <i class="fas fa-file-csv me-1"></i> Exportar CSV
            </a>
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm px-3">
                <i class="fas fa-print me-1"></i> Imprimir PDF
            </button>
        </div>
    </div>

    <!-- Filtros de Pesquisa -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('inventory.annual') }}" class="row g-3 align-items-center">
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold mb-1">Exercício Fiscal (Ano)</label>
                    <select name="year" class="form-select form-select-sm rounded-3">
                        @for($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold mb-1">Armazém</label>
                    <select name="warehouse_id" class="form-select form-select-sm rounded-3">
                        <option value="">Todos os Armazéns (Consolidado)</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ $warehouseId == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold mb-1">Categoria de Produtos</label>
                    <select name="category_id" class="form-select form-select-sm rounded-3">
                        <option value="">Todas as Categorias</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-dark btn-sm w-100 rounded-3">
                        <i class="fas fa-filter me-1"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Resumo dos Totais -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                <div class="text-muted small fw-bold text-uppercase">Total de Itens em Inventário</div>
                <div class="h3 font-weight-bold text-dark mt-1 mb-0">{{ number_format($totalItems) }} <span class="fs-6 text-muted font-normal">artigos</span></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                <div class="text-muted small fw-bold text-uppercase">Quantidade Total em Stock</div>
                <div class="h3 font-weight-bold text-primary mt-1 mb-0">{{ number_format($totalStockQty, 2, ',', '.') }} <span class="fs-6 text-muted font-normal">Uni</span></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                <div class="text-muted small fw-bold text-uppercase">Valor Total de Inventário (PMP)</div>
                <div class="h3 font-weight-bold text-success mt-1 mb-0">{{ number_format($totalInventoryValue, 2, ',', '.') }} <span class="fs-6 text-muted font-normal">Kz</span></div>
            </div>
        </div>
    </div>

    <!-- Tabela de Inventário -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase fw-bold">
                        <tr>
                            <th class="ps-4">Código</th>
                            <th>Descrição do Artigo</th>
                            <th>Categoria</th>
                            <th>Unidade</th>
                            <th class="text-end">Qtd Stock (31/Dez)</th>
                            <th class="text-end">Custo Médio Unit. (PMP)</th>
                            <th class="text-end pe-4">Valor Total (Kz)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventoryData as $row)
                            <tr>
                                <td class="ps-4 font-monospace text-primary fw-bold">{{ $row['code'] }}</td>
                                <td class="fw-bold text-dark">{{ $row['name'] }}</td>
                                /><td><span class="badge bg-light text-dark border">{{ $row['category'] }}</span></td>
                                <td>{{ $row['unit'] }}</td>
                                <td class="text-end fw-bold">{{ number_format($row['stock_qty'], 2, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($row['unit_cost'], 2, ',', '.') }} Kz</td>
                                <td class="text-end pe-4 font-monospace fw-bold text-success">{{ number_format($row['total_value'], 2, ',', '.') }} Kz</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-box-open fa-3x mb-3 text-muted opacity-50"></i>
                                    <div>Nenhum artigo encontrado para o filtro selecionado.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light fw-bold text-dark">
                        <tr>
                            <td colspan="4" class="ps-4 text-uppercase">Total Geral do Exercício {{ $year }}</td>
                            <td class="text-end">{{ number_format($totalStockQty, 2, ',', '.') }}</td>
                            <td class="text-end">-</td>
                            <td class="text-end pe-4 text-success font-monospace">{{ number_format($totalInventoryValue, 2, ',', '.') }} Kz</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

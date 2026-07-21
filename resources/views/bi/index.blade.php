@extends('layouts.app')

@push('styles')
<!-- PivotTable.js CSS via CDN -->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/pivottable/2.23.0/pivot.min.css">
<style>
    /* Adaptação aos estilos do ERP */
    .pvtUi { width: 100%; border: none !important; font-family: 'Inter', sans-serif !important; }
    .pvtAxisContainer, .pvtVals { background: #f8fafc !important; border-radius: 8px; border: 1px solid #e2e8f0; padding: 10px; }
    .pvtAxisContainer li { padding: 6px 12px; background: white; border: 1px solid #cbd5e1; border-radius: 6px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); margin-bottom: 5px; font-size: 0.85rem;}
    .pvtTable { border-collapse: collapse; width: 100%; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .pvtTable thead tr th, .pvtTable tbody tr th { background-color: #f1f5f9; color: #475569; font-weight: 600; padding: 10px; border: 1px solid #e2e8f0; }
    .pvtTable tbody tr td { padding: 10px; border: 1px solid #e2e8f0; color: #1e293b; text-align: right; }
    .pvtRenderer, .pvtAggregator { padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; background: white; margin-bottom: 10px; font-size: 0.85rem;}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h2 class="view-title mb-0"><i class="fas fa-chart-bar text-primary"></i> Business Intelligence</h2>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Analise dinamicamente todos os dados financeiros e operacionais do ERP.</p>
        </div>
        <div>
            <button class="btn btn-outline-secondary btn-sm" onclick="loadBiData()"><i class="fas fa-sync-alt"></i> Atualizar Dados</button>
        </div>
    </div>

    <!-- Alert / Loader -->
    <div id="bi-loader" class="alert alert-info" style="display: none;">
        <i class="fas fa-spinner fa-spin me-2"></i> A extrair dados do núcleo financeiro...
    </div>

    <div class="card shadow-sm border-0 bg-white">
        <div class="card-body p-4 overflow-auto">
            <!-- Contentor para a Pivot Table Dinâmica -->
            <div id="pivot-container">
                <div class="text-center text-muted py-5">
                    <i class="fas fa-magic fa-3x mb-3 text-primary opacity-50"></i>
                    <h5>Tabela Dinâmica Pronta</h5>
                    <p>Clique em "Atualizar Dados" para carregar as métricas mais recentes.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- jQuery UI is required for PivotTable drag and drop -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<!-- PivotTable.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pivottable/2.23.0/pivot.min.js"></script>

<script>
    // Inicializa o módulo BI (Híbrido - Extração JSON + Renderização JS Nativa)
    async function loadBiData() {
        const loader = document.getElementById('bi-loader');
        const container = $("#pivot-container");
        
        loader.style.display = 'block';
        container.css('opacity', '0.5');

        try {
            const response = await fetch('{{ route("bi.dataset") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (!response.ok) throw new Error('Falha na comunicação');
            const data = await response.json();

            // Renderiza a Pivot Table (Arrastar e Largar)
            container.empty().css('opacity', '1');
            container.pivotUI(data, {
                rows: ["Modulo", "Categoria"],
                cols: ["Estado"],
                aggregatorName: "Sum",
                vals: ["Valor"],
                rendererName: "Table",
                // Configurações extra podem ser aplicadas aqui (Heatmaps, Charts)
            });

        } catch (error) {
            alert('Erro ao carregar dados do BI: ' + error.message);
            container.css('opacity', '1');
        } finally {
            loader.style.display = 'none';
        }
    }
</script>
@endpush
@endsection

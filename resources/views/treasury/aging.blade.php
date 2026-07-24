@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h2 class="fw-bold text-dark mb-1"><i class="fas fa-clock text-warning me-2"></i>Idade dos Saldos (Aging Report)</h2>
            <p class="text-muted mb-0 small">Análise de maturidade e vencimento dos saldos de Clientes e Fornecedores.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm rounded-3" onclick="window.print()"><i class="fas fa-print me-1"></i> Imprimir</button>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-light">
        <div class="card-body p-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary btn-sm rounded-start-3 active" id="btn-type-customer" onclick="setAgingType('customer')"><i class="fas fa-user-tie me-1"></i> Aging Clientes</button>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-end-3" id="btn-type-supplier" onclick="setAgingType('supplier')"><i class="fas fa-truck me-1"></i> Aging Fornecedores</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4" id="aging-content-area">
            <div class="text-center py-5 text-muted">
                <div class="spinner-border text-primary me-2"></div> Carregando idade dos saldos...
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentType = 'customer';

    function setAgingType(type) {
        currentType = type;
        if (type === 'customer') {
            document.getElementById('btn-type-customer').className = 'btn btn-primary btn-sm rounded-start-3 active';
            document.getElementById('btn-type-supplier').className = 'btn btn-outline-primary btn-sm rounded-end-3';
        } else {
            document.getElementById('btn-type-customer').className = 'btn btn-outline-primary btn-sm rounded-start-3';
            document.getElementById('btn-type-supplier').className = 'btn btn-primary btn-sm rounded-end-3 active';
        }
        loadAgingData();
    }

    function formatKz(val) {
        return new Intl.NumberFormat('pt-AO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(val || 0) + ' Kz';
    }

    async function loadAgingData() {
        const area = document.getElementById('aging-content-area');
        area.innerHTML = '<div class="text-center py-5 text-muted"><div class="spinner-border text-primary me-2"></div> Processando dados...</div>';

        try {
            const res = await fetch(`/api/v1/tesouraria/aging?type=${currentType}`);
            const data = await res.json();
            renderAgingTable(data.report || []);
        } catch (err) {
            console.error(err);
            area.innerHTML = '<div class="alert alert-danger">Erro ao carregar o relatório de Idade dos Saldos.</div>';
        }
    }

    function renderAgingTable(rows) {
        let html = `
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Entidade / NIF</th>
                            <th class="text-end">Não Vencido</th>
                            <th class="text-end">1 - 30 Dias</th>
                            <th class="text-end">31 - 60 Dias</th>
                            <th class="text-end">61 - 90 Dias</th>
                            <th class="text-end text-danger">> 90 Dias</th>
                            <th class="text-end fw-bold">Saldo Total</th>
                        </tr>
                    </thead>
                    <tbody>`;

        if (rows.length === 0) {
            html += '<tr><td colspan="7" class="text-center text-muted py-4">Sem pendências registadas para esta categoria.</td></tr>';
        } else {
            let sumCurr = 0, sum30 = 0, sum60 = 0, sum90 = 0, sum91 = 0, sumTot = 0;

            rows.forEach(r => {
                sumCurr += r.current;
                sum30 += r.d1_30;
                sum60 += r.d31_60;
                sum90 += r.d61_90;
                sum91 += r.d91_plus;
                sumTot += r.total;

                html += `
                    <tr>
                        <td>
                            <div class="fw-bold">${r.name}</div>
                            <div class="small text-muted">NIF: ${r.tax_id || '-'}</div>
                        </td>
                        <td class="text-end text-success">${r.current ? formatKz(r.current) : '-'}</td>
                        <td class="text-end">${r.d1_30 ? formatKz(r.d1_30) : '-'}</td>
                        <td class="text-end text-warning">${r.d31_60 ? formatKz(r.d31_60) : '-'}</td>
                        <td class="text-end text-danger">${r.d61_90 ? formatKz(r.d61_90) : '-'}</td>
                        <td class="text-end fw-bold text-danger">${r.d91_plus ? formatKz(r.d91_plus) : '-'}</td>
                        <td class="text-end fw-bold text-primary">${formatKz(r.total)}</td>
                    </tr>`;
            });

            html += `
                <tfoot class="table-group-divider fw-bold bg-light">
                    <tr>
                        <td>TOTAL GERAL</td>
                        <td class="text-end text-success">${formatKz(sumCurr)}</td>
                        <td class="text-end">${formatKz(sum30)}</td>
                        <td class="text-end text-warning">${formatKz(sum60)}</td>
                        <td class="text-end text-danger">${formatKz(sum90)}</td>
                        <td class="text-end text-danger">${formatKz(sum91)}</td>
                        <td class="text-end text-primary fs-6">${formatKz(sumTot)}</td>
                    </tr>
                </tfoot>`;
        }

        html += '</tbody></table></div>';
        document.getElementById('aging-content-area').innerHTML = html;
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadAgingData();
    });
</script>
@endpush
@endsection

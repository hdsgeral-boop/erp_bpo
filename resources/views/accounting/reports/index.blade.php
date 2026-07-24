@extends('layouts.app')

@push('styles')
<style>
    .report-tab-btn {
        padding: 0.75rem 1.25rem;
        font-weight: 600;
        border-radius: 10px;
        color: #64748b;
        background: transparent;
        border: none;
        transition: all 0.2s ease;
    }
    .report-tab-btn.active {
        color: #0f172a;
        background: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    .report-tab-btn:hover:not(.active) {
        color: #334155;
        background: rgba(255, 255, 255, 0.5);
    }
    .table-report {
        font-size: 0.875rem;
    }
    .table-report th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
    }
    .table-report tr.is-header-acc {
        background-color: #f1f5f9;
        font-weight: 700;
    }
    .table-report tr.is-header-acc td {
        color: #0f172a;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h2 class="fw-bold text-dark mb-1"><i class="fas fa-chart-pie text-primary me-2"></i>Mapas e Relatórios Contábeis</h2>
            <p class="text-muted mb-0 small">Apuramento de Balancetes, Balanço Patrimonial PGC, DRE, DFC e Extrato de Contas em Uso.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm rounded-3" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Imprimir
            </button>
            <a id="btn-export-pdf" href="/contabilidade/balance-sheet/pdf" target="_blank" class="btn btn-primary btn-sm rounded-3">
                <i class="fas fa-file-pdf me-1"></i> Exportar PDF
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-light">
        <div class="card-body p-3">
            <form id="report-filter-form" class="row g-3 align-items-center">
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Exercício (Ano)</label>
                    <select id="filter-year" name="year" class="form-select form-select-sm rounded-3">
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Data Inicial</label>
                    <input type="date" id="filter-start-date" name="start_date" class="form-control form-control-sm rounded-3" value="{{ date('Y') }}-01-01">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Data Final</label>
                    <input type="date" id="filter-end-date" name="end_date" class="form-control form-control-sm rounded-3" value="{{ date('Y') }}-12-31">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Diário Contábil</label>
                    <select id="filter-journal" name="journal_id" class="form-select form-select-sm rounded-3">
                        <option value="">Todos os Diários</option>
                        @foreach($journals as $j)
                            <option value="{{ $j->id }}">{{ $j->code }} - {{ $j->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" id="filter-only-used" value="1" checked onchange="loadReportData()">
                        <label class="form-check-label small fw-bold text-dark" for="filter-only-used">Apenas Contas em Uso / Ativas</label>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Executive Summary Dashboard Cards & Charts -->
    <div class="row g-4 mb-4">
        <!-- Card 1: Rendimentos vs Gastos -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-chart-bar text-primary me-2"></i>Comparação: Rendimentos (Classe 7) vs Gastos (Classe 6)</h6>
                <div style="height: 220px; position: relative;">
                    <canvas id="chartIncomeVsExpenses"></canvas>
                </div>
            </div>
        </div>

        <!-- Card 2: Distribuição dos Saldos das Contas -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-chart-pie text-success me-2"></i>Distribuição das Contas Contábeis Ativas</h6>
                <div style="height: 220px; position: relative;">
                    <canvas id="chartAccountsDistribution"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="bg-secondary bg-opacity-10 p-1 rounded-4 d-inline-flex mb-4 gap-1">
        <button class="report-tab-btn active" onclick="switchReportTab('balancete', event)"><i class="fas fa-list-ol me-2"></i>Balancete de Verificação</button>
        <button class="report-tab-btn" onclick="switchReportTab('balanco', event)"><i class="fas fa-balance-scale me-2"></i>Balanço Patrimonial</button>
        <button class="report-tab-btn" onclick="switchReportTab('dre', event)"><i class="fas fa-file-invoice-dollar me-2"></i>DRE (Resultados)</button>
        <button class="report-tab-btn" onclick="switchReportTab('dfc', event)"><i class="fas fa-wave-square me-2"></i>DFC (Fluxo de Caixa)</button>
        <button class="report-tab-btn" onclick="switchReportTab('extrato', event)"><i class="fas fa-book-open me-2"></i>Razão / Extrato de Contas</button>
    </div>

    <!-- Tab Contents Container -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4" id="report-content-area">
            <div class="text-center py-5 text-muted">
                <div class="spinner-border text-primary me-2" role="status"></div> Carregando relatório contábil...
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let currentTab = 'balancete';
    let chart1 = null;
    let chart2 = null;

    function switchReportTab(tab, evt) {
        currentTab = tab;
        document.querySelectorAll('.report-tab-btn').forEach(btn => btn.classList.remove('active'));
        if (evt && evt.currentTarget) {
            evt.currentTarget.classList.add('active');
        }

        const pdfBtn = document.getElementById('btn-export-pdf');
        if (tab === 'balanco') {
            pdfBtn.href = `/contabilidade/balance-sheet/pdf?year=${document.getElementById('filter-year').value}`;
            pdfBtn.style.display = 'inline-block';
        } else if (tab === 'dre') {
            pdfBtn.href = `/contabilidade/income-statement/pdf?year=${document.getElementById('filter-year').value}`;
            pdfBtn.style.display = 'inline-block';
        } else {
            pdfBtn.style.display = 'none';
        }

        loadReportData();
    }

    document.getElementById('report-filter-form').addEventListener('submit', function(e) {
        e.preventDefault();
        loadReportData();
    });

    async function loadReportData() {
        const area = document.getElementById('report-content-area');
        area.innerHTML = '<div class="text-center py-5 text-muted"><div class="spinner-border text-primary me-2"></div> Processando dados contábeis...</div>';

        const year = document.getElementById('filter-year').value;
        const startDate = document.getElementById('filter-start-date').value;
        const endDate = document.getElementById('filter-end-date').value;
        const journalId = document.getElementById('filter-journal').value;
        const onlyUsed = document.getElementById('filter-only-used').checked ? '1' : '0';

        const queryParams = new URLSearchParams({
            year: year,
            start_date: startDate,
            end_date: endDate,
            journal_id: journalId,
            only_used: onlyUsed
        }).toString();

        try {
            if (currentTab === 'balancete') {
                const res = await fetch(`/contabilidade/trial-balance?${queryParams}`);
                const data = await res.json();
                renderBalancete(data);
                updateChartsFromTrial(data.accounts);
            } else if (currentTab === 'balanco') {
                const res = await fetch(`/contabilidade/balance-sheet?${queryParams}`);
                const data = await res.json();
                renderBalanco(data);
            } else if (currentTab === 'dre') {
                const res = await fetch(`/contabilidade/income-statement?${queryParams}`);
                const data = await res.json();
                renderDRE(data);
                updateChartsFromDRE(data);
            } else if (currentTab === 'dfc') {
                const res = await fetch(`/contabilidade/cash-flow?${queryParams}`);
                const data = await res.json();
                renderDFC(data);
            } else if (currentTab === 'extrato') {
                const accCode = document.getElementById('extrato-acc-code') ? document.getElementById('extrato-acc-code').value : '';
                const res = await fetch(`/contabilidade/ledger?${queryParams}&account_code=${accCode}`);
                const data = await res.json();
                renderExtrato(data);
            }
        } catch (err) {
            console.error(err);
            area.innerHTML = '<div class="alert alert-danger">Erro ao carregar os dados do relatório contábil. Por favor tente novamente.</div>';
        }
    }

    function formatKz(val) {
        return new Intl.NumberFormat('pt-AO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(val || 0) + ' Kz';
    }

    function renderBalancete(data) {
        let html = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Balancete de Verificação Geral (${data.year})</h5>
                <input type="text" id="search-acc" class="form-control form-control-sm" style="max-width: 280px;" placeholder="Filtrar por código ou designação..." onkeyup="filterBalanceteRows()">
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-report align-middle" id="table-balancete">
                    <thead>
                        <tr>
                            <th>Código PGC</th>
                            <th>Designação da Conta</th>
                            <th class="text-end">Débito Acumulado</th>
                            <th class="text-end">Crédito Acumulado</th>
                            <th class="text-end">Saldo Devedor</th>
                            <th class="text-end">Saldo Credor</th>
                        </tr>
                    </thead>
                    <tbody>`;

        if (!data.accounts || data.accounts.length === 0) {
            html += '<tr><td colspan="6" class="text-center text-muted py-4">Sem movimentos registados para o período selecionado.</td></tr>';
        } else {
            data.accounts.forEach(acc => {
                const isHeader = acc.type === 'I';
                const rowClass = isHeader ? 'is-header-acc' : '';
                const deb = acc.total_debit || 0;
                const cred = acc.total_credit || 0;
                const bal = acc.balance || 0;
                const sDevedor = bal > 0 ? bal : 0;
                const sCredor = bal < 0 ? Math.abs(bal) : 0;

                html += `
                    <tr class="${rowClass} acc-row" data-code="${acc.code}" data-name="${acc.name}">
                        <td class="fw-bold">${acc.code}</td>
                        <td>${acc.name}</td>
                        <td class="text-end text-info">${deb ? formatKz(deb) : '-'}</td>
                        <td class="text-end text-warning">${cred ? formatKz(cred) : '-'}</td>
                        <td class="text-end fw-bold text-success">${sDevedor ? formatKz(sDevedor) : '-'}</td>
                        <td class="text-end fw-bold text-danger">${sCredor ? formatKz(sCredor) : '-'}</td>
                    </tr>`;
            });
        }

        html += `</tbody></table></div>`;
        document.getElementById('report-content-area').innerHTML = html;
    }

    function filterBalanceteRows() {
        const query = document.getElementById('search-acc').value.toLowerCase();
        document.querySelectorAll('.acc-row').forEach(row => {
            const code = row.getAttribute('data-code').toLowerCase();
            const name = row.getAttribute('data-name').toLowerCase();
            if (code.includes(query) || name.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function renderBalanco(data) {
        let html = `
            <h5 class="fw-bold mb-3">Balanço Patrimonial (PGC-NIRF) — ${data.year}</h5>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-4 h-100">
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3"><i class="fas fa-arrow-circle-down me-2"></i>ATIVOS</h6>
                        <table class="table table-sm table-report">
                            <thead><tr><th>Conta</th><th>Descrição</th><th class="text-end">Valor (Kz)</th></tr></thead>
                            <tbody>`;
        if (data.ativos && data.ativos.length > 0) {
            data.ativos.forEach(a => {
                html += `<tr><td class="fw-bold">${a.code}</td><td>${a.name}</td><td class="text-end fw-bold text-primary">${formatKz(a.balance)}</td></tr>`;
            });
        } else {
            html += '<tr><td colspan="3" class="text-muted text-center py-3">Sem registo de ativos em uso.</td></tr>';
        }
        html += `</tbody><tfoot><tr class="table-primary fw-bold"><td colspan="2">TOTAL DOS ATIVOS</td><td class="text-end">${formatKz(data.total_ativos)}</td></tr></tfoot></table></div></div>`;

        html += `
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-4 h-100">
                        <h6 class="fw-bold text-danger border-bottom pb-2 mb-3"><i class="fas fa-arrow-circle-up me-2"></i>PASSIVOS E CAPITAIS PRÓPRIOS</h6>
                        <table class="table table-sm table-report">
                            <thead><tr><th>Conta</th><th>Descrição</th><th class="text-end">Valor (Kz)</th></tr></thead>
                            <tbody>`;
        if (data.passivos_capitais && data.passivos_capitais.length > 0) {
            data.passivos_capitais.forEach(p => {
                html += `<tr><td class="fw-bold">${p.code}</td><td>${p.name}</td><td class="text-end fw-bold text-danger">${formatKz(p.balance)}</td></tr>`;
            });
        } else {
            html += '<tr><td colspan="3" class="text-muted text-center py-3">Sem registo de passivos/capitais em uso.</td></tr>';
        }
        html += `</tbody><tfoot><tr class="table-danger fw-bold"><td colspan="2">TOTAL PASSIVOS & CAPITAIS</td><td class="text-end">${formatKz(data.total_passivos_capitais)}</td></tr></tfoot></table></div></div></div>`;

        document.getElementById('report-content-area').innerHTML = html;
    }

    function renderDRE(data) {
        let html = `
            <h5 class="fw-bold mb-3">Demonstração de Resultados por Natureza (DRE) — ${data.year}</h5>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="p-3 border rounded-4">
                        <h6 class="fw-bold text-success mb-3"><i class="fas fa-plus-circle me-2"></i>Rendimentos e Ganhos (Classe 7)</h6>
                        <table class="table table-sm table-report">
                            <tbody>`;
        if (data.rendimentos && data.rendimentos.length > 0) {
            data.rendimentos.forEach(r => {
                html += `<tr><td><strong>${r.code}</strong> - ${r.name}</td><td class="text-end text-success fw-bold">${formatKz(r.balance)}</td></tr>`;
            });
        } else {
            html += '<tr><td colspan="2" class="text-muted text-center py-2">Sem rendimentos no período.</td></tr>';
        }
        html += `<tr class="table-success fw-bold"><td>TOTAL RENDIMENTOS</td><td class="text-end">${formatKz(data.total_rendimentos)}</td></tr></tbody></table></div></div>`;

        html += `
                <div class="col-md-6">
                    <div class="p-3 border rounded-4">
                        <h6 class="fw-bold text-danger mb-3"><i class="fas fa-minus-circle me-2"></i>Gastos e Perdas (Classe 6)</h6>
                        <table class="table table-sm table-report">
                            <tbody>`;
        if (data.gastos && data.gastos.length > 0) {
            data.gastos.forEach(g => {
                html += `<tr><td><strong>${g.code}</strong> - ${g.name}</td><td class="text-end text-danger fw-bold">${formatKz(g.balance)}</td></tr>`;
            });
        } else {
            html += '<tr><td colspan="2" class="text-muted text-center py-2">Sem gastos no período.</td></tr>';
        }
        html += `<tr class="table-danger fw-bold"><td>TOTAL GASTOS</td><td class="text-end">${formatKz(data.total_gastos)}</td></tr></tbody></table></div></div></div>`;

        const resLiq = data.resultado_liquido || 0;
        const resClass = resLiq >= 0 ? 'bg-success text-white' : 'bg-danger text-white';
        html += `
            <div class="mt-4 p-4 rounded-4 ${resClass} d-flex justify-content-between align-items-center shadow-sm">
                <div>
                    <h5 class="fw-bold mb-1">RESULTADO LÍQUIDO DO EXERCÍCIO</h5>
                    <p class="mb-0 small opacity-75">Diferença apurada entre Rendimentos (Vendas) e Gastos (Compras & RH).</p>
                </div>
                <h2 class="fw-bold mb-0">${formatKz(resLiq)}</h2>
            </div>`;

        document.getElementById('report-content-area').innerHTML = html;
    }

    function renderDFC(data) {
        let html = `
            <h5 class="fw-bold mb-3">Demonstração dos Fluxos de Caixa (DFC) — ${data.year}</h5>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card bg-success bg-opacity-10 border-0 p-3 rounded-4">
                        <div class="text-muted small fw-semibold">ENTRADAS OPERACIONAIS</div>
                        <div class="fs-3 fw-bold text-success mt-1">${formatKz(data.entradas_operacionais)}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-danger bg-opacity-10 border-0 p-3 rounded-4">
                        <div class="text-muted small fw-semibold">SAÍDAS OPERACIONAIS</div>
                        <div class="fs-3 fw-bold text-danger mt-1">${formatKz(data.saidas_operacionais)}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-primary bg-opacity-10 border-0 p-3 rounded-4">
                        <div class="text-muted small fw-semibold">FLUXO LÍQUIDO DE CAIXA</div>
                        <div class="fs-3 fw-bold text-primary mt-1">${formatKz(data.fluxo_liquido)}</div>
                    </div>
                </div>
            </div>`;
        document.getElementById('report-content-area').innerHTML = html;
    }

    function renderExtrato(data) {
        let html = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Razão / Extrato de Movimentos em Uso</h5>
                <div class="d-flex gap-2">
                    <input type="text" id="extrato-acc-code" class="form-control form-control-sm" placeholder="Ex: 43.1 ou 61.1" style="max-width:200px;">
                    <button class="btn btn-sm btn-primary" onclick="loadReportData()"><i class="fas fa-search"></i> Buscar</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-report align-middle">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Conta</th>
                            <th>Descrição / Movimento</th>
                            <th class="text-end">Débito</th>
                            <th class="text-end">Crédito</th>
                            <th class="text-end">Saldo Acumulado</th>
                        </tr>
                    </thead>
                    <tbody>`;

        if (!data.lines || data.lines.length === 0) {
            html += '<tr><td colspan="6" class="text-center text-muted py-4">Nenhum movimento encontrado para os critérios.</td></tr>';
        } else {
            data.lines.forEach(l => {
                html += `
                    <tr>
                        <td>${l.entry_date}</td>
                        <td class="fw-bold">${l.account_code}</td>
                        <td>${l.description || '-'}</td>
                        <td class="text-end text-info">${l.type_dc === 'D' ? formatKz(l.value) : '-'}</td>
                        <td class="text-end text-warning">${l.type_dc === 'C' ? formatKz(l.value) : '-'}</td>
                        <td class="text-end fw-bold">${formatKz(l.running_balance)}</td>
                    </tr>`;
            });
        }
        html += '</tbody></table></div>';
        document.getElementById('report-content-area').innerHTML = html;
    }

    function updateChartsFromTrial(accounts) {
        if (!accounts) return;

        let totalRendimentos = 0;
        let totalGastos = 0;

        const labels = [];
        const values = [];

        accounts.forEach(acc => {
            const code = acc.code || '';
            const val = Math.abs(acc.balance || 0);

            if (code.startsWith('7')) totalRendimentos += val;
            if (code.startsWith('6')) totalGastos += val;

            if (val > 0 && acc.type === 'M' && labels.length < 6) {
                labels.push(`${acc.code} - ${acc.name}`);
                values.push(val);
            }
        });

        if (labels.length === 0) {
            labels.push('Vendas (Contas 71)', 'Compras (Contas 21)', 'Salários (Contas 63)');
            values.push(0, 0, 0);
        }

        // Gráfico 1: Bar Chart (Rendimentos vs Gastos)
        const ctx1 = document.getElementById('chartIncomeVsExpenses').getContext('2d');
        if (chart1) chart1.destroy();

        chart1 = new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: ['Rendimentos (Vendas)', 'Gastos (Operacionais)'],
                datasets: [{
                    label: 'Valor (Kz)',
                    data: [totalRendimentos, totalGastos],
                    backgroundColor: ['#16a34a', '#dc2626'],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });

        // Gráfico 2: Doughnut Chart (Distribuição)
        const ctx2 = document.getElementById('chartAccountsDistribution').getContext('2d');
        if (chart2) chart2.destroy();

        chart2 = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: ['#2563eb', '#16a34a', '#dc2626', '#d97706', '#9333ea', '#0891b2']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'right' } }
            }
        });
    }

    function updateChartsFromDRE(data) {
        const totalRendimentos = data.total_rendimentos || 0;
        const totalGastos = data.total_gastos || 0;

        const ctx1 = document.getElementById('chartIncomeVsExpenses').getContext('2d');
        if (chart1) chart1.destroy();

        chart1 = new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: ['Rendimentos (Vendas)', 'Gastos (Operacionais)'],
                datasets: [{
                    label: 'Valor (Kz)',
                    data: [totalRendimentos, totalGastos],
                    backgroundColor: ['#16a34a', '#dc2626'],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadReportData();
    });
</script>
@endpush
@endsection

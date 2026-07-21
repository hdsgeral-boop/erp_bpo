@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff; border: none; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); padding: 2rem; margin-bottom: 2rem;
    }
    .form-label-custom { font-weight: 600; color: #475569; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .form-control-custom { border-radius: 10px; border: 1px solid #cbd5e1; padding: 0.75rem 1rem; background-color: #f8fafc; }
    .form-control-custom:focus { background-color: #ffffff; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
    .btn-save { background: linear-gradient(135deg, #10b981, #059669); color: white; border-radius: 10px; padding: 0.75rem 2rem; font-weight: 600; border: none; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4" id="journalApp">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0 text-dark">Lançamento Contabilístico Manual</h2>
        <a href="{{ route('contabilidade.journals.index') }}" class="btn btn-light border">Voltar</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger shadow-sm">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('contabilidade.journals.store') }}" method="POST" id="journalForm">
        @csrf
        <div class="card-premium">
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <label class="form-label-custom">Data do Documento <span class="text-danger">*</span></label>
                    <input type="date" name="doc_date" class="form-control form-control-custom" required value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label-custom">Data do Lançamento <span class="text-danger">*</span></label>
                    <input type="date" name="entry_date" class="form-control form-control-custom" required value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Nº Documento / Referência <span class="text-danger">*</span></label>
                    <input type="text" name="doc_number" class="form-control form-control-custom" required placeholder="Ex: VEND-001, COMP-102">
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-12">
                    <label class="form-label-custom">Descrição do Lançamento <span class="text-danger">*</span></label>
                    <input type="text" name="description" class="form-control form-control-custom" required placeholder="Ex: Pagamento de faturas de luz, Venda de mercadoria, etc.">
                </div>
            </div>

            <div class="alert alert-secondary border bg-light mt-4 mb-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Linhas do Diário (Partidas Dobradas)</h5>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addLine()"><i class="fas fa-plus"></i> Adicionar Linha</button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th style="width: 40%">Conta Contabilística</th>
                                <th>Tipo (D/C)</th>
                                <th>Descrição da Linha</th>
                                <th style="width: 15%">Valor (Kz)</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="linesContainer">
                            <!-- Injected by JS -->
                        </tbody>
                        <tfoot>
                            <tr class="border-top">
                                <td colspan="3" class="text-end fw-bold pt-3">Totais:</td>
                                <td class="pt-3">
                                    <div class="d-flex justify-content-between fw-bold text-info">
                                        <span>Débito:</span> <span id="totalDDisplay">0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between fw-bold text-warning mt-1">
                                        <span>Crédito:</span> <span id="totalCDisplay">0.00</span>
                                    </div>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="5">
                                    <div id="balanceAlert" class="alert alert-danger py-2 mt-2 text-center" style="display: none;">
                                        <i class="fas fa-exclamation-triangle me-2"></i> O Lançamento está desbalanceado. O Débito tem de ser igual ao Crédito.
                                    </div>
                                    <div id="balanceOk" class="alert alert-success py-2 mt-2 text-center" style="display: none;">
                                        <i class="fas fa-check-circle me-2"></i> Lançamento Balanceado.
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-save" id="btnSubmit" disabled><i class="fas fa-check-double me-2"></i> Processar Diário</button>
            </div>
        </div>
    </form>
</div>

<script>
    const accounts = @json(\App\Models\ChartOfAccount::where('type', 'M')->get());
    let lines = [];
    let lineCounter = 0;

    function renderLines() {
        const container = document.getElementById('linesContainer');
        container.innerHTML = '';
        
        let totalD = 0;
        let totalC = 0;

        lines.forEach((line, index) => {
            if (line.type_dc === 'D') totalD += line.value;
            if (line.type_dc === 'C') totalC += line.value;

            let tr = document.createElement('tr');
            
            // Options generator
            let opts = `<option value="">Selecione...</option>`;
            accounts.forEach(acc => {
                let selected = line.account_code === acc.code ? 'selected' : '';
                opts += `<option value="${acc.code}" ${selected}>${acc.code} - ${acc.description}</option>`;
            });

            tr.innerHTML = `
                <td>
                    <select name="lines[${index}][account_code]" class="form-select form-control-custom" onchange="updateLine(${index}, 'account_code', this.value)" required>
                        ${opts}
                    </select>
                </td>
                <td>
                    <select name="lines[${index}][type_dc]" class="form-select form-control-custom fw-bold ${line.type_dc === 'D' ? 'text-info' : 'text-warning'}" onchange="updateLine(${index}, 'type_dc', this.value)" required>
                        <option value="D" ${line.type_dc === 'D' ? 'selected' : ''}>Débito (+D)</option>
                        <option value="C" ${line.type_dc === 'C' ? 'selected' : ''}>Crédito (+C)</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="lines[${index}][description]" class="form-control form-control-custom" value="${line.description}" oninput="updateLine(${index}, 'description', this.value)" placeholder="Igual à principal...">
                </td>
                <td>
                    <input type="number" step="0.01" name="lines[${index}][value]" class="form-control form-control-custom text-end fw-bold" value="${line.value}" oninput="updateLine(${index}, 'value', this.value)" required min="0.01">
                </td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeLine(${index})"><i class="fas fa-trash"></i></button>
                </td>
            `;
            container.appendChild(tr);
        });

        document.getElementById('totalDDisplay').innerText = totalD.toFixed(2);
        document.getElementById('totalCDisplay').innerText = totalC.toFixed(2);

        let balanceOk = Math.abs(totalD - totalC) < 0.01 && totalD > 0;
        
        document.getElementById('balanceAlert').style.display = (!balanceOk && lines.length > 0) ? 'block' : 'none';
        document.getElementById('balanceOk').style.display = balanceOk ? 'block' : 'none';
        document.getElementById('btnSubmit').disabled = !balanceOk;
    }

    function addLine() {
        lines.push({
            id: lineCounter++,
            account_code: '',
            type_dc: lines.length % 2 === 0 ? 'D' : 'C',
            description: '',
            value: 0
        });
        renderLines();
    }

    function updateLine(index, field, value) {
        if(field === 'value') value = parseFloat(value) || 0;
        lines[index][field] = value;
        
        // Optimize rendering: only re-render totals to avoid losing input focus if not necessary.
        // For simplicity, we just re-render everything, but you might want to handle focus.
        renderLines();
    }

    function removeLine(index) {
        lines.splice(index, 1);
        renderLines();
    }

    // Initialize with 2 lines (Debito and Credito)
    addLine();
    addLine();
</script>
@endsection

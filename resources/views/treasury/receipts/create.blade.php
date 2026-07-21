@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        background: #ffffff;
    }
    .form-control:focus, .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
    }
    .pending-amount {
        color: #ef4444;
        font-weight: bold;
    }
</style>
@endpush

@section('content')
@php
    $title = $category === 'recebimentos' ? 'Emitir Recibo (Recebimento)' : 'Emitir Pagamento';
    $icon = $category === 'recebimentos' ? 'fa-hand-holding-usd text-success' : 'fa-money-check-alt text-danger';
    $entityLabel = $category === 'recebimentos' ? 'Cliente' : 'Fornecedor';
@endphp
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('tesouraria.documentos.index', $category) }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="fas fa-arrow-left me-1"></i> Voltar aos Documentos
        </a>
        <h2 class="fw-bold mb-0 text-dark"><i class="fas {{ $icon }} me-2"></i>{{ $title }}</h2>
    </div>

    @if($errors->any())
        <div class="alert alert-danger shadow-sm" style="border-radius: 10px;">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger shadow-sm" style="border-radius: 10px;">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <!-- Coluna de Seleção de Entidade -->
        <div class="col-lg-4 mb-4">
            <div class="card-premium p-4 h-100">
                <h5 class="fw-bold border-bottom pb-2 mb-4">1. Selecionar Entidade</h5>
                <form action="{{ route('tesouraria.documentos.create', $category) }}" method="GET" id="entityForm">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ $entityLabel }} <span class="text-danger">*</span></label>
                        <select name="entity_id" class="form-select select2" onchange="document.getElementById('entityForm').submit()" required>
                            <option value="">Selecione o {{ $entityLabel }}...</option>
                            @foreach($thirdParties as $tp)
                                @if(($category === 'recebimentos' && $tp->is_customer) || ($category === 'pagamentos' && $tp->is_supplier))
                                    <option value="{{ $tp->id }}" {{ $selectedEntityId == $tp->id ? 'selected' : '' }}>
                                        {{ $tp->name }} (NIF: {{ $tp->tax_id ?? 'N/D' }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </form>

                @if($selectedEntityId)
                <div class="alert alert-info mt-4" style="border-radius: 8px;">
                    <i class="fas fa-info-circle me-2"></i> Escolha os documentos que pretende liquidar na tabela ao lado.
                </div>
                @endif
            </div>
        </div>

        <!-- Coluna do Documento e Linhas a Liquidar -->
        <div class="col-lg-8 mb-4">
            <div class="card-premium p-4 h-100">
                <h5 class="fw-bold border-bottom pb-2 mb-4">2. Detalhes do Documento Financeiro</h5>
                
                @if(!$selectedEntityId)
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-arrow-left fa-3x mb-3 text-light"></i>
                        <h5>Selecione primeiro um {{ $entityLabel }}</h5>
                        <p>Para visualizar as faturas pendentes e preencher o recibo.</p>
                    </div>
                @else
                    <form action="{{ route('tesouraria.documentos.store', $category) }}" method="POST" id="receiptForm">
                        @csrf
                        <input type="hidden" name="third_party_id" value="{{ $selectedEntityId }}">

                        <!-- Detalhes do Cabeçalho -->
                        <div class="row g-3 mb-4 bg-light p-3 rounded border">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Data de Emissão <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Série Documental <span class="text-danger">*</span></label>
                                <select name="series_id" class="form-select" required>
                                    @foreach($series as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }} (Atual: {{ $s->current_number }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Método de Pagamento <span class="text-danger">*</span></label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="TB">Transferência Bancária</option>
                                    <option value="MB">Referência Multibanco</option>
                                    <option value="CC">Cartão de Crédito/Débito</option>
                                    <option value="NU">Numerário</option>
                                    <option value="CH">Cheque</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Conta de Tesouraria <span class="text-danger">*</span></label>
                                <select name="treasury_account_id" class="form-select" required>
                                    <option value="">Selecione o Banco/Caixa onde o dinheiro {{ $category == 'recebimentos' ? 'entrou' : 'saiu' }}...</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->currency }}) - Saldo: {{ number_format($acc->current_balance, 2, ',', '.') }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nº de Comprovativo / Operação (Opcional)</label>
                                <input type="text" name="payment_reference" class="form-control" placeholder="Ex: Trf 98213344">
                            </div>
                        </div>

                        <!-- Lista de Documentos Pendentes -->
                        <h6 class="fw-bold mb-3"><i class="fas fa-list-ul text-primary me-2"></i>Documentos Pendentes de Liquidação</h6>
                        
                        <div class="table-responsive border rounded mb-4">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Documento</th>
                                        <th>Data</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-end">Pendente</th>
                                        <th class="text-end" style="width: 200px;">Valor a Liquidar (AOA)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pendingDocs as $index => $doc)
                                    @php
                                        $total = $doc->total_amount + $doc->total_tax;
                                        $pending = $total - $doc->amount_paid;
                                        $inputName = $category === 'recebimentos' ? "items[{$index}][sale_id]" : "items[{$index}][purchase_invoice_id]";
                                    @endphp
                                    <tr>
                                        <td>
                                            <input type="hidden" name="{{ $inputName }}" value="{{ $doc->id }}">
                                            <span class="fw-bold">{{ $doc->doc_type }} {{ $doc->doc_number }}</span>
                                        </td>
                                        <td>{{ $doc->date->format('d/m/Y') }}</td>
                                        <td class="text-end">{{ number_format($total, 2, ',', '.') }}</td>
                                        <td class="text-end pending-amount">{{ number_format($pending, 2, ',', '.') }}</td>
                                        <td class="text-end">
                                            <input type="number" step="0.01" max="{{ $pending }}" name="items[{{ $index }}][amount_paid]" class="form-control text-end amount-input" placeholder="0.00" oninput="calculateTotal()">
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            Não existem documentos pendentes para esta entidade.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                @if(count($pendingDocs) > 0)
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">Total a Liquidar Neste Documento:</td>
                                        <td class="text-end fw-bold text-primary fs-5">
                                            <span id="grandTotal">0,00</span> AOA
                                        </td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>

                        <div class="text-end mt-4 pt-3 border-top">
                            <a href="{{ route('tesouraria.documentos.index', $category) }}" class="btn btn-light border fw-bold me-2 px-4" style="border-radius:10px;">Cancelar</a>
                            <button type="submit" class="btn btn-primary fw-bold px-4" style="border-radius:10px; font-size: 1.1rem;" {{ count($pendingDocs) == 0 ? 'disabled' : '' }}>
                                <i class="fas fa-check-circle me-2"></i> Emitir Documento Financeiro
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.amount-input').forEach(function(input) {
            let val = parseFloat(input.value);
            if (!isNaN(val)) {
                total += val;
            }
        });
        
        // Formatar para exibição
        document.getElementById('grandTotal').innerText = new Intl.NumberFormat('pt-PT', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(total);
    }
</script>
@endpush
@endsection

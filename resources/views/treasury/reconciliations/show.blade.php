@extends('layouts.app')

@push('styles')
<style>
    body { background-color: #f8fafc; }
    .workspace-header { background: #1e293b; color: white; padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; }
    .rec-panel { background: white; border-radius: 12px; border: 1px solid #e2e8f0; height: calc(100vh - 250px); display: flex; flex-direction: column; }
    .rec-panel-header { background: #f1f5f9; padding: 1rem; border-bottom: 1px solid #e2e8f0; border-radius: 12px 12px 0 0; font-weight: 600; }
    .rec-list { flex: 1; overflow-y: auto; padding: 0.5rem; }
    .rec-item { border: 1px solid #cbd5e1; border-radius: 8px; padding: 1rem; margin-bottom: 0.5rem; cursor: pointer; transition: all 0.2s; background: white; }
    .rec-item:hover { border-color: #94a3b8; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
    .rec-item.selected { border-color: #3b82f6; background-color: #eff6ff; box-shadow: 0 0 0 2px rgba(59,130,246,0.3); }
    .status-badge { padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: bold; }
    .balance-box { background: #334155; padding: 1rem; border-radius: 8px; text-align: center; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4" id="reconciliationApp">
    
    <div class="workspace-header d-flex justify-content-between align-items-center shadow-sm">
        <div>
            <h3 class="mb-1 fw-bold"><i class="fas fa-check-double me-2"></i>Área de Reconciliação Bancária</h3>
            <p class="mb-0 text-white-50">Conta: <strong>{{ $reconciliation->account_code }}</strong> | Data Extrato: <strong>{{ \Carbon\Carbon::parse($reconciliation->reconciliation_date)->format('d/m/Y') }}</strong></p>
        </div>
        <div class="d-flex gap-4">
            <div class="balance-box">
                <div class="text-white-50 small text-uppercase">Saldo Inicial (Extrato)</div>
                <div class="fs-5 fw-bold">{{ number_format($reconciliation->opening_balance, 2) }}</div>
            </div>
            <div class="balance-box">
                <div class="text-white-50 small text-uppercase">Saldo Final (Extrato)</div>
                <div class="fs-5 fw-bold text-info">{{ number_format($reconciliation->closing_balance, 2) }}</div>
            </div>
            
            @if($reconciliation->status == 'OPEN')
                <form action="{{ route('tesouraria.reconciliations.close', $reconciliation->id) }}" method="POST" class="d-flex align-items-center ms-3" onsubmit="return confirm('Tem a certeza que pretende fechar esta reconciliação? Não poderá fazer mais correspondências.')">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-lg shadow-sm"><i class="fas fa-lock me-2"></i> Fechar Reconciliação</button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    @if($reconciliation->status == 'OPEN')
        <form action="{{ route('tesouraria.reconciliations.match', $reconciliation->id) }}" method="POST" id="matchForm">
            @csrf
            
            <div class="card mb-4 border-0 shadow-sm bg-light">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 text-dark fw-bold">Correspondência de Movimentos</h5>
                        <p class="mb-0 text-muted small">Selecione linhas à esquerda e à direita. O saldo selecionado tem de ser 0.00 para efetuar a correspondência.</p>
                    </div>
                    <div class="d-flex align-items-center gap-4">
                        <div class="text-end">
                            <div class="small text-muted text-uppercase fw-bold">Diferença Selecionada</div>
                            <div class="fs-4 fw-bold" id="diffDisplay">0.00 Kz</div>
                        </div>
                        <button type="button" class="btn btn-primary btn-lg" id="btnMatch" onclick="submitMatch()" disabled>
                            <i class="fas fa-link me-2"></i> Reconciliar Selecionados
                        </button>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Esquerda: Extrato Bancário -->
                <div class="col-md-6">
                    <div class="rec-panel shadow-sm">
                        <div class="rec-panel-header d-flex justify-content-between">
                            <span class="text-primary"><i class="fas fa-file-invoice-dollar me-2"></i>1. Linhas do Extrato Bancário</span>
                            <span class="badge bg-primary rounded-pill">{{ count($bankStatements) }} pendentes</span>
                        </div>
                        <div class="rec-list">
                            @forelse($bankStatements as $bs)
                                <label class="rec-item d-block w-100 m-0">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($bs->date)->format('d/m/Y') }}</div>
                                        <input type="checkbox" name="bank_lines[]" value="{{ $bs->id }}" class="form-check-input ms-2 ext-check" data-val="{{ $bs->type_dc == 'C' ? $bs->value : -$bs->value }}" onchange="calculateDiff()">
                                    </div>
                                    <div class="text-muted small mb-2">{{ $bs->description }}</div>
                                    <div class="d-flex justify-content-between">
                                        <span class="small text-secondary">{{ $bs->reference ?? 'Sem Ref' }}</span>
                                        <span class="fw-bold {{ $bs->type_dc == 'C' ? 'text-success' : 'text-danger' }}">
                                            {{ $bs->type_dc == 'C' ? '+' : '-' }}{{ number_format($bs->value, 2) }} Kz
                                        </span>
                                    </div>
                                </label>
                            @empty
                                <div class="text-center py-5 text-muted">
                                    <i class="fas fa-check-circle fa-2x mb-3 text-success"></i>
                                    <p>Não há linhas de extrato pendentes.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Direita: Movimentos do ERP (Receipts) -->
                <div class="col-md-6">
                    <div class="rec-panel shadow-sm">
                        <div class="rec-panel-header d-flex justify-content-between">
                            <span class="text-warning text-dark"><i class="fas fa-desktop me-2"></i>2. Movimentos no Sistema (Tesouraria)</span>
                            <span class="badge bg-warning text-dark rounded-pill">{{ count($receipts) }} pendentes</span>
                        </div>
                        <div class="rec-list">
                            @forelse($receipts as $rc)
                                <label class="rec-item d-block w-100 m-0">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($rc->date)->format('d/m/Y') }}</div>
                                        <input type="checkbox" name="receipts[]" value="{{ $rc->id }}" class="form-check-input ms-2 sys-check" data-val="{{ $rc->type == 'IN' ? $rc->amount : -$rc->amount }}" onchange="calculateDiff()">
                                    </div>
                                    <div class="text-muted small mb-2">{{ $rc->description }}</div>
                                    <div class="d-flex justify-content-between">
                                        <span class="small text-secondary">{{ $rc->reference ?? 'Sem Ref' }}</span>
                                        <span class="fw-bold {{ $rc->type == 'IN' ? 'text-success' : 'text-danger' }}">
                                            {{ $rc->type == 'IN' ? '+' : '-' }}{{ number_format($rc->amount, 2) }} Kz
                                        </span>
                                    </div>
                                </label>
                            @empty
                                <div class="text-center py-5 text-muted">
                                    <i class="fas fa-check-circle fa-2x mb-3 text-success"></i>
                                    <p>Não há movimentos de sistema pendentes.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @else
        <div class="alert alert-info d-flex align-items-center p-4 shadow-sm border-0 rounded-3">
            <i class="fas fa-lock fa-3x me-4 opacity-50"></i>
            <div>
                <h4 class="fw-bold mb-1">Reconciliação Fechada</h4>
                <p class="mb-0">Esta reconciliação bancária já foi terminada e selada no dia {{ \Carbon\Carbon::parse($reconciliation->updated_at)->format('d/m/Y H:i') }}. Não é possível efetuar mais alterações.</p>
            </div>
        </div>
    @endif
</div>

<script>
    function calculateDiff() {
        let extSum = 0;
        let sysSum = 0;

        document.querySelectorAll('.ext-check:checked').forEach(el => {
            extSum += parseFloat(el.dataset.val);
            el.closest('.rec-item').classList.add('selected');
        });
        document.querySelectorAll('.ext-check:not(:checked)').forEach(el => {
            el.closest('.rec-item').classList.remove('selected');
        });

        document.querySelectorAll('.sys-check:checked').forEach(el => {
            sysSum += parseFloat(el.dataset.val);
            el.closest('.rec-item').classList.add('selected');
        });
        document.querySelectorAll('.sys-check:not(:checked)').forEach(el => {
            el.closest('.rec-item').classList.remove('selected');
        });

        // A correspondência está correta se a soma do extrato for igual à soma do sistema
        let diff = extSum - sysSum;
        let diffDisplay = document.getElementById('diffDisplay');
        let btnMatch = document.getElementById('btnMatch');

        diffDisplay.innerText = Math.abs(diff).toLocaleString('pt-AO', { minimumFractionDigits: 2 }) + ' Kz';

        let hasSelection = document.querySelectorAll('.ext-check:checked').length > 0 && document.querySelectorAll('.sys-check:checked').length > 0;

        if (Math.abs(diff) < 0.01 && hasSelection) {
            diffDisplay.className = 'fs-4 fw-bold text-success';
            btnMatch.disabled = false;
        } else {
            diffDisplay.className = 'fs-4 fw-bold text-danger';
            btnMatch.disabled = true;
        }
    }

    function submitMatch() {
        document.getElementById('btnMatch').disabled = true;
        document.getElementById('btnMatch').innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processando...';
        document.getElementById('matchForm').submit();
    }
</script>
@endsection

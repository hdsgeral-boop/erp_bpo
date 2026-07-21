@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h2 class="view-title mb-0"><i class="fas fa-cogs text-warning"></i> Rotinas de Contabilidade</h2>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Fechos de Mês, processamento em lote e apuramentos automáticos.</p>
        </div>
    </div>

    <!-- Feedback Container -->
    <div id="ajax-feedback" style="display: none;" class="alert"></div>

    <div class="row">
        <!-- Rotina: Imposto de Selo -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 border-start border-warning border-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-stamp text-warning"></i> Apuramento de Imposto de Selo</h5>
                    <p class="card-text text-muted small">Processa e liquida o IS (1%) sobre todos os recibos não contabilizados do mês selecionado.</p>
                    
                    <form onsubmit="runRoutine(event, '{{ route('accounting.routines.stamp') }}')">
                        @csrf
                        <div class="input-group mb-3 mt-3">
                            <select name="month" class="form-select" required>
                                <option value="06" selected>Junho (06)</option>
                                <option value="07">Julho (07)</option>
                            </select>
                            <select name="year" class="form-select" required>
                                <option value="2026" selected>2026</option>
                            </select>
                            <button class="btn btn-warning" type="submit" id="btn-stamp">
                                <i class="fas fa-play"></i> Processar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Rotina: Amortizações -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 border-start border-info border-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-percent text-info"></i> Lançamento de Amortizações</h5>
                    <p class="card-text text-muted small">Gera os movimentos no diário para a depreciação mensal de todos os ativos.</p>
                    <button class="btn btn-info text-white mt-3 w-100" onclick="alert('Funcionalidade em integração.')">
                        <i class="fas fa-play"></i> Iniciar Processamento
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    async function runRoutine(e, url) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const btn = form.querySelector('button[type="submit"]');
        
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> A executar...';

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();
            
            if (response.ok && data.success) {
                showFeedback(data.message, 'success');
            } else {
                showFeedback(data.message || 'Erro no processamento.', 'danger');
            }
        } catch (error) {
            showFeedback('Falha de comunicação com o servidor.', 'danger');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }

    function showFeedback(msg, type) {
        const box = document.getElementById('ajax-feedback');
        box.className = `alert alert-${type}`;
        box.innerText = msg;
        box.style.display = 'block';
        setTimeout(() => box.style.display = 'none', 4000);
    }
</script>
@endpush
@endsection

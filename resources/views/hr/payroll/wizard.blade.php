@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        background: #ffffff;
    }
    .wizard-step {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        font-weight: bold;
    }
    .wizard-step.active {
        background-color: #0d6efd;
        color: white;
    }
    .wizard-step.pending {
        background-color: #e9ecef;
        color: #6c757d;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-play-circle text-primary me-2"></i>Processar Salários</h2>
            <p class="text-muted mt-1">Siga os passos para simular e fechar o processamento mensal.</p>
        </div>
        <a href="{{ route('rh.salarios.index') }}" class="btn btn-light border fw-bold text-muted">
            <i class="fas fa-arrow-left me-1"></i> Voltar
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card card-premium mb-4">
        <div class="card-body p-5">
            
            <div class="d-flex justify-content-center mb-5 position-relative">
                <div class="position-absolute top-50 start-50 translate-middle w-50 bg-light" style="height: 4px; z-index: 0;"></div>
                <div class="d-flex justify-content-between position-relative w-50" style="z-index: 1;">
                    <div class="text-center bg-white px-2">
                        <div class="wizard-step active mx-auto mb-2 shadow-sm">1</div>
                        <span class="fw-bold text-primary small">Parâmetros</span>
                    </div>
                    <div class="text-center bg-white px-2">
                        <div class="wizard-step pending mx-auto mb-2">2</div>
                        <span class="fw-bold text-muted small">Simulação</span>
                    </div>
                    <div class="text-center bg-white px-2">
                        <div class="wizard-step pending mx-auto mb-2">3</div>
                        <span class="fw-bold text-muted small">Fecho</span>
                    </div>
                </div>
            </div>

            <form action="{{ route('rh.salarios.process') }}" method="POST">
                @csrf
                <div class="row g-4 justify-content-center">
                    <div class="col-md-8">
                        <h5 class="fw-bold border-bottom pb-2 mb-4">Parâmetros do Processamento</h5>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Mês de Referência</label>
                                <select name="month" class="form-select" required>
                                    @for($i=1; $i<=12; $i++)
                                        <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>{{ str_pad($i, 2, '0', STR_PAD_LEFT) }} - {{ date('F', mktime(0, 0, 0, $i, 10)) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Ano</label>
                                <input type="number" name="year" class="form-control" value="{{ $year }}" required>
                            </div>
                            <div class="col-md-12 mt-4">
                                <label class="form-label fw-bold small">Selecionar Funcionários</label>
                                <div class="card border">
                                    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                        <span class="fw-medium small"><i class="fas fa-users text-primary me-1"></i> Lista de Colaboradores Ativos</span>
                                        <div class="form-check m-0">
                                            <input class="form-check-input" type="checkbox" id="selectAll" checked>
                                            <label class="form-check-label small" for="selectAll">Selecionar Todos</label>
                                        </div>
                                    </div>
                                    <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                                        <div class="list-group list-group-flush">
                                            @foreach($employees as $emp)
                                            <label class="list-group-item d-flex gap-3">
                                                <input class="form-check-input flex-shrink-0 emp-checkbox" type="checkbox" name="employee_ids[]" value="{{ $emp->id }}" checked>
                                                <span>
                                                    <span class="fw-bold d-block">{{ $emp->first_name }} {{ $emp->last_name }}</span>
                                                    <small class="text-muted d-block">Depart.: {{ $emp->department ? $emp->department->name : '-' }} | Contrato: {{ \App\Models\Contract::where('employee_id', $emp->id)->exists() ? 'Ativo' : 'S/ Contrato' }}</small>
                                                </span>
                                            </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-primary fw-bold px-4 py-2">
                                Iniciar Simulação <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('selectAll').addEventListener('change', function() {
        let checkboxes = document.querySelectorAll('.emp-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
</script>
@endpush
@endsection

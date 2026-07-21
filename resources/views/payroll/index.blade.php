@extends('layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    .card-premium {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    .table-custom { margin-bottom: 0; }
    .table-custom thead th {
        background-color: #f8fafc; color: #475569; font-weight: 600; font-size: 0.85rem; padding: 1rem 1.5rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0;
    }
    .table-custom tbody td { padding: 1rem 1.5rem; vertical-align: middle; color: #1e293b; border-bottom: 1px solid #f1f5f9; }
    .table-custom tbody tr:hover { background-color: #f8fafc; }
    .btn-action { border-radius: 8px; padding: 0.4rem 0.8rem; transition: all 0.2s; background: #f1f5f9; color: #475569; border: none; }
    .btn-action:hover { background: #e2e8f0; color: #1e293b; transform: translateY(-2px); }
    
    .btn-primary-custom {
        background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; border-radius: 10px; padding: 0.75rem 1.5rem; font-weight: 600; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3); transition: all 0.2s; border: none;
    }
    .btn-primary-custom:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4); color: white; }
    
    .btn-success-custom {
        background: linear-gradient(135deg, #10b981, #059669); color: white; border-radius: 10px; padding: 0.75rem 1.5rem; font-weight: 600; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3); transition: all 0.2s; border: none;
    }
    .btn-success-custom:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.4); color: white; }

    .stat-card {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1.5rem;
        border: 1px solid #e2e8f0;
    }
    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
    }
    .stat-label {
        color: #64748b;
        font-size: 0.875rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-calculator text-primary me-2"></i>Processamento Salarial</h2>
            <p class="text-muted mb-0">Cálculo de vencimentos e retenções (INSS e IRT).</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm" style="border-radius: 10px; border-left: 4px solid #10b981;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger shadow-sm" style="border-radius: 10px; border-left: 4px solid #ef4444;">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <!-- Calculadora de Simulação -->
        <div class="col-lg-4 mb-4">
            <div class="card-premium h-100">
                <h5 class="fw-bold mb-4"><i class="fas fa-cog text-muted me-2"></i>Configurar Processamento</h5>
                <form action="{{ route('rh.payroll.process') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted fw-semibold small text-uppercase">Mês de Referência</label>
                        <select name="month" class="form-select form-control-custom">
                            @for($m=1; $m<=12; $m++)
                                <option value="{{ sprintf('%02d', $m) }}" {{ (isset($month) ? $month : date('m')) == sprintf('%02d', $m) ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->locale('pt')->monthName }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted fw-semibold small text-uppercase">Ano</label>
                        <input type="number" name="year" class="form-control form-control-custom" value="{{ $year ?? date('Y') }}" min="2020" max="2100">
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100"><i class="fas fa-play me-2"></i> Simular Folha</button>
                    
                    @if(isset($results) && count($results) > 0)
                        <hr class="my-4">
                        <p class="small text-muted mb-3"><i class="fas fa-info-circle me-1"></i> Se a simulação estiver correta, feche a folha para guardar o histórico.</p>
                        @if($existingRun)
                            <div class="alert alert-warning py-2 small mb-0"><i class="fas fa-exclamation-triangle me-1"></i> Folha já fechada para este período.</div>
                        @else
                            <button type="submit" name="save_run" value="1" class="btn btn-success-custom w-100" onclick="return confirm('Tem certeza? A folha será fechada e os recibos emitidos.')"><i class="fas fa-lock me-2"></i> Fechar e Gravar Folha</button>
                        @endif
                    @endif
                </form>
            </div>
        </div>

        <!-- Resultados da Simulação -->
        <div class="col-lg-8 mb-4">
            @if(isset($results))
            <div class="card-premium h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Resultados da Simulação <span class="badge bg-light text-dark ms-2 border">{{ $month }}/{{ $year }}</span></h5>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Colaborador</th>
                                <th class="text-end">Rendimentos</th>
                                <th class="text-end">Retenções</th>
                                <th class="text-end">Líquido (Kz)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($results as $res)
                            <tr>
                                <td class="fw-bold">{{ $res['employee'] }}</td>
                                <td class="text-end">
                                    <div class="fw-semibold text-success">+{{ number_format($res['base'] + $res['outros_venc'], 2, ',', '.') }}</div>
                                    <small class="text-muted d-block">Base: {{ number_format($res['base'], 2, ',', '.') }}</small>
                                </td>
                                <td class="text-end">
                                    <div class="fw-semibold text-danger">-{{ number_format($res['inss_employee'] + $res['irt'] + $res['descontos'], 2, ',', '.') }}</div>
                                    <small class="text-muted d-block">IRT: {{ number_format($res['irt'], 2, ',', '.') }} | INSS: {{ number_format($res['inss_employee'], 2, ',', '.') }}</small>
                                </td>
                                <td class="text-end fw-bold fs-5 text-dark">
                                    {{ number_format($res['liquido'], 2, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Nenhum colaborador elegível para processamento.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="card-premium h-100 d-flex flex-column align-items-center justify-content-center text-muted" style="min-height: 300px;">
                <i class="fas fa-calculator fa-3x mb-3 opacity-50"></i>
                <h5>Nenhuma simulação ativa</h5>
                <p>Selecione o mês e ano ao lado para visualizar a folha de salários.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Histórico de Processamentos -->
    <div class="card-premium">
        <h5 class="fw-bold mb-4"><i class="fas fa-history text-muted me-2"></i>Histórico de Processamentos (Recibos)</h5>
        
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Referência</th>
                        <th>Estado</th>
                        <th>Colaboradores</th>
                        <th class="text-end">Base Sujeita</th>
                        <th class="text-end">Total INSS</th>
                        <th class="text-end">Total IRT</th>
                        <th class="text-end">Líquido Pago (Kz)</th>
                        <th class="text-center">Recibos</th>
                    </tr>
                </thead>
                <tbody>
                    @php $runs = \App\Models\PayrollRun::withCount('receipts')->orderBy('id', 'desc')->get(); @endphp
                    @forelse($runs as $run)
                    <tr>
                        <td class="fw-bold"><i class="far fa-calendar-alt text-muted me-2"></i> {{ str_pad($run->month, 2, '0', STR_PAD_LEFT) }}/{{ $run->year }}</td>
                        <td><span class="badge bg-success-subtle text-success px-2 py-1 rounded">{{ $run->status }}</span></td>
                        <td>{{ $run->receipts_count }}</td>
                        <td class="text-end">{{ number_format($run->total_base, 2, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($run->total_inss, 2, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($run->total_irt, 2, ',', '.') }}</td>
                        <td class="text-end fw-bold">{{ number_format($run->total_net_paid, 2, ',', '.') }}</td>
                        <td class="text-center">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-action dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-print me-1"></i> Imprimir
                                </button>
                                <ul class="dropdown-menu shadow border-0" style="border-radius: 10px;">
                                    @foreach(\App\Models\PayrollReceipt::where('payroll_run_id', $run->id)->get() as $rec)
                                        <li><a class="dropdown-item py-2" target="_blank" href="{{ route('rh.payroll.receipt', $rec->id) }}"><i class="fas fa-file-pdf text-danger me-2"></i> {{ $rec->employee->name }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Ainda não existem folhas fechadas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

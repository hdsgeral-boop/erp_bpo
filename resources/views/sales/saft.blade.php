@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h2 class="view-title mb-0"><i class="fas fa-file-code text-primary"></i> Exportação SAFT-AO</h2>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Geração do Ficheiro Normalizado de Auditoria Tributária para submissão na AGT.</p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="{{ route('vendas.saft.export') }}" method="POST">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ano Fiscal</label>
                                <select name="year" class="form-select" required>
                                    @for($i = date('Y'); $i >= 2020; $i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Mês de Faturação</label>
                                <select name="month" class="form-select" required>
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ date('m') == $m ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $m, 10)) }} ({{ str_pad($m, 2, '0', STR_PAD_LEFT) }})
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="alert alert-info bg-opacity-10 border-info">
                            <i class="fas fa-info-circle me-2"></i> O ficheiro será gerado respeitando a norma <strong>urn:OECD:StandardAuditFile-Tax:AO_1.01_01</strong>.
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-download me-2"></i> Gerar e Descarregar XML
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card border-0 bg-light">
                <div class="card-body p-4 text-center">
                    <i class="fas fa-shield-alt fa-4x text-success mb-3 opacity-50"></i>
                    <h5>Exportação Validada</h5>
                    <p class="text-muted">A arquitetura garante que a assinatura digital de cada fatura (Hash) é transportada para o SAFT exatamente como foi selada na emissão original.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

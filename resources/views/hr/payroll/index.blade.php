@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        background: #ffffff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-file-invoice-dollar text-primary me-2"></i>Histórico de Processamentos</h2>
            <p class="text-muted mt-1">Consulte os processamentos salariais fechados e emita recibos.</p>
        </div>
        <a href="{{ route('rh.salarios.wizard') }}" class="btn btn-primary fw-bold">
            <i class="fas fa-play me-1"></i> Novo Processamento
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card-premium">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Referência</th>
                        <th>Período</th>
                        <th>Total Ilíquido</th>
                        <th>Total INSS</th>
                        <th>Total IRT</th>
                        <th>Líquido a Pagar</th>
                        <th>Estado</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($runs as $run)
                    <tr>
                        <td class="ps-4 fw-bold text-dark"><i class="fas fa-archive text-muted me-2"></i>{{ $run->reference }}</td>
                        <td>{{ str_pad($run->month, 2, '0', STR_PAD_LEFT) }} / {{ $run->year }}</td>
                        <td class="text-secondary">{{ number_format($run->total_base + $run->total_additions, 2, ',', '.') }} Kz</td>
                        <td class="text-warning text-dark">{{ number_format($run->total_inss, 2, ',', '.') }} Kz</td>
                        <td class="text-danger">{{ number_format($run->total_irt, 2, ',', '.') }} Kz</td>
                        <td class="fw-bold text-success">{{ number_format($run->total_net_paid, 2, ',', '.') }} Kz</td>
                        <td>
                            @if($run->status == 'PROCESSED')
                                <span class="badge bg-success"><i class="fas fa-check me-1"></i> Fechado</span>
                            @else
                                <span class="badge bg-secondary">{{ $run->status }}</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            @if($run->status == 'PROCESSED' && !$run->is_reversed)
                            <form action="{{ route('rh.salarios.reverse', $run->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tem a certeza que deseja estornar este processamento? Esta ação irá gerar movimentos inversos na Contabilidade e cancelar pagamentos pendentes.');">
                                    <i class="fas fa-undo"></i> Estornar
                                </button>
                            </form>
                            @endif
                            <a href="{{ route('rh.salarios.export_agt', $run->id) }}" class="btn btn-sm btn-success text-white">
                                <i class="fas fa-file-excel"></i> Mapa AGT
                            </a>
                            <a href="#" class="btn btn-sm btn-light border text-primary">
                                <i class="fas fa-eye"></i> Recibos
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-file-invoice-dollar fs-4 mb-2 d-block opacity-50"></i>
                            Nenhum processamento salarial efetuado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">
            {{ $runs->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection

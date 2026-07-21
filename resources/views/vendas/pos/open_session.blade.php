@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow-lg border-0" style="width: 100%; max-width: 500px; border-radius: 1rem;">
        <div class="card-header bg-primary text-white text-center py-4" style="border-radius: 1rem 1rem 0 0;">
            <h3 class="mb-0"><i class="fas fa-cash-register me-2"></i>Frente de Caixa (POS)</h3>
            <p class="mb-0 mt-2 opacity-75">Abertura de Turno</p>
        </div>
        <div class="card-body p-5">
            <form action="{{ route('vendas.pos.open') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="form-label text-muted fw-bold">Selecione o Posto / Caixa</label>
                    <select name="pos_register_id" class="form-select form-select-lg" required>
                        <option value="">-- Escolha a Caixa --</option>
                        @foreach($registers as $reg)
                            <option value="{{ $reg->id }}" {{ $reg->status == 'OPEN' ? 'disabled' : '' }}>
                                {{ $reg->name }} {{ $reg->status == 'OPEN' ? '(Ocupada)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-5">
                    <label class="form-label text-muted fw-bold">Fundo de Maneio (Abertura)</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-light border-end-0">Kz</span>
                        <input type="number" step="0.01" min="0" name="opening_balance" class="form-control border-start-0" value="0.00" required>
                    </div>
                    <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle"></i> Valor em numerário presente na gaveta no início do turno.</small>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg py-3 fw-bold" style="border-radius: 0.5rem;">
                        <i class="fas fa-lock-open me-2"></i> Abrir Caixa e Iniciar Vendas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

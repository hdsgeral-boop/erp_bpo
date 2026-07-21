@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        background: #ffffff;
    }
    .nav-pills .nav-link {
        color: #64748b;
        border-radius: 8px;
        padding: 0.8rem 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        transition: all 0.2s;
    }
    .nav-pills .nav-link:hover {
        background-color: #f1f5f9;
        color: #1e293b;
    }
    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        color: white;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-cogs text-primary me-2"></i>Configurações do Sistema</h2>
        <p class="text-muted mt-1">Gestão centralizada de definições e variáveis globais do ERP.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm" style="border-radius: 10px;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card-premium p-3">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    @php $first = true; @endphp
                    @foreach($settings as $group => $groupSettings)
                        <button class="nav-link text-start {{ $first ? 'active' : '' }}" id="v-pills-{{ \Illuminate\Support\Str::slug($group) }}-tab" data-bs-toggle="pill" data-bs-target="#v-pills-{{ \Illuminate\Support\Str::slug($group) }}" type="button" role="tab">
                            @if($group == 'Geral') <i class="fas fa-sliders-h me-2"></i>
                            @elseif($group == 'Financeiro') <i class="fas fa-file-invoice-dollar me-2"></i>
                            @elseif($group == 'Segurança') <i class="fas fa-shield-alt me-2"></i>
                            @else <i class="fas fa-cog me-2"></i> @endif
                            {{ $group }}
                        </button>
                        @php $first = false; @endphp
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card-premium p-4">
                <form action="{{ route('admin.settings.updateBulk') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="tab-content" id="v-pills-tabContent">
                        @php $first = true; @endphp
                        @foreach($settings as $group => $groupSettings)
                            <div class="tab-pane fade {{ $first ? 'show active' : '' }}" id="v-pills-{{ \Illuminate\Support\Str::slug($group) }}" role="tabpanel">
                                <h4 class="fw-bold border-bottom pb-2 mb-4 text-dark">{{ $group }}</h4>
                                
                                <div class="row g-4">
                                    @foreach($groupSettings as $setting)
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-dark">{{ $setting->description ?: mb_convert_case(str_replace('_', ' ', $setting->key), MB_CASE_TITLE) }}</label>
                                            
                                            @if($setting->type == 'boolean')
                                                <div class="form-check form-switch fs-5 mt-2">
                                                    <input type="hidden" name="{{ $setting->key }}" value="0">
                                                    <input class="form-check-input" type="checkbox" name="{{ $setting->key }}" value="1" {{ $setting->value == '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label fs-6 mt-1 ms-2">Ativado</label>
                                                </div>
                                            @elseif($setting->type == 'text')
                                                <input type="text" name="{{ $setting->key }}" class="form-control" value="{{ $setting->value }}">
                                            @elseif($setting->type == 'integer')
                                                <input type="number" name="{{ $setting->key }}" class="form-control" value="{{ $setting->value }}">
                                            @else
                                                <textarea name="{{ $setting->key }}" class="form-control" rows="3">{{ $setting->value }}</textarea>
                                            @endif
                                            <small class="text-muted d-block mt-1">Chave: <code>{{ $setting->key }}</code></small>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @php $first = false; @endphp
                        @endforeach
                    </div>

                    <div class="text-end mt-5 pt-3 border-top">
                        <button type="submit" class="btn btn-primary fw-bold px-4" style="border-radius:10px; font-size: 1.1rem;">
                            <i class="fas fa-save me-2"></i> Guardar Configurações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

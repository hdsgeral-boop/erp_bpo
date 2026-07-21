@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    .form-control, .form-select {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border: 1px solid #cbd5e1;
    }
    .form-control:focus, .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .form-label {
        font-weight: 600;
        color: #475569;
        font-size: 0.9rem;
    }
    .btn-save {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border-radius: 10px;
        padding: 0.6rem 2rem;
        font-weight: 600;
        border: none;
        box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
        transition: all 0.2s;
    }
    .btn-save:hover { color: white; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.4); }
    
    .nav-tabs-custom {
        border-bottom: 2px solid #e2e8f0;
        margin-bottom: 2rem;
    }
    .nav-tabs-custom .nav-link {
        color: #64748b;
        font-weight: 600;
        border: none;
        padding: 1rem 1.5rem;
        border-bottom: 3px solid transparent;
        transition: all 0.2s;
        border-radius: 0;
    }
    .nav-tabs-custom .nav-link:hover { color: #3b82f6; }
    .nav-tabs-custom .nav-link.active {
        color: #3b82f6;
        background: transparent;
        border-bottom: 3px solid #3b82f6;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-cogs text-primary me-2"></i>Parâmetros Globais</h2>
        <p class="text-muted mb-0">Configurações globais e comportamentos base do sistema ERP.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm" style="border-radius: 10px; border-left: 4px solid #10b981;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger shadow-sm" style="border-radius: 10px; border-left: 4px solid #ef4444;">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card-premium p-4 p-md-5">
        <form action="{{ route('config.settings.bulk') }}" method="POST">
            @csrf
            
            <ul class="nav nav-tabs nav-tabs-custom" id="settingsTab" role="tablist">
                @php $first = true; @endphp
                @foreach($settings as $group => $groupSettings)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $first ? 'active' : '' }}" id="tab-{{ Str::slug($group) }}" data-bs-toggle="tab" data-bs-target="#content-{{ Str::slug($group) }}" type="button" role="tab">
                            {{ $group }}
                        </button>
                    </li>
                    @php $first = false; @endphp
                @endforeach
            </ul>

            <div class="tab-content" id="settingsTabContent">
                @php $first = true; @endphp
                @foreach($settings as $group => $groupSettings)
                    <div class="tab-pane fade {{ $first ? 'show active' : '' }}" id="content-{{ Str::slug($group) }}" role="tabpanel">
                        <div class="row g-4">
                            @foreach($groupSettings as $setting)
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded" style="border: 1px solid #e2e8f0; height: 100%;">
                                        <label class="form-label d-block mb-1">{{ $setting->description ?: $setting->key }}</label>
                                        <div class="text-muted mb-3" style="font-size: 0.75rem; font-family: monospace;">Key: {{ $setting->key }}</div>
                                        
                                        @if($setting->type === 'boolean')
                                            <div class="form-check form-switch mt-2">
                                                <input type="hidden" name="{{ $setting->key }}" value="0">
                                                <input class="form-check-input" type="checkbox" role="switch" name="{{ $setting->key }}" value="1" id="setting_{{ $setting->id }}" {{ $setting->value == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="setting_{{ $setting->id }}">Ativar/Desativar</label>
                                            </div>
                                        @elseif($setting->type === 'integer')
                                            <input type="number" name="{{ $setting->key }}" class="form-control" value="{{ $setting->value }}">
                                        @else
                                            <input type="text" name="{{ $setting->key }}" class="form-control" value="{{ $setting->value }}">
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @php $first = false; @endphp
                @endforeach
            </div>

            <div class="d-flex justify-content-end mt-5 border-top pt-4">
                <button type="submit" class="btn btn-save"><i class="fas fa-save me-2"></i> Guardar Todas as Configurações</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
@endsection

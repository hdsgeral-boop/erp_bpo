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
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.4);
        color: white;
    }
    .btn-cancel {
        background: #f1f5f9;
        color: #475569;
        border-radius: 10px;
        padding: 0.6rem 2rem;
        font-weight: 600;
        border: none;
        transition: all 0.2s;
    }
    .btn-cancel:hover {
        background: #e2e8f0;
        color: #1e293b;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('admin.companies.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="fas fa-arrow-left me-1"></i> Voltar à Listagem
        </a>
        <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-edit text-primary me-2"></i>Editar Empresa</h2>
        <p class="text-muted mb-0">Modifique os dados de {{ $company->name }}.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm" style="border-radius: 10px; border-left: 4px solid #ef4444;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-premium p-4 p-md-5">
        <form action="{{ route('admin.companies.update', $company->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row g-4">
                <div class="col-md-8">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">Informação Base</h5>
                        </div>
                        
                        <div class="col-md-8">
                            <label class="form-label">Nome da Empresa <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $company->name) }}" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">NIF <span class="text-danger">*</span></label>
                            <input type="text" name="nif" class="form-control" value="{{ old('nif', $company->nif) }}" required>
                        </div>

                        <div class="col-md-12 mt-4">
                            <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">Localização</h5>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Província</label>
                            <input type="text" name="province" class="form-control" value="{{ old('province', $company->province) }}">
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Município</label>
                            <input type="text" name="municipality" class="form-control" value="{{ old('municipality', $company->municipality) }}">
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Comuna</label>
                            <input type="text" name="commune" class="form-control" value="{{ old('commune', $company->commune) }}">
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-4 bg-light rounded" style="border: 1px dashed #cbd5e1;">
                        <h6 class="fw-bold text-dark mb-3">Logótipo</h6>
                        
                        @if($company->logo)
                        <div class="text-center mb-3">
                            <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo Atual" class="img-fluid rounded shadow-sm" style="max-height: 100px;">
                            <div class="mt-2 text-muted" style="font-size: 0.8rem;">Logótipo Atual</div>
                        </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label" style="font-size: 0.8rem;">Alterar Logótipo</label>
                            <input type="file" name="logo" class="form-control" accept="image/*" id="logo-input">
                            <div class="form-text mt-2 text-muted" style="font-size: 0.8rem;">Deixe em branco para manter o atual.</div>
                        </div>
                        <div id="logo-preview" class="text-center mt-3 d-none">
                            <img src="" alt="Preview" class="img-fluid rounded shadow-sm" style="max-height: 100px;">
                        </div>
                    </div>

                    <div class="p-4 bg-light rounded mt-3" style="border: 1px solid #cbd5e1;">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_master_data" name="is_master_data" value="1" {{ old('is_master_data', $company->is_master_data) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_master_data">Empresa Matriz (Sede)</label>
                        </div>
                        <p class="text-muted mt-2 mb-0" style="font-size: 0.8rem;">Define se esta empresa é a entidade principal do grupo. Só pode existir uma matriz ativa.</p>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3 mt-5 border-top pt-4">
                <a href="{{ route('admin.companies.index') }}" class="btn btn-cancel">Cancelar</a>
                <button type="submit" class="btn btn-save"><i class="fas fa-save me-2"></i> Atualizar Empresa</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('logo-input').addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('logo-preview');
                preview.querySelector('img').src = e.target.result;
                preview.classList.remove('d-none');
            }
            reader.readAsDataURL(e.target.files[0]);
        }
    });
</script>
@endpush
@endsection

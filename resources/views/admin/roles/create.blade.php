@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    .form-control {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border: 1px solid #cbd5e1;
    }
    .form-control:focus {
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
    }
    .btn-save:hover { color: white; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.4); }
    .btn-cancel {
        background: #f1f5f9;
        color: #475569;
        border-radius: 10px;
        padding: 0.6rem 2rem;
        font-weight: 600;
        border: none;
        transition: all 0.2s;
    }
    .btn-cancel:hover { background: #e2e8f0; color: #1e293b; }
    
    /* Permissions Grid Styling */
    .module-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        height: 100%;
        background: #f8fafc;
        transition: all 0.2s;
    }
    .module-card:hover { border-color: #cbd5e1; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
    .module-header {
        background: #f1f5f9;
        padding: 1rem;
        border-bottom: 1px solid #e2e8f0;
        border-radius: 12px 12px 0 0;
        font-weight: 600;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .module-body { padding: 1rem; }
    .permission-item {
        margin-bottom: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px dashed #e2e8f0;
    }
    .permission-item:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('admin.roles.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="fas fa-arrow-left me-1"></i> Voltar à Listagem
        </a>
        <h2 class="fw-bold mb-0 text-dark"><i class="fas fa-plus-circle text-primary me-2"></i>Novo Perfil</h2>
        <p class="text-muted mb-0">Crie um novo nível de acesso e configure as permissões em bloco.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm" style="border-radius: 10px;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-premium p-4 p-md-5">
        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf
            
            <div class="row mb-5">
                <div class="col-md-6">
                    <label class="form-label">Nome do Perfil <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-tag text-muted"></i></span>
                        <input type="text" name="name" class="form-control border-start-0" value="{{ old('name') }}" required placeholder="Ex: Gestor de RH">
                    </div>
                </div>
            </div>

            <h5 class="fw-bold text-dark border-bottom pb-2 mb-4">Matriz de Permissões</h5>
            
            @php
            $moduleTranslations = [
                'admin' => 'Administração Global',
                'users' => 'Utilizadores',
                'roles' => 'Perfis de Acesso',
                'companies' => 'Empresas',
                'logs' => 'Auditoria (Logs)',
                'sales' => 'Vendas & Faturação',
                'purchases' => 'Compras',
                'logistica' => 'Logística',
                'stock' => 'Armazém & Stock',
                'hr' => 'Recursos Humanos',
                'treasury' => 'Tesouraria',
                'accounting' => 'Contabilidade',
                'assets' => 'Ativos Fixos',
                'documents' => 'Gestão Documental',
                'entities' => 'Entidades / Contactos',
                'ai' => 'Recursos Avançados (IA)',
                'pos' => 'Ponto de Venda (POS)',
                'reports' => 'Relatórios',
                'settings' => 'Definições do Sistema'
            ];
            $actionTranslations = [
                'view' => 'Visualizar / Consultar',
                'create' => 'Criar / Adicionar',
                'edit' => 'Editar / Modificar',
                'delete' => 'Eliminar / Remover',
                'manage' => 'Gerir Todos os Registos',
                'approve' => 'Aprovar Documentos',
                'export' => 'Exportar Dados',
                'import' => 'Importar Dados',
                'print' => 'Imprimir',
                'dashboard' => 'Ver Dashboard',
            ];
            @endphp
            
            <div class="mb-3 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-sm btn-outline-primary" id="selectAll"><i class="fas fa-check-square me-1"></i> Selecionar Tudo</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAll"><i class="fas fa-square me-1"></i> Limpar Seleção</button>
            </div>

            <div class="row g-4">
                @foreach($permissions as $module => $modulePermissions)
                <div class="col-md-4 col-lg-3">
                    <div class="module-card">
                        <div class="module-header">
                            <span class="text-capitalize fw-bold"><i class="fas fa-cube text-primary me-2"></i>{{ $moduleTranslations[$module] ?? ucfirst($module) }}</span>
                            <div class="form-check m-0">
                                <input class="form-check-input module-checkbox" type="checkbox" data-module="{{ $module }}">
                            </div>
                        </div>
                        <div class="module-body">
                            @foreach($modulePermissions as $perm)
                            <div class="form-check permission-item">
                                <input class="form-check-input perm-checkbox module-{{ $module }}" type="checkbox" name="permissions[]" value="{{ $perm->name }}" id="perm_{{ $perm->id }}">
                                @php
                                    $actionKey = str_replace($module.'.', '', $perm->name);
                                    $actionName = $actionTranslations[$actionKey] ?? ucfirst($actionKey);
                                @endphp
                                <label class="form-check-label text-dark" for="perm_{{ $perm->id }}" style="font-size: 0.85rem;">
                                    {{ $actionName }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-end gap-3 mt-5 border-top pt-4">
                <a href="{{ route('admin.roles.index') }}" class="btn btn-cancel">Cancelar</a>
                <button type="submit" class="btn btn-save"><i class="fas fa-save me-2"></i> Guardar Perfil</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Selecionar/Deselecionar módulo inteiro
        document.querySelectorAll('.module-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const module = this.dataset.module;
                const perms = document.querySelectorAll('.module-' + module);
                perms.forEach(function(p) {
                    p.checked = checkbox.checked;
                });
            });
        });

        // Atualizar checkbox do módulo se as permissões internas mudarem
        document.querySelectorAll('.perm-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const moduleClass = Array.from(this.classList).find(c => c.startsWith('module-'));
                if (moduleClass) {
                    const moduleName = moduleClass.replace('module-', '');
                    const allPerms = document.querySelectorAll('.' + moduleClass);
                    const allChecked = Array.from(allPerms).every(p => p.checked);
                    const someChecked = Array.from(allPerms).some(p => p.checked);
                    
                    const moduleCheckbox = document.querySelector(`.module-checkbox[data-module="${moduleName}"]`);
                    if (moduleCheckbox) {
                        moduleCheckbox.checked = allChecked;
                        moduleCheckbox.indeterminate = someChecked && !allChecked;
                    }
                }
            });
        });

        // Botões Globais
        document.getElementById('selectAll').addEventListener('click', function() {
            document.querySelectorAll('input[type="checkbox"]').forEach(c => c.checked = true);
        });
        document.getElementById('deselectAll').addEventListener('click', function() {
            document.querySelectorAll('input[type="checkbox"]').forEach(c => c.checked = false);
        });
    });
</script>
@endpush
@endsection

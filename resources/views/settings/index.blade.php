@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h2 class="view-title mb-0"><i class="fas fa-cogs text-secondary"></i> Administração Global do Sistema</h2>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Gestão de utilizadores, permissões e importação/restauro de dados corporativos.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger"><i class="fas fa-times-circle me-2"></i>{{ session('error') }}</div>
    @endif

    <!-- Tabs Navigation -->
    <div class="tabs mb-4" style="display:flex; align-items:center; gap: 1rem;">
        <button class="tab-btn active bg-primary text-white border-primary rounded px-3 py-2" onclick="switchSettingsTab('import', this)">
            <i class="fas fa-file-excel"></i> Restauro e Importação
        </button>
        <button class="tab-btn border rounded px-3 py-2 text-secondary bg-light" onclick="switchSettingsTab('users', this)">
            <i class="fas fa-users-cog"></i> Controlo de Acessos
        </button>
        <button class="tab-btn border rounded px-3 py-2 text-secondary bg-light" onclick="switchSettingsTab('api', this)">
            <i class="fas fa-key"></i> Integrações e API
        </button>
        <button class="tab-btn border rounded px-3 py-2 text-secondary bg-light" onclick="switchSettingsTab('tables', this)">
            <i class="fas fa-table"></i> Tabelas Mestras
        </button>
        <button class="tab-btn border rounded px-3 py-2 text-secondary bg-light" onclick="switchSettingsTab('security', this)">
            <i class="fas fa-shield-alt"></i> Segurança da Conta
        </button>
    </div>

    <!-- TAB: Restauro e Importação -->
    <div id="tab-import" class="settings-tab">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold"><i class="fas fa-upload text-primary"></i> Restauro e Backup de Base de Dados</h5>
                    <p class="text-muted small">Faça download do template XML/Excel rigoroso ou crie um dump completo da BD.</p>
                </div>
                <form action="{{ route('admin.settings.backup') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning shadow-sm"><i class="fas fa-database"></i> Executar Backup BD (pg_dump)</button>
                </form>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="border p-4 rounded bg-light">
                            <h6>1. Terceiros (Clientes / Fornecedores)</h6>
                            <a href="{{ route('settings.template.download', 'third_parties') }}" class="btn btn-outline-success btn-sm mb-3">
                                <i class="fas fa-download"></i> Baixar Template Excel
                            </a>
                            <form action="{{ route('settings.import.upload') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="import_type" value="third_parties">
                                <div class="input-group">
                                    <input type="file" class="form-control" name="import_file" accept=".xlsx,.csv" required>
                                    <button class="btn btn-primary" type="submit"><i class="fas fa-upload"></i> Processar Upload</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="border p-4 rounded bg-light opacity-50">
                            <h6>2. Catálogo de Produtos (Brevemente)</h6>
                            <button class="btn btn-outline-secondary btn-sm mb-3" disabled><i class="fas fa-download"></i> Baixar Template Excel</button>
                            <div class="input-group">
                                <input type="file" class="form-control" disabled>
                                <button class="btn btn-secondary" disabled>Processar Upload</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB: Users -->
    <div id="tab-users" class="settings-tab" style="display: none;">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="fas fa-shield-alt text-success"></i> Gestão de Utilizadores</h5>
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Perfil / Role</th>
                            <th>Estado</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="badge bg-secondary">{{ $user->role->name ?? 'Sem Perfil' }}</span></td>
                            <td><span class="badge bg-success">Ativo</span></td>
                            <td><button class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></button></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center">Nenhum utilizador registado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB: Integrações e API -->
    <div id="tab-api" class="settings-tab" style="display: none;">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="fas fa-key text-warning"></i> Chaves de Integração (API / PowerBI)</h5>
                <p class="text-muted small">Emita <em>Personal Access Tokens</em> para permitir que sistemas externos extraiam dados do ERP de forma segura.</p>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Form Gerar Token -->
                    <div class="col-md-5 mb-4 border-end">
                        <form action="{{ route('settings.api.token.generate') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nome da Aplicação</label>
                                <input type="text" name="token_name" class="form-control" placeholder="ex: Dashboard PowerBI" required>
                                <small class="text-muted">A chave secreta será exibida apenas uma vez no topo do ecrã.</small>
                            </div>
                            <button type="submit" class="btn btn-warning w-100"><i class="fas fa-magic"></i> Gerar Nova Chave</button>
                        </form>
                    </div>

                    <!-- Lista de Tokens -->
                    <div class="col-md-7 mb-4">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Última Utilização</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tokens ?? [] as $token)
                                <tr>
                                    <td>{{ $token->name }}</td>
                                    <td>{{ $token->last_used_at ? $token->last_used_at->diffForHumans() : 'Nunca usado' }}</td>
                                    <td>
                                        <form action="{{ route('settings.api.token.revoke', $token->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tem a certeza? O sistema ligado perderá imediatamente o acesso.')"><i class="fas fa-trash"></i> Revogar</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted">Sem chaves ativas.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB: Tabelas Mestras -->
    <div id="tab-tables" class="settings-tab" style="display: none;">
        <div class="card shadow-sm border-0"><div class="card-body"><h5 class="text-muted">Gestão de Armazéns, Categorias e Taxas em breve...</h5></div></div>
    </div>

    <!-- TAB: Segurança da Conta -->
    <div id="tab-security" class="settings-tab" style="display: none;">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="fas fa-lock text-primary"></i> Alterar Palavra-passe</h5>
                <p class="text-muted small">Mantenha a sua conta segura substituindo a palavra-passe periodicamente.</p>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.password.update') }}" method="POST" style="max-width: 500px;">
                    @csrf
                    <div class="form-group mb-3">
                        <label>Palavra-passe Atual</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Nova Palavra-passe</label>
                        <input type="password" name="password" class="form-control" required minlength="8">
                    </div>
                    <div class="form-group mb-4">
                        <label>Confirmar Nova Palavra-passe</label>
                        <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Atualizar Palavra-passe</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function switchSettingsTab(tabId, btn) {
        document.querySelectorAll('.settings-tab').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.tab-btn').forEach(el => {
            el.className = 'tab-btn border rounded px-3 py-2 text-secondary bg-light';
        });
        
        document.getElementById('tab-' + tabId).style.display = 'block';
        btn.className = 'tab-btn active bg-primary text-white border-primary rounded px-3 py-2';
    }
</script>
@endpush
@endsection

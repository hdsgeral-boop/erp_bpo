@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

    <!-- Top Action & Title Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h2 class="fw-bold text-dark mb-0"><i class="fas fa-users-cog text-primary me-2"></i>Gerir Utilizadores</h2>
                @if($isSuperAdmin)
                    <span class="badge bg-danger px-3 py-2 fs-8 fw-semibold" style="border-radius: 8px;">
                        <i class="fas fa-crown me-1"></i> Visão Global Super Admin
                    </span>
                @else
                    <span class="badge bg-primary px-3 py-2 fs-8 fw-semibold" style="border-radius: 8px;">
                        <i class="fas fa-building me-1"></i> Gestão de Empresa
                    </span>
                @endif
            </div>
            <p class="text-muted mb-0 fs-8">
                @if($isSuperAdmin)
                    Hierarquia do sistema: Gerencie os utilizadores organizados por Empresa &gt; Colaboradores em todo o grupo.
                @else
                    Gestão de membros e permissões da equipa associada à sua empresa.
                @endif
            </p>
        </div>
        <div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary fw-bold shadow-sm px-4" style="border-radius: 10px;">
                <i class="fas fa-user-plus me-1"></i> Novo Utilizador
            </a>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Pesquisar por nome ou e-mail..." value="{{ $search }}">
                    </div>
                </div>

                @if($isSuperAdmin && isset($allCompanies))
                    <div class="col-md-4">
                        <select name="company_id" class="form-select border-1" onchange="this.form.submit()">
                            <option value="">Todas as Empresas (Visão Global)</option>
                            @foreach($allCompanies as $comp)
                                <option value="{{ $comp->id }}" {{ $companyFilter == $comp->id ? 'selected' : '' }}>
                                    {{ $comp->name }} ({{ $comp->users_count ?? $comp->users->count() }} utilizadores)
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-dark fw-bold w-100" style="border-radius: 8px;">
                        <i class="fas fa-filter me-1"></i> Filtrar
                    </button>
                    @if($search || $companyFilter)
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary" title="Limpar Filtros" style="border-radius: 8px;">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if($isSuperAdmin)
        <!-- VISTA HIERÁRQUICA SUPER ADMIN: EMPRESA >> UTILIZADORES -->
        @if(isset($companies) && $companies->count() > 0)
            @foreach($companies as $company)
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; overflow: hidden;">
                    <div class="card-header bg-dark text-white p-3 d-flex justify-content-between align-items-center border-0">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center fw-bold" style="width: 42px; height: 42px; font-size: 1rem;">
                                {{ strtoupper(substr($company->name, 0, 2)) }}
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-white">{{ $company->name }}</h5>
                                <span class="fs-8 text-light opacity-75">NIF: {{ $company->nif ?? 'N/A' }} • {{ $company->users->count() }} Utilizador(es) Registados</span>
                            </div>
                        </div>
                        <a href="{{ route('admin.users.create') }}?company_id={{ $company->id }}" class="btn btn-sm btn-outline-light fw-bold" style="border-radius: 8px;">
                            <i class="fas fa-plus me-1"></i> Adicionar Utilizador nesta Empresa
                        </a>
                    </div>

                    <div class="card-body p-0">
                        @if($company->users->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light fs-8 text-uppercase">
                                        <tr>
                                            <th class="ps-4">Utilizador</th>
                                            <th>Contacto / E-mail</th>
                                            <th>Cargo / Dept.</th>
                                            <th>Perfil de Acesso</th>
                                            <th>Data Registo</th>
                                            <th class="text-end pe-4">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($company->users as $u)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="user-avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 0.85rem;">
                                                            {{ strtoupper(substr($u->name, 0, 2)) }}
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold text-dark fs-7">{{ $u->name }}</div>
                                                            @if($u->id === auth()->id())
                                                                <span class="badge bg-success-light text-success fs-8">Você</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="fs-8">
                                                    <div class="text-dark fw-semibold">{{ $u->email }}</div>
                                                    <div class="text-muted">{{ $u->phone ?? 'Sem telefone' }}</div>
                                                </td>
                                                <td class="fs-8">
                                                    <div class="fw-semibold text-dark">{{ $u->job_title ?? 'Colaborador' }}</div>
                                                    <div class="text-muted">{{ $u->department ?? 'Geral' }}</div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary px-2 py-1 fs-8">
                                                        {{ $u->roles->first()?->name ?? 'Sem Perfil' }}
                                                    </span>
                                                </td>
                                                <td class="fs-8 text-muted">
                                                    {{ $u->created_at ? $u->created_at->format('d/m/Y') : 'N/A' }}
                                                </td>
                                                <td class="text-end pe-4">
                                                    <div class="btn-group">
                                                        <a href="{{ route('admin.users.edit', $u->id) }}" class="btn btn-sm btn-light border text-primary" title="Editar Utilizador">
                                                            <i class="fas fa-edit"></i> Editar
                                                        </a>
                                                        @if($u->id !== auth()->id())
                                                            <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja eliminar este utilizador?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-light border text-danger" title="Eliminar Utilizador">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4 text-muted fs-8">
                                <i class="fas fa-user-slash fa-2x mb-2 text-secondary opacity-50"></i>
                                <p class="mb-0">Nenhum utilizador associado a esta empresa.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif

        @if(isset($unassignedUsers) && $unassignedUsers->count() > 0)
            <!-- UTILIZADORES SEM EMPRESA FIXA -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                <div class="card-header bg-secondary text-white p-3 border-0">
                    <h5 class="fw-bold mb-0 text-white"><i class="fas fa-user-shield me-2"></i>Utilizadores Gerais / Super Administradores</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light fs-8 text-uppercase">
                                <tr>
                                    <th class="ps-4">Utilizador</th>
                                    <th>E-mail</th>
                                    <th>Perfil de Acesso</th>
                                    <th class="text-end pe-4">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($unassignedUsers as $u)
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark">{{ $u->name }}</td>
                                        <td class="fs-8 text-dark">{{ $u->email }}</td>
                                        <td>
                                            <span class="badge bg-danger">{{ $u->roles->first()?->name ?? 'Super Admin' }}</span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('admin.users.edit', $u->id) }}" class="btn btn-sm btn-light border text-primary">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

    @else
        <!-- VISTA GESTOR/ADMIN DA EMPRESA: EQUIPA DA SUA EMPRESA -->
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-building text-primary me-2"></i>Equipa de {{ $myCompany->name ?? 'Sua Empresa' }}</h5>
                    <p class="text-muted fs-8 mb-0">Lista de utilizadores com acesso ativo ao ERP nesta empresa.</p>
                </div>
                <span class="badge bg-primary px-3 py-2 fs-7">{{ $users->total() }} Membros</span>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light fs-8 text-uppercase">
                            <tr>
                                <th class="ps-4">Nome do Utilizador</th>
                                <th>Contacto / E-mail</th>
                                <th>Cargo / Função</th>
                                <th>Perfil no ERP</th>
                                <th class="text-end pe-4">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $u)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="user-avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 0.85rem;">
                                                {{ strtoupper(substr($u->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark fs-7">{{ $u->name }}</div>
                                                @if($u->id === auth()->id())
                                                    <span class="badge bg-success-light text-success fs-8">Você</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fs-8">
                                        <div class="text-dark fw-semibold">{{ $u->email }}</div>
                                        <div class="text-muted">{{ $u->phone ?? 'Sem telefone' }}</div>
                                    </td>
                                    <td class="fs-8">
                                        <div class="fw-semibold text-dark">{{ $u->job_title ?? 'Colaborador' }}</div>
                                        <div class="text-muted">{{ $u->department ?? 'Geral' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary px-2 py-1 fs-8">
                                            {{ $u->roles->first()?->name ?? 'Utilizador' }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.users.edit', $u->id) }}" class="btn btn-sm btn-light border text-primary" title="Editar Utilizador">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            @if($u->id !== auth()->id())
                                                <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja eliminar este utilizador?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-light border text-danger" title="Eliminar Utilizador">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-users-slash fa-3x mb-3 text-secondary opacity-50"></i>
                                        <h5>Nenhum utilizador encontrado</h5>
                                        <p class="fs-8 mb-0">Tente ajustar a sua pesquisa ou adicione novos colaboradores.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($users->hasPages())
                <div class="card-footer bg-white border-top-0 p-3">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection

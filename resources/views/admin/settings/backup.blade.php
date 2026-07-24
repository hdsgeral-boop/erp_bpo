@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="margin: 0; font-weight: 800; color: #0f172a; font-size: 1.6rem; letter-spacing: -0.5px;">
                <i class="fas fa-database text-primary me-2"></i> Gestão de Backups do PostgreSQL
            </h2>
            <p style="margin: 0; color: #64748b; font-size: 0.925rem;">
                Cópia de segurança e salvaguarda integral dos dados do ERP Consulvolt.
            </p>
        </div>
        <div>
            <form action="{{ route('admin.settings.backup') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-primary fw-bold" style="padding: 0.65rem 1.4rem; background: #2563eb; color: #fff; border: none; border-radius: 10px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-database"></i> Gerar Novo Backup Agora
                </button>
            </form>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; background: #dcfce7; color: #15803d;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; background: #fef2f2; color: #991b1b;">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Cards Stats -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <span style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Motor de BD</span>
                    <h3 style="margin: 0.25rem 0 0; font-weight: 800; color: #0f172a;">PostgreSQL</h3>
                </div>
                <div style="width: 50px; height: 50px; background: #eff6ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #2563eb; font-size: 1.4rem;">
                    <i class="fas fa-server"></i>
                </div>
            </div>
        </div>

        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <span style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Total de Tabelas</span>
                    <h3 style="margin: 0.25rem 0 0; font-weight: 800; color: #0f172a;">{{ $tablesCount ?? 90 }} Tabelas</h3>
                </div>
                <div style="width: 50px; height: 50px; background: #f0fdf4; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #16a34a; font-size: 1.4rem;">
                    <i class="fas fa-table"></i>
                </div>
            </div>
        </div>

        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <span style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Estado da Segurança</span>
                    <h3 style="margin: 0.25rem 0 0; font-weight: 800; color: #16a34a;">Protegido</h3>
                </div>
                <div style="width: 50px; height: 50px; background: #fefce8; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #ca8a04; font-size: 1.4rem;">
                    <i class="fas fa-shield-alt"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Backups -->
    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem;" class="shadow-sm">
        <h4 style="margin: 0 0 1rem; font-weight: 800; color: #0f172a;">Histórico de Cópias de Segurança</h4>
        
        @if(empty($backups))
            <div class="text-center py-5">
                <i class="fas fa-database text-muted mb-3" style="font-size: 3rem; opacity: 0.4;"></i>
                <p class="text-muted font-semibold mb-0">Nenhum ficheiro de backup gerado ainda. Clique em "Gerar Novo Backup Agora" para criar uma cópia.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light fs-8 text-uppercase">
                        <tr>
                            <th>Ficheiro de Backup</th>
                            <th>Tamanho</th>
                            <th>Data de Criação</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($backups as $backup)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark font-monospace fs-7">
                                        <i class="fas {{ str_contains($backup['name'], '.gz') ? 'fa-file-archive text-warning' : 'fa-file-code text-primary' }} me-2"></i>
                                        {{ $backup['name'] }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border fs-8">{{ $backup['size_formatted'] }}</span>
                                </td>
                                <td class="fs-8 text-muted">
                                    {{ $backup['created_at'] }}
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a href="{{ route('admin.backups.download', $backup['name']) }}" class="btn btn-sm btn-outline-primary fw-bold" style="border-radius: 8px;">
                                            <i class="fas fa-download me-1"></i> Descarregar
                                        </a>

                                        <form action="{{ route('admin.backups.delete', $backup['name']) }}" method="POST" style="display: inline;" onsubmit="return confirm('Eliminar definitivamente este ficheiro de backup?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger fw-bold" style="border-radius: 8px;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

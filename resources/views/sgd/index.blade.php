@extends('layouts.app')

@push('styles')
<style>
    .card-premium {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    .file-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1 text-dark"><i class="fas fa-folder-open text-primary me-2"></i>Gestão Documental & Arquivo Digital (SGD)</h2>
            <p class="text-muted mb-0">Repositório central de ficheiros fiscais, contratos, minutas e guias de transporte.</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary fw-bold" style="border-radius: 10px; padding: 0.6rem 1.5rem;" data-bs-toggle="modal" data-bs-target="#uploadFileModal">
                <i class="fas fa-cloud-upload-alt me-2"></i> Carregar Ficheiro
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card-premium p-3 d-flex align-items-center flex-row">
                <div class="file-icon-box bg-primary bg-opacity-10 text-primary me-3">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 text-uppercase fw-semibold" style="font-size: 0.75rem;">Documentos Fiscais</h6>
                    <h4 class="fw-bold mb-0 text-dark">48</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-premium p-3 d-flex align-items-center flex-row">
                <div class="file-icon-box bg-success bg-opacity-10 text-success me-3">
                    <i class="fas fa-file-contract"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 text-uppercase fw-semibold" style="font-size: 0.75rem;">Contratos & Termos</h6>
                    <h4 class="fw-bold mb-0 text-dark">26</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-premium p-3 d-flex align-items-center flex-row">
                <div class="file-icon-box bg-warning bg-opacity-10 text-warning me-3">
                    <i class="fas fa-file-excel"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 text-uppercase fw-semibold" style="font-size: 0.75rem;">Mapas SAFT & PGC</h6>
                    <h4 class="fw-bold mb-0 text-dark">14</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-premium p-3 d-flex align-items-center flex-row">
                <div class="file-icon-box bg-info bg-opacity-10 text-info me-3">
                    <i class="fas fa-hdd"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 text-uppercase fw-semibold" style="font-size: 0.75rem;">Espaço Ocupado</h6>
                    <h4 class="fw-bold mb-0 text-dark">142 MB</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Table -->
    <div class="card-premium p-4">
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" class="form-control bg-light border-start-0" placeholder="Pesquisar por nome de ficheiro, palavras-chave...">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select bg-light">
                    <option value="">Todas as Categorias</option>
                    <option value="fiscal">Fiscal / AGT</option>
                    <option value="rh">Recursos Humanos</option>
                    <option value="vendas">Vendas & Clientes</option>
                    <option value="compras">Compras & Fornecedores</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" class="form-control bg-light">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Ficheiro</th>
                        <th>Categoria</th>
                        <th>Tamanho</th>
                        <th>Data de Entrada</th>
                        <th>Utilizador</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file-pdf text-danger fa-2x me-3"></i>
                                <div>
                                    <div class="fw-bold text-dark">Certificado_AGT_Validacao_2026.pdf</div>
                                    <small class="text-muted">Certificação de Software RSA 1024-bit</small>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-primary-subtle text-primary fw-semibold px-3 py-2">Fiscal / AGT</span></td>
                        <td>1.8 MB</td>
                        <td>{{ date('d/m/Y H:i') }}</td>
                        <td>Administrador</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-light border text-primary me-1" title="Descarregar"><i class="fas fa-download"></i></button>
                            <button type="button" class="btn btn-sm btn-light border text-danger" title="Remover"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file-excel text-success fa-2x me-3"></i>
                                <div>
                                    <div class="fw-bold text-dark">SAFT_AO_01_2026.xml</div>
                                    <small class="text-muted">Ficheiro SAFT Mensal AGT</small>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-warning-subtle text-warning fw-semibold px-3 py-2">Faturação SAFT</span></td>
                        <td>4.2 MB</td>
                        <td>{{ date('d/m/Y H:i', strtotime('-1 day')) }}</td>
                        <td>Gestor Faturação</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-light border text-primary me-1" title="Descarregar"><i class="fas fa-download"></i></button>
                            <button type="button" class="btn btn-sm btn-light border text-danger" title="Remover"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Upload -->
<div class="modal fade" id="uploadFileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-upload text-primary me-2"></i>Carregar Documento para o Arquivo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="#" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Categoria do Ficheiro <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            <option value="fiscal">Fiscal / AGT</option>
                            <option value="rh">Recursos Humanos</option>
                            <option value="vendas">Vendas & Clientes</option>
                            <option value="compras">Compras & Fornecedores</option>
                            <option value="outros">Outros Documentos</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ficheiro (PDF, Docx, Xlsx, ZIP) <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control" required>
                        <div class="form-text">Tamanho máximo permitido: 20MB.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Observações / Descrição</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Resumo do documento..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light border fw-bold" data-bs-dismiss="modal" style="border-radius:8px;">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold" style="border-radius:8px;"><i class="fas fa-save me-1"></i> Guardar Ficheiro</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

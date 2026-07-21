@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h2 class="view-title mb-0"><i class="fas fa-archive"></i> Gestão Documental (SGD)</h2>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Organização, classificação e arquivo digital de documentos da empresa.</p>
        </div>
        <div>
            <button class="btn btn-primary" onclick="showUploadModal()">
                <i class="fas fa-upload"></i> Carregar Documento
            </button>
        </div>
    </div>
    
    <!-- Tabs Navigation -->
    <div class="tabs mb-4" style="display:flex; align-items:center; gap: 1rem;">
        <button class="tab-btn active text-primary bg-primary bg-opacity-10 border-primary" onclick="switchTab('arquivo', this)"><i class="fas fa-folder-open"></i> Arquivo Digital</button>
        <button class="tab-btn text-success bg-success bg-opacity-10 border-success" onclick="switchTab('classificador', this)"><i class="fas fa-tags"></i> Classificador de Documentos</button>
    </div>

    <!-- Feedback Container -->
    <div id="ajax-feedback" style="display: none;" class="alert"></div>

    <!-- Tab Content: Arquivo Digital -->
    <div id="tab-arquivo" class="tab-content active">
        <!-- Stats -->
        <div class="card mb-4 bg-light border-0">
            <div class="card-body row text-center">
                <div class="col-md-3">
                    <span class="d-block fw-bold text-muted text-uppercase" style="font-size:0.7rem;">Total de Documentos</span>
                    <span class="fw-bolder text-primary" style="font-size:1.5rem;">{{ $documents->count() }}</span>
                </div>
            </div>
        </div>

        <!-- Document Grid -->
        <div class="row">
            @forelse($documents as $doc)
            <div class="col-md-3 mb-4" id="doc-card-{{ $doc->id }}">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="fas {{ str_contains($doc->mime_type, 'pdf') ? 'fa-file-pdf text-danger' : 'fa-file-image text-primary' }} fa-3x mb-3"></i>
                        <h6 class="card-title text-truncate" title="{{ $doc->original_name }}">{{ $doc->original_name }}</h6>
                        <p class="card-text text-muted small mb-1">{{ $doc->type->name ?? 'Sem Categoria' }}</p>
                        <p class="card-text text-muted" style="font-size: 0.7rem;">
                            {{ number_format($doc->size / 1024, 2) }} KB <br>
                            Status OCR: <span class="badge bg-{{ $doc->status == 'processed' ? 'success' : 'warning' }}">{{ $doc->status }}</span>
                        </p>
                    </div>
                    <div class="card-footer bg-white border-top-0 d-flex justify-content-between">
                        <button class="btn btn-sm btn-outline-primary" onclick="viewDocument('{{ Storage::url($doc->file_path) }}', '{{ $doc->mime_type }}')"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteDocument({{ $doc->id }})"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted py-5">
                <i class="fas fa-folder-open fa-3x mb-3"></i>
                <p>Nenhum documento arquivado.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Tab Content: Classificador -->
    <div id="tab-classificador" class="tab-content" style="display: none;">
        <div class="card">
            <div class="card-body">
                <h5 class="text-muted">Gestão de Tipos de Documentos em breve...</h5>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload -->
<div class="modal" id="uploadModal" tabindex="-1" style="display: none; background: rgba(0,0,0,0.5);">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload de Documento</h5>
                <button type="button" class="btn-close" onclick="closeUploadModal()"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" onsubmit="submitUploadForm(event)">
                    @csrf
                    <!-- Dummy Fields since we don't have all tables fully seeded in view -->
                    <input type="hidden" name="documentable_type" value="App\Models\Company">
                    <input type="hidden" name="documentable_id" value="1">
                    
                    <div class="mb-3">
                        <label>Categoria do Documento</label>
                        <select class="form-select" name="document_type_id" required>
                            @foreach(\App\Models\DocumentType::all() as $type)
                                <option value="{{ $type->id }}">{{ $type->name }} ({{ $type->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label>Ficheiro (PDF, JPG, PNG)</label>
                        <input type="file" class="form-control" name="file" accept=".pdf, .jpg, .jpeg, .png" required>
                    </div>
                    
                    <div class="modal-footer px-0 pb-0">
                        <button type="button" class="btn btn-secondary" onclick="closeUploadModal()">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="uploadBtn">Carregar (AJAX)</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Viewer -->
<div class="modal" id="viewerModal" tabindex="-1" style="display: none; background: rgba(0,0,0,0.8);">
    <div class="modal-dialog modal-xl" style="height: 90vh;">
        <div class="modal-content h-100">
            <div class="modal-header">
                <h5 class="modal-title">Visualizador de Documentos</h5>
                <button type="button" class="btn-close" onclick="closeViewerModal()"></button>
            </div>
            <div class="modal-body p-0" id="viewerContent" style="background:#e2e8f0;">
                <!-- Iframe or Img injected here -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<style>
    .tab-btn { padding: 0.5rem 1rem; border: 1px solid #e2e8f0; background: #f8fafc; cursor: pointer; border-radius: 4px; color: #475569; font-weight: 600;}
    .tab-btn.active { border-bottom-width: 2px;}
</style>
<script>
    function switchTab(tabId, btn) {
        document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.tab-btn').forEach(el => {
            el.classList.remove('active');
            el.style.borderBottomWidth = '1px';
        });
        document.getElementById('tab-' + tabId).style.display = 'block';
        btn.classList.add('active');
    }

    function showUploadModal() {
        document.getElementById('uploadForm').reset();
        document.getElementById('uploadModal').style.display = 'block';
    }

    function closeUploadModal() {
        document.getElementById('uploadModal').style.display = 'none';
    }

    async function submitUploadForm(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const btn = document.getElementById('uploadBtn');
        btn.disabled = true;
        btn.innerText = 'A processar OCR...';
        
        try {
            const response = await fetch('/documents', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();
            
            if (response.ok && data.success) {
                closeUploadModal();
                showFeedback(data.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showFeedback(data.message || 'Erro de validação do ficheiro', 'danger');
            }
        } catch (error) {
            showFeedback('Erro na ligação ao servidor.', 'danger');
        } finally {
            btn.disabled = false;
            btn.innerText = 'Carregar (AJAX)';
        }
    }

    async function deleteDocument(id) {
        if (!confirm('Deseja eliminar permanentemente este documento?')) return;

        try {
            const response = await fetch(`/documents/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            if (response.ok && data.success) {
                document.getElementById('doc-card-' + id).remove();
                showFeedback(data.message, 'success');
            } else {
                showFeedback(data.message, 'danger');
            }
        } catch (error) {
            showFeedback('Erro ao eliminar.', 'danger');
        }
    }

    function viewDocument(url, mime) {
        const viewer = document.getElementById('viewerContent');
        if (mime.includes('image')) {
            viewer.innerHTML = `<div class="d-flex justify-content-center align-items-center h-100"><img src="${url}" style="max-height:100%; max-width:100%;"></div>`;
        } else {
            viewer.innerHTML = `<iframe src="${url}" width="100%" height="100%" frameborder="0"></iframe>`;
        }
        document.getElementById('viewerModal').style.display = 'block';
    }

    function closeViewerModal() {
        document.getElementById('viewerModal').style.display = 'none';
        document.getElementById('viewerContent').innerHTML = '';
    }

    function showFeedback(msg, type) {
        const box = document.getElementById('ajax-feedback');
        box.className = `alert alert-${type}`;
        box.innerText = msg;
        box.style.display = 'block';
        setTimeout(() => box.style.display = 'none', 3000);
    }
</script>
@endpush
@endsection

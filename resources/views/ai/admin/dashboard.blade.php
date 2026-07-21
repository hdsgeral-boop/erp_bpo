@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h2 class="view-title mb-0"><i class="fas fa-brain text-primary"></i> Plataforma de Inteligência Artificial</h2>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Monitorização Global, Gestão de Agentes e Ferramentas.</p>
        </div>
    </div>

    <!-- KPIs -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-primary border-4 h-100">
                <div class="card-body">
                    <p class="text-muted mb-1 text-uppercase small fw-bold">Total Conversas</p>
                    <h3 class="mb-0 fw-bold">{{ $totalConversations }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-success border-4 h-100">
                <div class="card-body">
                    <p class="text-muted mb-1 text-uppercase small fw-bold">Mensagens (Queries)</p>
                    <h3 class="mb-0 fw-bold">{{ $totalMessages }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-warning border-4 h-100">
                <div class="card-body">
                    <p class="text-muted mb-1 text-uppercase small fw-bold">Tokens Consumidos</p>
                    <h3 class="mb-0 fw-bold">{{ number_format($totalTokens, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-danger border-4 h-100">
                <div class="card-body">
                    <p class="text-muted mb-1 text-uppercase small fw-bold">Custo Estimado ($)</p>
                    <h3 class="mb-0 fw-bold">${{ number_format($totalCost, 4) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Estrutura Principal -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="fw-bold"><i class="fas fa-robot text-primary"></i> Infraestrutura Preparada</h5>
                </div>
                <div class="card-body">
                    <p>A arquitetura AI Platform foi implementada com sucesso e está preparada para:</p>
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> <strong>Multi-Agent System:</strong> {{ $agentsCount }} Agentes ativos no sistema.</li>
                        <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> <strong>Tool Calling Seguro:</strong> 15 Tools stubadas (Sales e Stock fully implemented).</li>
                        <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> <strong>Providers Abstraídos:</strong> OpenAI e DeepSeek implementados nativamente.</li>
                        <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i> <strong>Gestão de Memória:</strong> Base de dados guarda conversas por Empresa/Utilizador.</li>
                        <li class="list-group-item"><i class="fas fa-cogs text-warning me-2"></i> <strong>RAG & Embeddings:</strong> Tabelas `ai_knowledge_bases` prontas para vetores.</li>
                        <li class="list-group-item"><i class="fas fa-network-wired text-warning me-2"></i> <strong>MCP Protocol:</strong> Arquitetura de interfaces preparada para servidores externos.</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold border-bottom pb-2 mb-3">Links de Gestão</h5>
                    <div class="d-grid gap-2">
                        <a href="{{ route('ai.admin.agents') }}" class="btn btn-outline-primary text-start"><i class="fas fa-users-cog me-2"></i> Gestão de Agentes</a>
                        <a href="{{ route('ai.admin.providers') }}" class="btn btn-outline-primary text-start"><i class="fas fa-server me-2"></i> Chaves e Providers</a>
                        <button class="btn btn-outline-secondary text-start" disabled><i class="fas fa-history me-2"></i> Histórico Global</button>
                        <button class="btn btn-outline-secondary text-start" disabled><i class="fas fa-database me-2"></i> RAG Knowledge Base</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

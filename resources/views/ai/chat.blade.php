@extends('layouts.app')

@push('styles')
<style>
    .chat-container { height: calc(100vh - 220px); display: flex; flex-direction: column; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; }
    .chat-history { flex: 1; overflow-y: auto; padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem; }
    .chat-message { max-width: 75%; padding: 1rem 1.25rem; border-radius: 16px; position: relative; font-size: 0.95rem; line-height: 1.5; }
    
    .chat-message.user { align-self: flex-end; background: #4f46e5; color: white; border-bottom-right-radius: 4px; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2); }
    .chat-message.agent { align-self: flex-start; background: white; border: 1px solid #e2e8f0; color: #1e293b; border-bottom-left-radius: 4px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
    
    .chat-input-area { background: white; border-top: 1px solid #e2e8f0; padding: 1rem 1.5rem; }
    .typing-indicator { display: none; padding: 1rem; color: #64748b; font-size: 0.85rem; font-style: italic; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                <i class="fas fa-robot fa-lg"></i>
            </div>
            <div>
                <h2 class="view-title mb-0">Assistente IA</h2>
                <p class="text-muted mb-0" style="font-size: 0.85rem;">Consulte dados do ERP usando linguagem natural.</p>
            </div>
        </div>
        <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="fas fa-circle me-1" style="font-size:0.5rem"></i> Online</span>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="chat-container shadow-sm">
                <div class="chat-history" id="chat-history">
                    <div class="chat-message agent">
                        <strong><i class="fas fa-robot text-primary"></i> Assistente Consulvolt</strong><br>
                        Olá! Sou o teu Assistente Virtual ERP. Estou ligado aos módulos de Faturação, Activos e Logística.<br>Como te posso ajudar hoje?
                    </div>
                </div>
                
                <div class="typing-indicator" id="typing-indicator">
                    <i class="fas fa-circle-notch fa-spin me-2"></i> O assistente está a analisar a Base de Dados...
                </div>

                <div class="chat-input-area">
                    <form onsubmit="sendMessage(event)" class="d-flex gap-2">
                        @csrf
                        <input type="text" id="user-input" class="form-control form-control-lg border-0 bg-light" placeholder="Pergunta-me sobre faturas em atraso, ou o valor dos ativos..." required autocomplete="off">
                        <button type="submit" class="btn btn-primary btn-lg px-4 rounded-3 shadow-sm" id="send-btn">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    async function sendMessage(e) {
        e.preventDefault();
        
        const inputField = document.getElementById('user-input');
        const message = inputField.value.trim();
        if (!message) return;

        // 1. Mostrar mensagem do utilizador
        appendMessage(message, 'user');
        inputField.value = '';
        
        // 2. Estado de Carregamento
        const btn = document.getElementById('send-btn');
        const loader = document.getElementById('typing-indicator');
        btn.disabled = true;
        loader.style.display = 'block';
        scrollToBottom();

        // 3. Comunicação AJAX com AiAgentController
        try {
            const formData = new FormData();
            formData.append('message', message);
            
            const response = await fetch('{{ route("ai.process") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            if (!response.ok) throw new Error('Falha na API');
            
            const data = await response.json();
            
            if (data.success) {
                appendMessage(data.data.reply, 'agent');
            } else {
                appendMessage('Ocorreu um erro a processar a tua resposta.', 'agent');
            }
        } catch (error) {
            appendMessage('Falha de ligação ao servidor cognitivo.', 'agent');
        } finally {
            btn.disabled = false;
            loader.style.display = 'none';
            inputField.focus();
            scrollToBottom();
        }
    }

    function appendMessage(text, sender) {
        const history = document.getElementById('chat-history');
        const msgDiv = document.createElement('div');
        msgDiv.className = `chat-message ${sender} slide-up`;
        
        if (sender === 'agent') {
            msgDiv.innerHTML = `<strong><i class="fas fa-robot text-primary"></i> Assistente Consulvolt</strong><br>${text}`;
        } else {
            msgDiv.innerHTML = text;
        }
        
        history.appendChild(msgDiv);
        scrollToBottom();
    }

    function scrollToBottom() {
        const history = document.getElementById('chat-history');
        history.scrollTop = history.scrollHeight;
    }
</script>
@endpush
@endsection

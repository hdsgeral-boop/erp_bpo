<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Models\AiAgent;
use App\Models\AiConversation;
use App\Services\AI\AIService;
use Illuminate\Support\Facades\Log;

class AiAgentController extends Controller
{
    use ApiResponse;

    /**
     * Retorna a interface gráfica (Chat)
     */
    public function chat()
    {
        return view('ai.chat');
    }

    /**
     * Processa a mensagem do utilizador via AJAX
     */
    public function process(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000'
        ]);

        $userMessage = $request->input('message');
        
        try {
            // No futuro, o agente ativo virá da sessão ou configuração da empresa.
            // Para já, vamos carregar o primeiro agente ativo.
            $agent = AiAgent::where('is_active', true)->first();

            if (!$agent) {
                return $this->errorResponse('Nenhum Assistente IA ativo configurado no momento.');
            }

            // Gerir a Sessão de Conversa (Criar ou Recuperar)
            // Assumimos que o utilizador atual tem uma conversa ativa com este agente
            $conversation = AiConversation::firstOrCreate(
                [
                    'user_id' => auth()->id() ?? 1, // Fallback para dev local
                    'ai_agent_id' => $agent->id,
                    'company_id' => $agent->company_id ?? 1,
                ],
                [
                    'title' => 'Conversa de Suporte',
                ]
            );

            // Instanciar o core Service
            $aiService = new AIService($agent);

            // Processar a mensagem (Isto aciona o Provider e as Tools automaticamente)
            $reply = $aiService->processMessage($conversation, $userMessage);

            return $this->successResponse('Resposta gerada', ['reply' => $reply]);

        } catch (\Exception $e) {
            Log::error("Erro no AiAgentController", ['error' => $e->getMessage()]);
            return $this->errorResponse('Erro ao processar o pedido: ' . $e->getMessage());
        }
    }
}

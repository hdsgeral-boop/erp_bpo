<?php

namespace App\Services\AI;

use App\Models\AiAgent;
use App\Models\AiConversation;
use App\Models\AiMessage;
use Illuminate\Support\Facades\Log;
use Exception;

class AIService
{
    /**
     * @var \App\Services\AI\Contracts\ToolInterface[]
     */
    protected array $registeredTools = [];
    protected AiAgent $agent;

    public function __construct(AiAgent $agent)
    {
        $this->agent = $agent;
        $this->loadTools();
    }

    protected function loadTools()
    {
        // Fetch tools assigned to this agent from the database
        foreach ($this->agent->tools as $agentTool) {
            $class = $agentTool->tool_class;
            if (class_exists($class)) {
                $this->registeredTools[$class] = app($class);
            }
        }
    }

    public function processMessage(AiConversation $conversation, string $userMessage): string
    {
        // 1. Record User Message
        AiMessage::create([
            'ai_conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $userMessage,
        ]);

        // 2. Executar via Router com suporte a Fallback Automático
        return ProviderManager::executeWithFallback($this->agent, function(AIProviderInterface $provider) use ($conversation) {
            
            // 3. Fetch Conversation History (re-fetched on each attempt to avoid stale state)
            $history = $this->buildMessageHistory($conversation);

            // 4. Send to Provider (Supports 1 loop of tool calls)
            $response = $provider->chat(
                $history, 
                array_values($this->registeredTools), 
                $this->agent->temperature
            );

            // 5. Handle Tool Calls se existirem e se o modelo suportar
            if (!empty($response['tool_calls']) && $this->agent->aiModel->supports_tool_calling) {
                // Save Assistant tool call message
                $assistantMsg = AiMessage::create([
                    'ai_conversation_id' => $conversation->id,
                    'role' => 'assistant',
                    'content' => $response['content'],
                    'tool_calls' => $response['tool_calls'],
                    'tokens_used' => $response['tokens_used'] ?? 0,
                ]);

                // Execute Tools
                foreach ($response['tool_calls'] as $call) {
                    $toolResult = $this->executeToolCall($call);
                    
                    AiMessage::create([
                        'ai_conversation_id' => $conversation->id,
                        'role' => 'tool',
                        'content' => json_encode($toolResult),
                        'tool_call_id' => $call['id'] ?? null,
                    ]);
                }

                // Re-fetch history and call Provider again to get final answer
                $history = $this->buildMessageHistory($conversation);
                $response = $provider->chat(
                    $history, 
                    [], // Sem ferramentas no second pass
                    $this->agent->temperature
                );
            }

            // 6. Save Final Assistant Message
            AiMessage::create([
                'ai_conversation_id' => $conversation->id,
                'role' => 'assistant',
                'content' => $response['content'],
                'tokens_used' => $response['tokens_used'] ?? 0,
            ]);

            return $response['content'] ?? 'Desculpe, ocorreu um erro na geração da resposta.';
        });
    }

    protected function buildMessageHistory(AiConversation $conversation): array
    {
        $messages = [];
        
        // System Prompt
        $messages[] = [
            'role' => 'system',
            'content' => $this->agent->system_prompt
        ];

        // Last 10 messages for context window
        $dbMessages = $conversation->messages()->orderBy('id', 'asc')->take(10)->get();

        foreach ($dbMessages as $msg) {
            $m = [
                'role' => $msg->role,
                'content' => $msg->content,
            ];
            if ($msg->tool_calls) {
                $m['tool_calls'] = $msg->tool_calls;
            }
            if ($msg->tool_call_id) {
                $m['tool_call_id'] = $msg->tool_call_id;
            }
            $messages[] = $m;
        }

        return $messages;
    }

    protected function executeToolCall(array $call): mixed
    {
        $functionName = $call['function']['name'] ?? '';
        $arguments = json_decode($call['function']['arguments'] ?? '{}', true);

        // Find the tool by name
        foreach ($this->registeredTools as $tool) {
            if ($tool->getName() === $functionName) {
                try {
                    return $tool->execute($arguments);
                } catch (Exception $e) {
                    Log::error("Tool execution failed: {$functionName}", ['error' => $e->getMessage()]);
                    return ['error' => "Falha ao executar ferramenta: " . $e->getMessage()];
                }
            }
        }

        return ['error' => "Ferramenta {$functionName} não encontrada ou não autorizada."];
    }
}

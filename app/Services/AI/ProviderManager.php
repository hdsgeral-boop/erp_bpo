<?php

namespace App\Services\AI;

use App\Models\AiAgent;
use App\Models\AiProvider;
use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\AI\Providers\OpenAIProvider;
use App\Services\AI\Providers\DeepSeekProvider;
use Illuminate\Support\Facades\Log;
use Exception;

class ProviderManager
{
    /**
     * Instancia a classe de um Provider específico (Factory).
     */
    public static function make(AiProvider $providerModel, string $modelIdentifier): AIProviderInterface
    {
        $driver = strtolower($providerModel->driver);

        $instance = match($driver) {
            'openai' => new OpenAIProvider(),
            'deepseek' => new DeepSeekProvider(),
            // Stubbed future providers:
            // 'gemini' => new GeminiProvider(),
            // 'claude' => new ClaudeProvider(),
            // 'ollama' => new OllamaProvider(),
            default => throw new Exception("Driver IA não suportado: {$driver}"),
        };

        return $instance->setConfig(
            $providerModel->base_url ?? '',
            $providerModel->api_key ?? '', // This will be automatically decrypted by the Model cast
            $modelIdentifier
        );
    }

    /**
     * Router Inteligente: Tenta executar um pedido no Provider principal.
     * Se falhar, segue a cadeia de Fallbacks.
     */
    public static function executeWithFallback(AiAgent $agent, callable $action)
    {
        if (!$agent->provider || !$agent->provider->is_active) {
            throw new Exception("Nenhum Provider ativo configurado para este Agente.");
        }

        if (!$agent->aiModel) {
            throw new Exception("Nenhum Modelo ativo configurado para este Agente.");
        }

        $currentProvider = $agent->provider;
        $maxAttempts = 3;
        $attempt = 1;
        $lastError = null;

        while ($currentProvider && $attempt <= $maxAttempts) {
            try {
                $providerInstance = self::make($currentProvider, $agent->aiModel->identifier);
                
                // Executa a callback injetando a interface do Provider configurado
                return $action($providerInstance);

            } catch (Exception $e) {
                $lastError = $e->getMessage();
                Log::warning("AI Provider '{$currentProvider->name}' falhou. Tentativa: {$attempt}. Erro: {$lastError}");
                
                // Procurar Fallback
                $fallback = $currentProvider->fallback;
                
                if ($fallback && $fallback->is_active) {
                    Log::info("Router IA: A alternar para Fallback Provider '{$fallback->name}'.");
                    $currentProvider = $fallback;
                    $attempt++;
                } else {
                    // Sem fallback ativo disponível
                    break;
                }
            }
        }

        throw new Exception("O Router IA esgotou as tentativas. Falha no Fornecedor Principal e nos Fallbacks. Último erro: {$lastError}");
    }

    /**
     * Utilizado pelo Assistente de Configuração para testar a ligação.
     */
    public static function testConnection(AiProvider $providerModel): array
    {
        try {
            // Usa o gpt-4o-mini ou equivalente apenas para pingar
            $instance = self::make($providerModel, 'ping-test-model');
            
            $start = microtime(true);
            // Simular uma conversa simples
            $response = $instance->chat([
                ['role' => 'user', 'content' => 'Say exactly "OK"']
            ], [], 0.0);
            $time = round((microtime(true) - $start) * 1000); // ms

            return [
                'success' => true,
                'message' => 'Ligação efetuada com sucesso.',
                'response_time' => $time . ' ms',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Falha de comunicação: ' . $e->getMessage(),
            ];
        }
    }
}

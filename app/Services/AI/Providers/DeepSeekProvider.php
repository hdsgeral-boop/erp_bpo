<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\AIProviderInterface;
use Illuminate\Support\Facades\Http;
use Exception;

class DeepSeekProvider implements AIProviderInterface
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $model;

    public function setConfig(string $baseUrl, string $apiKey, string $model): self
    {
        // DeepSeek default base URL (compatible with OpenAI format)
        $this->baseUrl = $baseUrl ?: 'https://api.deepseek.com/v1';
        $this->apiKey = $apiKey;
        $this->model = $model;

        return $this;
    }

    public function chat(array $messages, array $tools = [], float $temperature = 0.7): array
    {
        $payload = [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => $temperature,
        ];

        // DeepSeek supports function calling exactly like OpenAI
        if (!empty($tools)) {
            $payload['tools'] = array_map(function($tool) {
                return [
                    'type' => 'function',
                    'function' => [
                        'name' => $tool->getName(),
                        'description' => $tool->getDescription(),
                        'parameters' => $tool->getParameters(),
                    ]
                ];
            }, $tools);
        }

        $response = Http::withToken($this->apiKey)
            ->timeout(60)
            ->post("{$this->baseUrl}/chat/completions", $payload);

        if ($response->failed()) {
            throw new Exception("DeepSeek API Error: " . $response->body());
        }

        $data = $response->json();
        
        $choice = $data['choices'][0]['message'] ?? [];
        $toolCalls = $choice['tool_calls'] ?? null;
        $content = $choice['content'] ?? null;

        return [
            'role' => 'assistant',
            'content' => $content,
            'tool_calls' => $toolCalls,
            'tokens_used' => $data['usage']['total_tokens'] ?? 0,
        ];
    }

    public function embeddings(string $input): array
    {
        throw new Exception("DeepSeek API does not currently support robust embeddings via standard endpoints.");
    }
}

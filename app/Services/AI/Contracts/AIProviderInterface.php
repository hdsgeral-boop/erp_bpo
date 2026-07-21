<?php

namespace App\Services\AI\Contracts;

interface AIProviderInterface
{
    /**
     * Set configuration dynamically.
     */
    public function setConfig(string $baseUrl, string $apiKey, string $model): self;

    /**
     * Standard chat completion with optional tool calls.
     */
    public function chat(array $messages, array $tools = [], float $temperature = 0.7): array;

    /**
     * Generate embeddings for RAG.
     */
    public function embeddings(string $input): array;
}

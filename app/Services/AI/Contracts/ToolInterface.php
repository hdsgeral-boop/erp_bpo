<?php

namespace App\Services\AI\Contracts;

interface ToolInterface
{
    /**
     * O nome exato da ferramenta para uso no Function Calling.
     */
    public function getName(): string;

    /**
     * A descrição do que a ferramenta faz.
     */
    public function getDescription(): string;

    /**
     * A estrutura JSON Schema dos argumentos necessários.
     */
    public function getParameters(): array;

    /**
     * Executa a ferramenta baseada nos argumentos passados pela IA.
     * Deve devolver um array ou string.
     */
    public function execute(array $arguments): mixed;
}

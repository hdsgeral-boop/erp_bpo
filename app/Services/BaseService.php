<?php

namespace App\Services;

abstract class BaseService
{
    /**
     * Helper para formatar a resposta da Service para as camadas acima (Controllers)
     */
    protected function response(bool $success, string $message, $data = null): array
    {
        return [
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ];
    }
}

<?php

namespace App\Services\OCR\Contracts;

interface OcrServiceInterface
{
    /**
     * Extrai o texto de uma imagem ou PDF.
     * 
     * @param string $physicalPath Caminho absoluto no disco para o ficheiro
     * @return string O texto extraído
     * @throws \Exception
     */
    public function extractText(string $physicalPath): string;
}

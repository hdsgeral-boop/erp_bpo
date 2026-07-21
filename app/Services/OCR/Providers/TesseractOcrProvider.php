<?php

namespace App\Services\OCR\Providers;

use App\Services\OCR\Contracts\OcrServiceInterface;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Support\Facades\Log;

class TesseractOcrProvider implements OcrServiceInterface
{
    /**
     * @inheritDoc
     */
    public function extractText(string $physicalPath): string
    {
        try {
            // Inicializa a biblioteca passando o caminho absoluto da imagem/documento
            $ocr = new TesseractOCR($physicalPath);
            
            // Define o idioma para Português (assumindo pt para Portugal/Angola) e Inglês
            $ocr->lang('por', 'eng');
            
            $text = $ocr->run();
            
            return $text;
        } catch (\Exception $e) {
            Log::error("Erro na extração Tesseract OCR: " . $e->getMessage());
            throw new \Exception("Falha na extração de texto via OCR.");
        }
    }
}

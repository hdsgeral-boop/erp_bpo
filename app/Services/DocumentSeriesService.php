<?php

namespace App\Services;

use App\Models\DocumentSeries;
use Illuminate\Support\Facades\DB;
use Exception;

class DocumentSeriesService
{
    /**
     * Obter o próximo número de uma série documental de forma segura e atómica.
     * 
     * @param string $documentType Ex: 'FT' (Fatura), 'FR' (Fatura-Recibo)
     * @param int $companyId 
     * @param int|null $seriesId Opcional. Se nulo, procura a série default.
     * @return string O número gerado Ex: 'FT 2026/1'
     * @throws Exception
     */
    public function getNextDocumentNumber(string $documentType, int $companyId, ?int $seriesId = null): string
    {
        return DB::transaction(function () use ($documentType, $companyId, $seriesId) {
            $query = DocumentSeries::where('company_id', $companyId)
                                   ->where('document_type', $documentType)
                                   ->where('is_active', true);

            if ($seriesId) {
                $query->where('id', $seriesId);
            } else {
                $query->where('is_default', true);
            }

            // Lock for update para garantir atomicidade no incremento do número
            $series = $query->lockForUpdate()->first();

            if (!$series) {
                // Tentar encontrar a série por defeito para este tipo de documento
                $series = DocumentSeries::where('company_id', $companyId)
                    ->where('document_type', $documentType)
                    ->where('is_active', true)
                    ->where('is_default', true)
                    ->lockForUpdate()
                    ->first();
            }

            if (!$series) {
                // Auto-criar Série Padrão Inicial para este tipo de documento na empresa
                $year = date('Y');
                $series = DocumentSeries::create([
                    'company_id' => $companyId,
                    'document_type' => $documentType,
                    'identifier' => "A{$year}",
                    'description' => "Série Geral {$year} ({$documentType})",
                    'current_number' => 0,
                    'is_active' => true,
                    'is_default' => true,
                ]);
            }

            $series->current_number += 1;
            $series->save();

            // Formato padrão: TIPO SÉRIE/NÚMERO (Ex: FT 2026/1)
            return "{$documentType} {$series->identifier}/{$series->current_number}";
        });
    }

    /**
     * Obter as séries documentais disponíveis para um determinado tipo de documento.
     * 
     * @param string $documentType Ex: 'RC', 'PG'
     * @param int $companyId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableSeries(string $documentType, int $companyId)
    {
        return DocumentSeries::where('company_id', $companyId)
                             ->where('document_type', $documentType)
                             ->where('is_active', true)
                             ->orderBy('is_default', 'desc')
                             ->orderBy('identifier', 'asc')
                             ->get();
    }
}

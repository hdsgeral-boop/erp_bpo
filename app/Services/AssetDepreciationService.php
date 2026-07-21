<?php

namespace App\Services;

use App\Models\FixedAsset;
use App\Models\AssetDepreciation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssetDepreciationService extends BaseService
{
    /**
     * Processa a amortização de um único ativo para um dado ano e mês
     */
    public function processDepreciation(FixedAsset $asset, int $year, ?int $month = null)
    {
        if ($asset->status !== 'active') {
            return $this->response(false, 'Ativo não está ativo');
        }

        if ($asset->residual_value <= 0) {
            return $this->response(false, 'Ativo já totalmente amortizado');
        }

        try {
            DB::beginTransaction();

            $rate = $asset->category->depreciation_rate / 100;
            
            // Cálculo base anual (Quotas Constantes)
            $annualDepreciation = $asset->purchase_value * $rate;
            
            // Ajustar se for mensal
            $depreciationAmount = $month ? ($annualDepreciation / 12) : $annualDepreciation;

            // Evitar amortizar mais do que o valor residual existente
            if ($depreciationAmount > $asset->residual_value) {
                $depreciationAmount = $asset->residual_value;
            }

            // Calcular acumulados e valor contabilístico (net book value)
            $newResidualValue = $asset->residual_value - $depreciationAmount;
            
            // Assegurar que não fica negativo
            if ($newResidualValue < 0) {
                $newResidualValue = 0;
            }
            
            $accumulated = $asset->purchase_value - $newResidualValue;

            $depreciationRecord = AssetDepreciation::create([
                'fixed_asset_id' => $asset->id,
                'year' => $year,
                'month' => $month,
                'depreciation_amount' => $depreciationAmount,
                'accumulated_amount' => $accumulated,
                'net_book_value' => $newResidualValue,
            ]);

            // Atualiza o valor residual do ativo
            $asset->update(['residual_value' => $newResidualValue]);

            // Se atingiu 0, abater
            if ($newResidualValue <= 0) {
                $asset->update(['status' => 'written_off']);
            }

            DB::commit();

            return $this->response(true, 'Amortização processada com sucesso', $depreciationRecord);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Falha ao processar amortização do ativo {$asset->code}: " . $e->getMessage());
            return $this->response(false, 'Falha ao processar amortização', $e->getMessage());
        }
    }
}

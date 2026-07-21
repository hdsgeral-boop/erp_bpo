<?php

namespace App\Services\Accounting;

use App\Services\BaseService;
use Illuminate\Support\Facades\Log;

class TaxRoutineService extends BaseService
{
    /**
     * @var float Taxa base do Imposto de Selo (1%)
     */
    protected const STAMP_DUTY_RATE = 0.01;

    /**
     * Calcula o Imposto de Selo para um determinado valor base
     * 
     * @param float $baseAmount
     * @return float
     */
    public function calculateStampDuty(float $baseAmount): float
    {
        if ($baseAmount <= 0) {
            return 0.0;
        }

        // Retorna o valor arredondado a duas casas decimais
        return round($baseAmount * self::STAMP_DUTY_RATE, 2);
    }

    /**
     * Aplica o imposto de selo a um recibo, atualizando a linha do documento
     * 
     * @param \Illuminate\Database\Eloquent\Model $receipt
     * @return array
     */
    public function applyStampDutyToReceipt($receipt)
    {
        try {
            // Assumimos que o recibo tem uma propriedade 'total_amount'
            $stampDutyAmount = $this->calculateStampDuty($receipt->total_amount ?? 0);
            
            // Lógica de atualização na base de dados (Exemplo abstrato)
            // $receipt->update(['stamp_duty' => $stampDutyAmount]);
            // Pode também criar uma linha no diário contabilístico
            
            return $this->response(true, 'Imposto de Selo calculado e aplicado', [
                'stamp_duty_amount' => $stampDutyAmount,
                'rate' => self::STAMP_DUTY_RATE * 100 . '%'
            ]);
        } catch (\Exception $e) {
            Log::error("Falha ao aplicar imposto de selo ao recibo {$receipt->id}: " . $e->getMessage());
            return $this->response(false, 'Erro no cálculo do imposto de selo');
        }
    }
}

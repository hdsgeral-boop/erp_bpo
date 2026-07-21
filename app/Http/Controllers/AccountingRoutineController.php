<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Accounting\TaxRoutineService;
use App\Traits\ApiResponse;

class AccountingRoutineController extends Controller
{
    use ApiResponse;

    protected $taxService;

    public function __construct(TaxRoutineService $taxService)
    {
        $this->taxService = $taxService;
    }

    /**
     * Interface gráfica para Rotinas de Fecho / Contabilidade
     */
    public function index()
    {
        return view('accounting.routines');
    }

    /**
     * Exemplo prático de processamento em massa
     * (Apuramento de Imposto de Selo de um dado mês)
     */
    public function processStampDuty(Request $request)
    {
        $request->validate([
            'month' => 'required',
            'year' => 'required',
        ]);

        // Simulação de processamento via TaxRoutineService
        // Num cenário real faríamos: $this->taxService->applyStampDutyToReceipt($receipt);
        
        sleep(1); // Simular demora de processamento

        if ($request->wantsJson()) {
            return $this->successResponse('Rotina de Imposto de Selo executada com sucesso para ' . $request->month . '/' . $request->year);
        }

        return back()->with('success', 'Rotina executada.');
    }
}

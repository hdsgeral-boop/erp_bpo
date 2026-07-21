<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Reporting\ReportingService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    use ApiResponse;

    protected $reportingService;

    public function __construct(ReportingService $reportingService)
    {
        $this->reportingService = $reportingService;
    }

    /**
     * Interface gráfica para exportação do SAFT
     */
    public function saftView()
    {
        return view('sales.saft');
    }

    /**
     * Gera e devolve o ficheiro SAFT em formato XML para download
     */
    public function generateSaft(Request $request)
    {
        $request->validate([
            'year' => 'required|digits:4',
            'month' => 'required|digits:2',
        ]);

        // Aqui obteríamos as faturas reais do repositório/mês selecionado
        $invoices = []; // Placeholder

        $response = $this->reportingService->generateSaftXml($invoices, $request->year, $request->month);

        if ($response['success']) {
            $path = $response['data']['path'];
            $filename = $response['data']['filename'];
            
            // Devolve ficheiro para download imediato
            return Storage::disk('local')->download($path, $filename, [
                'Content-Type' => 'text/xml'
            ]);
        }

        return back()->with('error', $response['message']);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * AgtWebhookController
 *
 * Recebe notificações assíncronas em tempo real da AGT (Callback URL / Webhook via HTTP POST)
 * para atualização do estado de validação fiscal dos documentos de faturação.
 */
class AgtWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();
        Log::info('AGT Webhook Notification received:', $payload);

        $invoiceNo = $request->input('invoice_no') ?? $request->input('InvoiceNo');
        $agtStatus = $request->input('status') ?? $request->input('InvoiceStatus') ?? 'VALIDATED';
        $validationMsg = $request->input('message') ?? 'Documento validado com sucesso pela AGT.';

        if (!$invoiceNo) {
            return response()->json([
                'success' => false,
                'message' => 'Parâmetro invoice_no ausente.'
            ], 400);
        }

        $sale = Sale::where('doc_number', $invoiceNo)->first();

        if (!$sale) {
            return response()->json([
                'success' => false,
                'message' => 'Documento não encontrado no ERP.'
            ], 404);
        }

        $sale->update([
            'agt_status' => strtoupper($agtStatus),
            'notes' => ($sale->notes ? $sale->notes . " | " : "") . "[AGT " . Carbon::now()->format('Y-m-d H:i') . "]: " . $validationMsg,
            'updated_at' => Carbon::now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estado fiscal da fatura atualizado com sucesso.',
            'invoice_no' => $invoiceNo,
            'agt_status' => $sale->agt_status
        ], 200);
    }
}

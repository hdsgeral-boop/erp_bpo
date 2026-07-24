<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscriptionPayment;
use App\Http\Controllers\BillingController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentWebhookController extends Controller
{
    /**
     * Webhook automático para confirmação instantânea de pagamentos (Multicaixa Express / Referência)
     */
    public function handle(Request $request)
    {
        $referenceCode = $request->input('reference_code');
        $status = strtoupper($request->input('status', 'PAID'));
        $secret = $request->header('X-Webhook-Secret');

        Log::info('SaaS Payment Webhook Received:', $request->all());

        if (!$referenceCode) {
            return response()->json(['success' => false, 'message' => 'Código de referência em falta.'], 400);
        }

        $payment = SubscriptionPayment::where('reference_code', $referenceCode)->first();

        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Pagamento não encontrado.'], 404);
        }

        if ($payment->status === 'APPROVED') {
            return response()->json(['success' => true, 'message' => 'Pagamento já processado previamente.']);
        }

        if ($status === 'PAID' || $status === 'APPROVED') {
            DB::beginTransaction();
            try {
                $payment->update([
                    'status' => 'APPROVED',
                    'validated_at' => now(),
                ]);

                $billing = new BillingController();
                $billing->activateSubscriptionAndInvoice($payment);

                DB::commit();
                return response()->json(['success' => true, 'message' => 'Subscrição ativada e fatura AGT emitida com sucesso.']);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Webhook activation error: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
        }

        return response()->json(['success' => true, 'message' => 'Notificação recebida.']);
    }
}

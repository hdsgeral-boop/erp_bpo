<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscriptionPayment;
use App\Models\Company;
use App\Http\Controllers\BillingController;
use Illuminate\Support\Facades\DB;

class PaymentManagementController extends Controller
{
    /**
     * Exibe o painel de controlo BackOffice de pagamentos e licenças
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'all');
        $query = SubscriptionPayment::with(['company', 'plan', 'validator', 'invoice'])->orderBy('id', 'desc');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $payments = $query->paginate(15);
        $companies = Company::orderBy('name', 'asc')->get();

        return view('admin.payments.index', compact('payments', 'companies', 'status'));
    }

    /**
     * Aprova um pagamento pendente (Validação Manual pelo BackOffice)
     */
    public function approve($id)
    {
        $payment = SubscriptionPayment::with(['company', 'plan'])->findOrFail($id);

        if ($payment->status === 'APPROVED') {
            return back()->with('info', 'Este pagamento já foi aprovado anteriormente.');
        }

        DB::beginTransaction();
        try {
            $payment->update([
                'status' => 'APPROVED',
                'validated_at' => now(),
                'validated_by' => auth()->id(),
            ]);

            // Invocar helper para ativar licença e gerar Fatura AGT
            $billingCtrl = new BillingController();
            $billingCtrl->activateSubscriptionAndInvoice($payment);

            DB::commit();
            return back()->with('success', 'Pagamento aprovado com sucesso! Licença da empresa ' . $payment->company->name . ' ativada por 30 dias.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Falha ao aprovar pagamento: ' . $e->getMessage());
        }
    }

    /**
     * Rejeita um pagamento pendente com motivo
     */
    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $payment = SubscriptionPayment::findOrFail($id);
        $payment->update([
            'status' => 'REJECTED',
            'validated_at' => now(),
            'validated_by' => auth()->id(),
            'rejection_reason' => $validated['rejection_reason']
        ]);

        return back()->with('success', 'Pagamento marcado como rejeitado.');
    }

    /**
     * Estende manualmente o Trial ou Licença de uma empresa por N dias
     */
    public function extendLicense(Request $request, $companyId)
    {
        $validated = $request->validate([
            'days' => 'required|numeric|min:1|max:365'
        ]);

        $company = Company::findOrFail($companyId);
        $days = (int)$validated['days'];

        $newDate = $company->effective_expiration_date->addDays($days);

        if ($company->subscription_status === 'active') {
            $company->update(['subscription_ends_at' => $newDate]);
        } else {
            $company->update(['trial_ends_at' => $newDate, 'subscription_status' => 'trial']);
        }

        return back()->with('success', 'Licença da empresa ' . $company->name . ' estendida por ' . $days . ' dias.');
    }
}

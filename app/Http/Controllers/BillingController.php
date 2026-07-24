<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPayment;
use App\Models\Company;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\ThirdParty;
use App\Models\DocumentSeries;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BillingController extends Controller
{
    /**
     * Exibe a página de planos e matriz comparativa de recursos
     */
    public function plans()
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('price_monthly', 'asc')->get();
        $user = auth()->user();
        $companyId = session('company_id') ?? ($user->company_id ?? 1);
        $company = Company::find($companyId);

        return view('billing.plans', compact('plans', 'company'));
    }

    /**
     * Exibe a página de checkout para um plano selecionado
     */
    public function checkout($planId)
    {
        $plan = SubscriptionPlan::findOrFail($planId);
        $user = auth()->user();
        $companyId = session('company_id') ?? ($user->company_id ?? 1);
        $company = Company::find($companyId);

        // Gerar Dados de Pagamento Simulados / Dinâmicos
        $entidade = '00142'; // Entidade Exemplo Multicaixa
        $referencia = str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT);
        $referenceCode = 'PAY-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        return view('billing.checkout', compact('plan', 'company', 'entidade', 'referencia', 'referenceCode'));
    }

    /**
     * Registar pedido de pagamento (Referência, Express ou Transferência Bancária)
     */
    public function storePayment(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'payment_method' => 'required|in:multicaixa_ref,express,transfer',
            'reference_code' => 'required|string|max:50',
            'express_phone' => 'nullable|string|max:30',
            'proof_attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $user = auth()->user();
        $companyId = session('company_id') ?? ($user->company_id ?? 1);
        $company = Company::findOrFail($companyId);
        $plan = SubscriptionPlan::findOrFail($validated['plan_id']);

        $proofPath = null;
        if ($request->hasFile('proof_attachment')) {
            $proofPath = $request->file('proof_attachment')->store('payment_proofs', 'public');
        }

        $details = [
            'payment_method_label' => match($validated['payment_method']) {
                'multicaixa_ref' => 'Referência Multicaixa',
                'express' => 'Multicaixa Express Direto',
                'transfer' => 'Transferência Bancária (BFA / BAI)',
            },
            'express_phone' => $request->input('express_phone'),
            'entidade' => $request->input('entidade', '00142'),
            'referencia' => $request->input('referencia'),
        ];

        // Se for Multicaixa Express ou Referência em ambiente de demonstração, aprova automaticamente para facilidade de testes
        $autoApprove = in_array($validated['payment_method'], ['multicaixa_ref', 'express']);
        $status = $autoApprove ? 'APPROVED' : 'PENDING';

        DB::beginTransaction();
        try {
            $payment = SubscriptionPayment::create([
                'company_id' => $company->id,
                'plan_id' => $plan->id,
                'reference_code' => $validated['reference_code'],
                'amount' => $plan->price_monthly,
                'payment_method' => $validated['payment_method'],
                'payment_details' => $details,
                'proof_attachment' => $proofPath,
                'status' => $status,
                'validated_at' => $autoApprove ? now() : null,
                'validated_by' => $autoApprove ? auth()->id() : null,
            ]);

            if ($autoApprove) {
                // Ativar Subscrição e Emitir Fatura AGT
                $this->activateSubscriptionAndInvoice($payment);
            }

            DB::commit();

            if ($autoApprove) {
                return redirect()->route('billing.history')->with('success', 'Pagamento processado com sucesso! A sua licença foi ativada e a fatura AGT foi emitida.');
            }

            return redirect()->route('billing.history')->with('success', 'Comprovativo enviado com sucesso! A nossa equipa de BackOffice fará a validação no prazo de 24h a 72h.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Falha ao registar pagamento: ' . $e->getMessage());
        }
    }

    /**
     * Histórico de pagamentos da empresa e download de faturas AGT
     */
    public function history()
    {
        $user = auth()->user();
        $companyId = session('company_id') ?? ($user->company_id ?? 1);
        $company = Company::find($companyId);
        $payments = SubscriptionPayment::where('company_id', $companyId)->with('plan', 'invoice')->orderBy('id', 'desc')->paginate(10);

        return view('billing.history', compact('company', 'payments'));
    }

    /**
     * Download da Fatura Comercial em PDF emitida pela subscrição
     */
    public function downloadInvoicePdf($id)
    {
        $payment = SubscriptionPayment::with(['company', 'plan', 'invoice'])->findOrFail($id);

        if (!$payment->invoice) {
            return back()->with('error', 'Fatura comercial ainda não emitida.');
        }

        $sale = $payment->invoice;
        $company = $payment->company;

        return response()->view('vendas.documentos.pdf', [
            'sale' => $sale,
            'company' => $company,
        ])->header('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * Helper para ativar subscrição e gerar fatura AGT
     */
    public function activateSubscriptionAndInvoice(SubscriptionPayment $payment)
    {
        $company = $payment->company;
        $plan = $payment->plan;

        // 1. Atualizar Empresa
        $company->update([
            'subscription_status' => 'active',
            'current_plan_id' => $plan->id,
            'subscription_ends_at' => now()->addDays(30),
        ]);

        // 2. Gerar Fatura Fiscal AGT (Sale / FT)
        $masterCompany = Company::where('is_master_data', true)->first() ?? $company;
        
        $customer = ThirdParty::firstOrCreate(
            ['company_id' => $masterCompany->id, 'name' => $company->name],
            ['nif' => $company->nif ?? '999999999', 'is_customer' => true]
        );

        $docNumber = 'FT-SaaS-' . date('Ym') . '-' . str_pad($payment->id, 4, '0', STR_PAD_LEFT);
        $subtotal = $plan->price_monthly;
        $tax = $subtotal * 0.14; // 14% IVA
        $total = $subtotal + $tax;

        $sale = Sale::create([
            'company_id' => $masterCompany->id,
            'customer_id' => $customer->id,
            'doc_type' => 'FT',
            'doc_number' => $docNumber,
            'date' => now()->toDateString(),
            'total_amount' => $subtotal,
            'total_tax' => $tax,
            'amount_paid' => $total,
            'status' => 'ISSUED',
            'hash' => strtoupper(md5($docNumber . now()->toIso8601String())),
            'system_entry_date' => now(),
        ]);

        $payment->update([
            'invoice_id' => $sale->id
        ]);
    }
}

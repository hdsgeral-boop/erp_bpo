<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use App\Models\Sale;
use App\Models\PurchaseInvoice;
use App\Models\Employee;
use App\Models\PayrollRun;
use App\Models\Receipt;
use Illuminate\Support\Facades\DB;

class IntegrationController extends Controller
{
    /**
     * Exibe a interface do Módulo de Integrações
     */
    public function index()
    {
        $user = auth()->user() ?? User::where('email', 'admin@consulvolt.com')->first();
        $tokens = $user ? $user->tokens : collect();
        $companyId = session('company_id') ?? ($user?->company_id ?? 1);
        $company = Company::find($companyId);

        $baseUrl = url('/api/v1/external/powerbi');

        return view('admin.integrations.index', compact('tokens', 'company', 'baseUrl'));
    }

    /**
     * Gera uma nova Chave de API / Token Sanctum
     */
    public function generateKey(Request $request)
    {
        $request->validate([
            'token_name' => 'required|string|max:255',
            'abilities' => 'nullable|array'
        ]);

        $user = auth()->user() ?? User::find(session('user_id')) ?? User::where('email', 'admin@consulvolt.com')->first();

        if (!$user) {
            return back()->with('error', 'Sessão expirada. Por favor efetue login novamente.');
        }

        $abilities = $request->input('abilities', ['*']);
        $token = $user->createToken($request->input('token_name'), $abilities);

        return back()->with('success', 'Chave de API gerada com sucesso!')
                     ->with('new_api_token', $token->plainTextToken);
    }

    /**
     * Revoga uma Chave de API
     */
    public function revokeKey($id)
    {
        $user = auth()->user() ?? User::find(session('user_id')) ?? User::where('email', 'admin@consulvolt.com')->first();

        if ($user) {
            $user->tokens()->where('id', $id)->delete();
            return back()->with('success', 'Chave de API revogada com sucesso.');
        }

        return back()->with('error', 'Sessão expirada. Por favor efetue login novamente.');
    }

    // ═══════════════════════════════════════════════════════
    // ENDPOINTS POWERBI (OData / JSON Feed com Token Sanctum)
    // ═══════════════════════════════════════════════════════

    public function powerbiSales(Request $request)
    {
        $companyId = auth()->user()->company_id ?? session('company_id') ?? 1;

        $sales = Sale::with('customer', 'items.product')
            ->where('company_id', $companyId)
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($s) {
                return [
                    'ID' => $s->id,
                    'Documento' => $s->doc_type . ' ' . $s->doc_number,
                    'Tipo' => $s->doc_type,
                    'Data' => $s->date,
                    'Cliente' => $s->customer ? $s->customer->name : 'Consumidor Final',
                    'Cliente_NIF' => $s->customer ? $s->customer->nif : '999999999',
                    'Total_Incidência' => (float)$s->total_amount,
                    'Total_Imposto' => (float)$s->total_tax,
                    'Total_Geral' => (float)($s->total_amount + $s->total_tax),
                    'Pago' => (float)($s->amount_paid ?? 0),
                    'Estado' => $s->status
                ];
            });

        return response()->json([
            'value' => $sales,
            '@odata.context' => url('/api/v1/external/powerbi/sales/$metadata')
        ]);
    }

    public function powerbiFinancials(Request $request)
    {
        $companyId = auth()->user()->company_id ?? session('company_id') ?? 1;

        $receipts = Receipt::with('thirdParty')
            ->where('company_id', $companyId)
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($r) {
                return [
                    'ID' => $r->id,
                    'Tipo' => $r->doc_type,
                    'Numero' => $r->doc_number,
                    'Data' => $r->date,
                    'Entidade' => $r->thirdParty ? $r->thirdParty->name : 'Geral',
                    'Valor' => (float)$r->total_amount,
                    'Forma_Pagamento' => $r->payment_method ?? 'Transferência',
                    'Status' => $r->status
                ];
            });

        return response()->json([
            'value' => $receipts,
            '@odata.context' => url('/api/v1/external/powerbi/financials/$metadata')
        ]);
    }

    public function powerbiHr(Request $request)
    {
        $companyId = auth()->user()->company_id ?? session('company_id') ?? 1;

        $payrollRuns = PayrollRun::where('company_id', $companyId)
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($run) {
                return [
                    'ID' => $run->id,
                    'Referência' => $run->reference,
                    'Mês' => $run->month,
                    'Ano' => $run->year,
                    'Total_Bruto' => (float)$run->total_base,
                    'Total_Líquido' => (float)$run->total_net_paid,
                    'Total_INSS' => (float)$run->total_inss,
                    'Total_IRT' => (float)$run->total_irt,
                    'Estado' => $run->status
                ];
            });

        return response()->json([
            'value' => $payrollRuns,
            '@odata.context' => url('/api/v1/external/powerbi/hr/$metadata')
        ]);
    }
}

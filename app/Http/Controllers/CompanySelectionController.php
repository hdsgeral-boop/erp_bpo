<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;
use App\Services\TenantContextService;
use Exception;

class CompanySelectionController extends Controller
{
    protected TenantContextService $tenantContextService;

    public function __construct(TenantContextService $tenantContextService)
    {
        $this->tenantContextService = $tenantContextService;
    }

    /**
     * Apresenta o ecrã de seleção de empresa para utilizadores vinculados a múltiplas empresas.
     */
    public function showSelectForm(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $isSuperAdmin = $user->hasRole('Super Admin');
        $companies = $isSuperAdmin ? Company::with('currentPlan')->get() : $user->companies()->with('currentPlan')->get();

        if ($companies->isEmpty()) {
            return redirect()->route('login')->with('error', 'Nenhuma empresa associada a esta conta.');
        }

        // Se tiver apenas 1 empresa, ativa o contexto e entra diretamente
        if ($companies->count() === 1) {
            try {
                $this->tenantContextService->activateCompanyContext($user, $companies->first()->id, $request);
                return redirect()->intended('/dashboard');
            } catch (Exception $e) {
                return redirect()->route('login')->with('error', $e->getMessage());
            }
        }

        // Construir dados informativos dos cartões das empresas
        $companyCards = $companies->map(function ($company) use ($user) {
            $roleName = $user->roleNameInCompany($company->id);
            $planName = $company->currentPlan?->name 
                ?? ($company->subscription_status === 'trial' ? 'Plano Trial (30 Dias)' : 'Plano Enterprise');
            
            $statusLabel = match($company->subscription_status) {
                'active' => 'Activo',
                'trial' => 'Trial',
                'expired' => 'Expirado',
                'cancelled' => 'Cancelado',
                default => 'Activo',
            };

            return [
                'id' => $company->id,
                'name' => $company->name,
                'nif' => $company->nif ?? 'N/A',
                'logo' => $company->logo,
                'role' => $roleName,
                'plan' => $planName,
                'status' => $statusLabel,
                'days_remaining' => $company->remaining_days,
                'is_active' => $company->isLicenseActive(),
            ];
        });

        return view('auth.select-company', compact('user', 'companyCards', 'isSuperAdmin'));
    }

    /**
     * Processa a seleção exclusiva de empresa e ativação de contexto.
     */
    public function selectCompany(Request $request)
    {
        $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
        ]);

        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $companyId = (int)$request->input('company_id');

        try {
            $result = $this->tenantContextService->activateCompanyContext($user, $companyId, $request);
            return redirect()->route('dashboard')->with('success', 'Entrou com sucesso na empresa: ' . $result['company']->name);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}

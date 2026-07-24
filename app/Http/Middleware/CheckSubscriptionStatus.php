<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Company;

class CheckSubscriptionStatus
{
    /**
     * Valida o estado da licença/subscrição da empresa ativa.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        // Super Admin e BackOffice possuem permissão de bypass para auditoria e gestão
        if ($user->hasRole('Super Admin') || $user->hasRole('BackOffice')) {
            return $next($request);
        }

        $activeCompanyId = session('company_id') ?? ($user->company_id ?? 1);
        $company = Company::find($activeCompanyId);

        if (!$company) {
            return $next($request);
        }

        // Se a licença não estiver ativa (trial expirado ou subscrição expirada)
        if (!$company->isLicenseActive()) {
            // Permitir acesso APENAS às rotas de faturação/pagamento, logout e alteração de palavra-passe
            $allowedRoutes = [
                'billing.plans',
                'billing.checkout',
                'billing.store_payment',
                'billing.history',
                'billing.invoice.pdf',
                'company.switch',
                'logout',
                'profile.show',
                'profile.update',
                'profile.password.update'
            ];

            $routeName = $request->route()?->getName();

            if (!in_array($routeName, $allowedRoutes) && !$request->is('billing*') && !$request->is('logout')) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'A subscrição da sua empresa expirou. Efetue a renovação para continuar a utilizar o ERP.',
                        'redirect' => route('billing.plans')
                    ], 402);
                }

                return redirect()->route('billing.plans')->with('error', 'A subscrição da sua empresa expirou. Escolha um dos planos abaixo para reativar o acesso total ao ERP.');
            }
        }

        return $next($request);
    }
}

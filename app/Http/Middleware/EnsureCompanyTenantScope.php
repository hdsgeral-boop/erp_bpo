<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Company;

class EnsureCompanyTenantScope
{
    /**
     * Handle an incoming request ensuring tenant isolation for multi-company access.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user) {
            // Se a sessão não tiver company_id definido, usa o da ficha do utilizador ou a primeira empresa
            if (!session()->has('company_id') || empty(session('company_id'))) {
                $userCompanyId = $user->company_id ?? Company::value('id') ?? 1;
                session(['company_id' => $userCompanyId]);
            }

            $currentCompanyId = (int) session('company_id');

            // Garantir que a role ativa do Spatie Permission corresponda à empresa selecionada na sessão
            if (!$user->hasRole('Super Admin')) {
                $companyPivot = $user->companies()->where('companies.id', $currentCompanyId)->first();
                if ($companyPivot && $companyPivot->pivot->role_id) {
                    $activeRole = \Spatie\Permission\Models\Role::find($companyPivot->pivot->role_id);
                    if ($activeRole && !$user->hasRole($activeRole->name)) {
                        $user->syncRoles([$activeRole]);
                    }
                }
            }

            // Sanitização de entradas: Se a requisição enviar company_id diferente sem ser Super Admin, rejeita
            if ($request->has('company_id') && (int)$request->input('company_id') !== $currentCompanyId) {
                if (!$user->hasRole('Super Admin')) {
                    if ($request->wantsJson() || $request->is('api/*')) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Acesso negado: tentativa de alteração não autorizada do escopo da empresa.'
                        ], 403);
                    }
                    return redirect()->route('dashboard')->with('error', 'Sem permissão para aceder a dados de outra empresa.');
                }
            }
        }

        return $next($request);
    }
}

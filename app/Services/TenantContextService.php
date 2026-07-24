<?php

namespace App\Services;

use App\Models\User;
use App\Models\Company;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Exception;

class TenantContextService
{
    /**
     * Ativa o contexto completo de um Tenant / Empresa para um utilizador.
     * Recalcula papéis, permissões, limpa caches e regista auditoria.
     *
     * @param User $user
     * @param int $companyId
     * @param Request|null $request
     * @return array
     * @throws Exception
     */
    public function activateCompanyContext(User $user, int $companyId, ?Request $request = null): array
    {
        $isSuperAdmin = $user->hasRole('Super Admin');
        $userCompanyIds = $user->companies->pluck('id')->toArray();

        // 1. Validar se o utilizador pertence à empresa ou se é Super Admin
        if (!$isSuperAdmin && !in_array($companyId, $userCompanyIds)) {
            Log::warning("Tentativa não autorizada de acesso a empresa: Utilizador ID {$user->id} tentou aceder a Empresa ID {$companyId}");
            throw new Exception('Acesso negado: Não possui permissão para aceder a esta empresa.');
        }

        $company = Company::find($companyId);
        if (!$company) {
            throw new Exception('Empresa não encontrada.');
        }

        // 2. Invalidação Imediata de Cache de Permissões e Menus
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->invalidateTenantCache($companyId, $user->id);

        // 3. Determinar e Ativar o Papel (Role) para esta Empresa
        $activeRole = null;
        if ($isSuperAdmin) {
            $activeRoleName = 'Super Admin';
        } else {
            $companyPivot = $user->companies()->where('companies.id', $companyId)->first();
            $roleId = $companyPivot?->pivot?->role_id;

            if ($roleId) {
                $activeRole = Role::find($roleId);
            }

            if (!$activeRole) {
                // Fallback para Gestor ou primeiro papel do utilizador
                $activeRole = Role::where('name', 'Gestor')->first() ?? $user->roles->first();
            }

            $activeRoleName = $activeRole?->name ?? 'Utilizador';

            // Recalcular permissões Spatie atribuindo APENAS a Role da empresa ativa
            if ($activeRole) {
                $user->syncRoles([$activeRole]);
            }
        }

        // 4. Guardar na Sessão APENAS os dados da empresa ativa
        session([
            'company_id' => $company->id,
            'company_name' => $company->name,
            'active_role_id' => $activeRole?->id,
            'active_role_name' => $activeRoleName,
            'subscription_status' => $company->subscription_status,
            'license_active' => $company->isLicenseActive(),
        ]);

        // 5. Registar evento de auditoria
        $ip = $request?->ip() ?? request()->ip();
        $userAgent = $request?->userAgent() ?? request()->userAgent();

        $this->logContextActivation($user, $company, $activeRoleName, $ip, $userAgent);

        return [
            'success' => true,
            'company' => $company,
            'role_name' => $activeRoleName,
            'message' => 'Contexto ativado para a empresa: ' . $company->name,
        ];
    }

    /**
     * Invalida todas as chaves de cache relacionadas com o tenant e o utilizador.
     */
    public function invalidateTenantCache(int $companyId, int $userId): void
    {
        Cache::forget("tenant:{$companyId}:permissions");
        Cache::forget("tenant:{$companyId}:menus");
        Cache::forget("tenant:{$companyId}:settings");
        Cache::forget("tenant:{$companyId}:dashboard");
        Cache::forget("tenant:{$companyId}:kpis");
        Cache::forget("user_{$userId}_permissions");
    }

    /**
     * Registar log de auditoria no sistema.
     */
    protected function logContextActivation(User $user, Company $company, string $roleName, ?string $ip, ?string $userAgent): void
    {
        $os = php_uname('s');

        try {
            DB::table('audit_logs')->insert([
                'user_id' => $user->id,
                'action' => 'COMPANY_CONTEXT_ACTIVATION',
                'details' => json_encode([
                    'company_id' => $company->id,
                    'company_name' => $company->name,
                    'role_active' => $roleName,
                    'ip' => $ip,
                    'os' => $os,
                    'user_agent' => $userAgent,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Em caso de fallback na tabela audit_logs
            Log::info("Auditoria Contexto: Utilizador ID {$user->id} ativou Empresa {$company->name} (Role: {$roleName}) IP: {$ip}");
        }
    }
}

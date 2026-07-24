<?php

namespace App\Services;

use App\Models\User;
use App\Models\Company;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;

class UserCompanyRegistrationService
{
    /**
     * Regista simultaneamente uma nova empresa e um novo utilizador em transação DB.
     * Atribui o utilizador como Administrador da nova empresa.
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function registerNewCompanyAndUser(array $data): array
    {
        // 1. Verificação Estrita de Duplicados antes de iniciar a transação
        $this->validateUniqueness([
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'company_nif' => $data['company_nif'] ?? null,
        ]);

        return DB::transaction(function () use ($data) {
            // 2. Criar a Empresa
            $company = Company::create([
                'name' => $data['company_name'],
                'nif' => $data['company_nif'],
                'email' => $data['company_email'] ?? null,
                'phone' => $data['company_phone'] ?? null,
                'address' => $data['company_address'] ?? null,
            ]);

            // 3. Criar o Utilizador
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'status' => 'active',
                'email_verified_at' => isset($data['email_verified']) && $data['email_verified'] ? now() : null,
                'google_id' => $data['google_id'] ?? null,
                'google_email' => $data['google_email'] ?? null,
                'google_avatar' => $data['google_avatar'] ?? null,
                'provider' => $data['provider'] ?? null,
                'provider_id' => $data['provider_id'] ?? null,
                'avatar' => $data['avatar'] ?? null,
            ]);

            // 4. Obter a Role de Administrador
            $adminRole = Role::where('name', 'Administrador')->first() 
                      ?? Role::where('name', 'Gestor')->first() 
                      ?? Role::first();

            // 5. Criar vínculo na tabela pivot company_user
            $user->companies()->attach($company->id, [
                'role_id' => $adminRole?->id,
                'status' => 'active',
                'joined_at' => now(),
            ]);

            // 6. Atribuir o papel no Spatie Permission
            if ($adminRole) {
                $user->assignRole($adminRole);
            }

            Log::info("Novo Registo Simultâneo Concluído: Utilizador ID {$user->id} ({$user->email}) criou Empresa ID {$company->id} ({$company->name}) como Administrador.");

            return [
                'user' => $user,
                'company' => $company,
                'role' => $adminRole,
            ];
        });
    }

    /**
     * Associa um utilizador existente (ex: Funcionário) a uma nova empresa criada por ele.
     * O utilizador torna-se Administrador da nova empresa sem duplicar a conta.
     *
     * @param User $user
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function attachUserToNewCompany(User $user, array $data): array
    {
        // 1. Validar unicidade do NIF da nova empresa
        $existingCompany = Company::where('nif', $data['company_nif'])->first();
        if ($existingCompany) {
            throw new Exception('Já existe uma empresa registada com este NIF. Por favor, verifique o NIF introduzido.');
        }

        return DB::transaction(function () use ($user, $data) {
            // 2. Criar a nova empresa
            $company = Company::create([
                'name' => $data['company_name'],
                'nif' => $data['company_nif'],
                'email' => $data['company_email'] ?? null,
                'phone' => $data['company_phone'] ?? null,
                'address' => $data['company_address'] ?? null,
            ]);

            // 3. Obter a Role de Administrador
            $adminRole = Role::where('name', 'Administrador')->first() 
                      ?? Role::where('name', 'Gestor')->first() 
                      ?? Role::first();

            // 4. Criar vínculo da nova empresa para o utilizador existente
            $user->companies()->attach($company->id, [
                'role_id' => $adminRole?->id,
                'status' => 'active',
                'joined_at' => now(),
            ]);

            Log::info("Nova Empresa Adicionada a Utilizador Existente: Utilizador ID {$user->id} criou Empresa ID {$company->id} ({$company->name}) como Administrador.");

            return [
                'user' => $user,
                'company' => $company,
                'role' => $adminRole,
            ];
        });
    }

    /**
     * Validação estrita de unicidade com mensagens personalizadas e amigáveis.
     *
     * @param array $criteria
     * @throws Exception
     */
    public function validateUniqueness(array $criteria): void
    {
        // Check Email
        if (!empty($criteria['email'])) {
            $userWithEmail = User::where('email', $criteria['email'])->first();
            if ($userWithEmail) {
                throw new Exception('Já existe uma conta associada a este email. Utilize Recuperar Palavra-passe ou inicie sessão.');
            }
        }

        // Check Phone
        if (!empty($criteria['phone'])) {
            $userWithPhone = User::where('phone', $criteria['phone'])->first();
            if ($userWithPhone) {
                throw new Exception('Este número de telefone já se encontra associado a outra conta de utilizador.');
            }
        }

        // Check NIF
        if (!empty($criteria['company_nif'])) {
            $companyWithNif = Company::where('nif', $criteria['company_nif'])->first();
            if ($companyWithNif) {
                throw new Exception('Já existe uma empresa registada com este NIF. Por favor, verifique o NIF introduzido.');
            }
        }
    }
}

<?php

namespace App\Services;

use App\Repositories\Contracts\CompanyRepositoryInterface;
use Illuminate\Support\Facades\Log;

class CompanyService extends BaseService
{
    protected $companyRepository;

    public function __construct(CompanyRepositoryInterface $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function createCompany(array $data)
    {
        try {
            if (isset($data['is_master_data']) && $data['is_master_data']) {
                $this->resetMasterCompany();
            }

            $company = $this->companyRepository->create($data);
            Log::info("Nova Empresa Criada: {$company->name}");

            // Se for um utilizador autenticado a criar a empresa (ex: Funcionário que cria a sua empresa)
            if (auth()->check()) {
                $user = auth()->user();
                $adminRole = \Spatie\Permission\Models\Role::where('name', 'Administrador')->first()
                          ?? \Spatie\Permission\Models\Role::first();

                // Vincular o utilizador à nova empresa se ainda não estiver vinculado
                if (!$user->companies()->where('companies.id', $company->id)->exists()) {
                    $user->companies()->attach($company->id, [
                        'role_id' => $adminRole?->id,
                        'status' => 'active',
                        'joined_at' => now(),
                    ]);
                }
            }
            
            return $this->response(true, 'Empresa criada com sucesso', $company);
        } catch (\Exception $e) {
            Log::error("Erro a criar empresa: " . $e->getMessage());
            return $this->response(false, 'Erro ao criar a empresa', $e->getMessage());
        }
    }

    public function updateCompany(int $id, array $data)
    {
        try {
            if (isset($data['is_master_data']) && $data['is_master_data']) {
                $this->resetMasterCompany($id);
            }

            $company = $this->companyRepository->update($id, $data);
            Log::info("Empresa Atualizada: {$company->name}");
            
            return $this->response(true, 'Empresa atualizada com sucesso', $company);
        } catch (\Exception $e) {
            Log::error("Erro a atualizar empresa: " . $e->getMessage());
            return $this->response(false, 'Erro ao atualizar a empresa', $e->getMessage());
        }
    }

    protected function resetMasterCompany(int $excludeId = null)
    {
        $query = \App\Models\Company::where('is_master_data', true);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        $query->update(['is_master_data' => false]);
    }
}

<?php

namespace App\Services;

use App\Repositories\Contracts\AssetRepositoryInterface;
use Illuminate\Support\Facades\Log;

class AssetService extends BaseService
{
    protected $assetRepository;

    public function __construct(AssetRepositoryInterface $assetRepository)
    {
        $this->assetRepository = $assetRepository;
    }

    public function createAsset(array $data, $userId = null)
    {
        try {
            $data['status'] = $data['status'] ?? 'active';
            $asset = $this->assetRepository->create($data);
            
            // Registar movimento inicial
            \App\Models\AssetMovement::create([
                'fixed_asset_id' => $asset->id,
                'movement_date' => now(),
                'type' => 'allocation',
                'to_department_id' => $asset->department_id,
                'to_employee_id' => $asset->employee_id,
                'to_location' => $asset->location,
                'to_status' => $asset->status,
                'notes' => 'Registo inicial do ativo.',
                'created_by' => $userId,
            ]);
            
            Log::info("Novo Ativo Criado: {$asset->code}");
            
            return $this->response(true, 'Ativo criado com sucesso', $asset);
        } catch (\Exception $e) {
            Log::error("Erro a criar ativo: " . $e->getMessage());
            return $this->response(false, 'Erro ao criar o ativo', $e->getMessage());
        }
    }

    public function updateAsset(int $id, array $data, $userId = null)
    {
        try {
            $asset = $this->assetRepository->find($id);
            $oldDepartment = $asset->department_id;
            $oldEmployee = $asset->employee_id;
            $oldLocation = $asset->location;
            $oldStatus = $asset->status;

            $this->assetRepository->update($id, $data);
            $asset->refresh();

            // Verificar se houve mudança de alocação ou estado
            if ($oldDepartment != $asset->department_id || $oldEmployee != $asset->employee_id || $oldLocation != $asset->location || $oldStatus != $asset->status) {
                \App\Models\AssetMovement::create([
                    'fixed_asset_id' => $asset->id,
                    'movement_date' => now(),
                    'type' => ($oldStatus != $asset->status) ? 'status_change' : 'allocation',
                    'from_department_id' => $oldDepartment,
                    'to_department_id' => $asset->department_id,
                    'from_employee_id' => $oldEmployee,
                    'to_employee_id' => $asset->employee_id,
                    'from_location' => $oldLocation,
                    'to_location' => $asset->location,
                    'from_status' => $oldStatus,
                    'to_status' => $asset->status,
                    'notes' => 'Atualização de alocação/estado do ativo.',
                    'created_by' => $userId,
                ]);
            }

            return $this->response(true, 'Ativo atualizado com sucesso', $asset);
        } catch (\Exception $e) {
            Log::error("Erro a atualizar ativo: " . $e->getMessage());
            return $this->response(false, 'Erro ao atualizar o ativo', $e->getMessage());
        }
    }
    
    /**
     * Abater ou Vender um ativo
     */
    public function updateStatus(int $id, string $status, $userId = null)
    {
        try {
            $asset = $this->assetRepository->find($id);
            $oldStatus = $asset->status;
            
            $this->assetRepository->update($id, ['status' => $status]);

            \App\Models\AssetMovement::create([
                'fixed_asset_id' => $asset->id,
                'movement_date' => now(),
                'type' => 'status_change',
                'from_status' => $oldStatus,
                'to_status' => $status,
                'notes' => "Status alterado para {$status}",
                'created_by' => $userId,
            ]);

            return $this->response(true, "Status do ativo alterado para {$status}");
        } catch (\Exception $e) {
            return $this->response(false, "Falha ao alterar status", $e->getMessage());
        }
    }
}

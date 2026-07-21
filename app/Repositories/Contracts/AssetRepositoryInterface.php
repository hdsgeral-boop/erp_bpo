<?php

namespace App\Repositories\Contracts;

interface AssetRepositoryInterface extends BaseRepositoryInterface
{
    public function paginate($perPage = 15, $search = null, $categoryId = null, $departmentId = null, $employeeId = null, $status = null);
    
    public function findWithDetails($id);

    /**
     * Obter lista de ativos formatada para mapas
     */
    public function getAssetsForMaps(array $filters = []);
    
    /**
     * Encontrar ativos prontos para serem abatidos
     */
    public function getReadyForWriteOff();
}

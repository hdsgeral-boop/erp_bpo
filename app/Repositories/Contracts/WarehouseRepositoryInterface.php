<?php

namespace App\Repositories\Contracts;

interface WarehouseRepositoryInterface extends BaseRepositoryInterface
{
    public function paginate($perPage = 15, $search = null);
    public function findWithDetails($id);
}

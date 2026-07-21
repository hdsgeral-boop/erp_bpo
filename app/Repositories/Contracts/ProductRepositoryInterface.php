<?php

namespace App\Repositories\Contracts;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function paginate($perPage = 15, $search = null, $categoryId = null, $isInventory = null);
    public function findWithDetails($id);
    public function findByCode($code);
}

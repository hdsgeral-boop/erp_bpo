<?php

namespace App\Repositories;

use App\Models\Warehouse;
use App\Repositories\Contracts\WarehouseRepositoryInterface;

class WarehouseRepository extends BaseRepository implements WarehouseRepositoryInterface
{
    public function __construct(Warehouse $model)
    {
        parent::__construct($model);
    }

    public function paginate($perPage = 15, $search = null)
    {
        $query = $this->model->newQuery();
        
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
        }
        
        return $query->orderBy('name', 'asc')->paginate($perPage);
    }

    public function findWithDetails($id)
    {
        return $this->model->with(['stocks.product.category'])->findOrFail($id);
    }
}

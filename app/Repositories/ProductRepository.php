<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function paginate($perPage = 15, $search = null, $categoryId = null, $isInventory = null)
    {
        $query = $this->model->with(['category']);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        if ($isInventory !== null) {
            $query->where('is_inventory', $isInventory);
        }
        
        return $query->orderBy('name', 'asc')->paginate($perPage);
    }

    public function findWithDetails($id)
    {
        return $this->model->with(['category', 'stocks.warehouse', 'movements.fromWarehouse', 'movements.toWarehouse', 'movements.creator', 'attachments'])
                           ->findOrFail($id);
    }

    public function findByCode($code)
    {
        return $this->model->where('code', $code)->first();
    }
}

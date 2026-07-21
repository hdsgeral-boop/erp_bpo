<?php

namespace App\Repositories;

use App\Models\FixedAsset;
use App\Repositories\Contracts\AssetRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AssetRepository extends BaseRepository implements AssetRepositoryInterface
{
    public function __construct(FixedAsset $model)
    {
        parent::__construct($model);
    }

    public function paginate($perPage = 15, $search = null, $categoryId = null, $departmentId = null, $employeeId = null, $status = null)
    {
        $query = $this->model->with(['category', 'department', 'employee']);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }
        if ($status) {
            $query->where('status', $status);
        }
        
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findWithDetails($id)
    {
        return $this->model->with(['category', 'department', 'employee', 'vendor', 'movements.fromDepartment', 'movements.toDepartment', 'movements.fromEmployee', 'movements.toEmployee', 'movements.creator', 'attachments'])->findOrFail($id);
    }

    public function getAssetsForMaps(array $filters = [])
    {
        $query = $this->model->with('category');
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->get();
    }

    public function getReadyForWriteOff()
    {
        return $this->model->where('residual_value', 0)
                           ->where('status', 'active')
                           ->get();
    }
}

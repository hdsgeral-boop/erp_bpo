<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Repositories\Contracts\EmployeeRepositoryInterface;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    public function all()
    {
        return Employee::orderBy('name')->get();
    }
    
    public function paginate($perPage = 15, $search = null, $departmentId = null, $isActive = null)
    {
        $query = Employee::with(['department', 'position', 'role']);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nif', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }
        
        return $query->orderBy('name')->paginate($perPage);
    }
    
    public function find($id)
    {
        return Employee::with(['department', 'position', 'role', 'attachments'])->findOrFail($id);
    }
    
    public function create(array $data)
    {
        return Employee::create($data);
    }
    
    public function update($id, array $data)
    {
        $employee = $this->find($id);
        $employee->update($data);
        return $employee;
    }
    
    public function delete($id)
    {
        $employee = $this->find($id);
        return $employee->delete();
    }
    
    public function findByNif($nif, $companyId, $excludeId = null)
    {
        if (empty($nif)) return null;
        
        $query = Employee::where('nif', $nif)
                        ->where('company_id', $companyId);
                          
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->first();
    }
}

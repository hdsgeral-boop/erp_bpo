<?php

namespace App\Repositories\Contracts;

interface EmployeeRepositoryInterface
{
    public function all();
    
    public function paginate($perPage = 15, $search = null, $departmentId = null, $isActive = null);
    
    public function find($id);
    
    public function create(array $data);
    
    public function update($id, array $data);
    
    public function delete($id);
    
    public function findByNif($nif, $companyId, $excludeId = null);
}

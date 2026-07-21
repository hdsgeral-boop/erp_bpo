<?php

namespace App\Repositories\Contracts;

interface ThirdPartyRepositoryInterface
{
    public function all();
    
    public function paginate($perPage = 15, $search = null, $type = null);
    
    public function find($id);
    
    public function create(array $data);
    
    public function update($id, array $data);
    
    public function delete($id);
    
    public function findByNif($nif, $companyId, $excludeId = null);
}

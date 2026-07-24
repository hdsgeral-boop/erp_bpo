<?php

namespace App\Repositories;

use App\Models\ThirdParty;
use App\Repositories\Contracts\ThirdPartyRepositoryInterface;

class ThirdPartyRepository implements ThirdPartyRepositoryInterface
{
    public function all()
    {
        return ThirdParty::orderBy('name')->get();
    }
    
    public function paginate($perPage = 15, $search = null, $type = null, $companyId = null)
    {
        $query = ThirdParty::query();
        
        $companyIdResolved = $companyId ?? session('company_id') ?? auth()->user()?->company_id ?? 1;
        $query->where('company_id', $companyIdResolved);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nif', 'like', "%{$search}%")
                  ->orWhere('account_code', 'like', "%{$search}%");
            });
        }
        
        if ($type === 'customer') {
            $query->customers();
        } elseif ($type === 'supplier') {
            $query->suppliers();
        }
        
        return $query->orderBy('name')->paginate($perPage);
    }
    
    public function find($id)
    {
        return ThirdParty::findOrFail($id);
    }
    
    public function create(array $data)
    {
        return ThirdParty::create($data);
    }
    
    public function update($id, array $data)
    {
        $thirdParty = $this->find($id);
        $thirdParty->update($data);
        return $thirdParty;
    }
    
    public function delete($id)
    {
        $thirdParty = $this->find($id);
        return $thirdParty->delete();
    }
    
    public function findByNif($nif, $companyId, $excludeId = null)
    {
        if (empty($nif)) return null;
        
        $query = ThirdParty::where('nif', $nif)
                          ->where('company_id', $companyId);
                          
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->first();
    }
}

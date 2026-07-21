<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

interface BaseRepositoryInterface
{
    public function all(array $columns = ['*']): Collection;
    
    public function find(int $id, array $columns = ['*']): ?Model;
    
    public function findOrFail(int $id, array $columns = ['*']): Model;
    
    public function create(array $data): Model;
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;
    
    public function with(array $relations): self;
}

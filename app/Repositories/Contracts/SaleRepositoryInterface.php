<?php

namespace App\Repositories\Contracts;

interface SaleRepositoryInterface
{
    public function paginateSales(int $perPage = 15, ?string $search = null, ?string $status = null);
    public function paginateSalesByCategory(int $perPage = 15, ?string $search = null, ?string $status = null, ?array $docTypes = null);
    public function findSale(int $id);
}

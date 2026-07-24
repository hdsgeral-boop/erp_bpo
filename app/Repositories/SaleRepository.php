<?php

namespace App\Repositories;

use App\Models\Sale;
use App\Repositories\Contracts\SaleRepositoryInterface;

class SaleRepository implements SaleRepositoryInterface
{
    public function paginateSales(int $perPage = 15, ?string $search = null, ?string $status = null)
    {
        return $this->paginateSalesByCategory($perPage, $search, $status, null);
    }

    public function paginateSalesByCategory(int $perPage = 15, ?string $search = null, ?string $status = null, ?array $docTypes = null)
    {
        $companyId = session('company_id') ?? auth()->user()?->company_id ?? 1;
        $query = Sale::where('company_id', $companyId)->with(['customer', 'creator', 'warehouse'])->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('doc_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($docTypes && count($docTypes) > 0) {
            $query->whereIn('doc_type', $docTypes);
        }

        return $query->paginate($perPage);
    }

    public function findSale(int $id)
    {
        return Sale::with(['items.product', 'customer', 'creator', 'warehouse', 'canceller', 'attachments'])->findOrFail($id);
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, // Primary Key
            'third_party_id' => $this->third_party_id, // Foreign Key - Vital para o PowerBI cruzar com Clientes
            'document_type' => $this->document_type, // FT, RC, NC...
            'document_number' => $this->document_number,
            'issue_date' => $this->issue_date,
            'due_date' => $this->due_date,
            'subtotal' => (float) $this->subtotal,
            'tax_total' => (float) $this->tax_total,
            'total' => (float) $this->total,
            'status' => $this->status, // draft, final, paid, canceled
            
            // Sincronização Incremental
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}

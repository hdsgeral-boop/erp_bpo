<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ThirdPartyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, // Primary Key exposta para relacionamentos no PowerBI
            'type' => $this->type, // customer or supplier
            'name' => $this->name,
            'nif' => $this->nif,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            // Campos de Sincronização Incremental
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}

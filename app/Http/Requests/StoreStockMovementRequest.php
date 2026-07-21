<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'type' => 'required|string|in:in,out,transfer,adjustment',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ];

        // Se for transferência, requer origem e destino
        if ($this->input('type') === 'transfer') {
            $rules['from_warehouse_id'] = 'required|exists:warehouses,id|different:to_warehouse_id';
            $rules['to_warehouse_id'] = 'required|exists:warehouses,id';
        } 
        // Se for entrada (in)
        elseif ($this->input('type') === 'in' || $this->input('type') === 'adjustment') {
            $rules['to_warehouse_id'] = 'required|exists:warehouses,id';
        }
        // Se for saída (out)
        elseif ($this->input('type') === 'out') {
            $rules['from_warehouse_id'] = 'required|exists:warehouses,id';
        }

        return $rules;
    }
}

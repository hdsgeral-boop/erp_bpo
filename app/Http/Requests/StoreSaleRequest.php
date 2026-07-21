<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:third_parties,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'series_id' => 'nullable|exists:document_series,id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_id' => 'required|exists:taxes,id',
            'items.*.exemption_reason' => 'nullable|string',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
        ];
    }
}

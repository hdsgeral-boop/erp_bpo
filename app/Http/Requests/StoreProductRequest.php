<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (
            auth()->user()->can('inventory.create') || 
            auth()->user()->hasRole(['Administrador', 'Gestor', 'Gestor de Armazém', 'Super Admin'])
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'category_id' => 'required|exists:product_categories,id',
            'price' => 'nullable|numeric|min:0',
            'unit_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|max:20',
            'min_stock' => 'nullable|numeric|min:0',
            'max_stock' => 'nullable|numeric|min:0',
            'company_id' => 'nullable|integer',
            'is_inventory' => 'nullable',
            'is_asset' => 'nullable',
            'is_blocked' => 'nullable',
            'description' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:10240',
        ];
    }
}

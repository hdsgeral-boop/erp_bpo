<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseRequest extends FormRequest
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
            'location' => 'nullable|string|max:255',
            'company_id' => 'nullable|integer',
            'is_active' => 'nullable',
        ];
    }
}

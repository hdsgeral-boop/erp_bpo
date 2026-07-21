<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFixedAssetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Delegation to Policy in Controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:asset_categories,id',
            'code' => 'required|string|unique:fixed_assets,code',
            'name' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'purchase_value' => 'required|numeric|min:0',
            'residual_value' => 'nullable|numeric|min:0',
            'useful_life_years' => 'nullable|integer|min:1',
            'vendor_id' => 'nullable|exists:third_parties,id',
            'department_id' => 'nullable|exists:departments,id',
            'employee_id' => 'nullable|exists:employees,id',
            'location' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:active,sold,written_off',
            'notes' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:10240',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'nif' => 'nullable|string|max:50',
            'inss' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'role_id' => 'nullable|exists:roles,id',
            'admission_date' => 'nullable|date',
            'base_salary' => 'nullable|numeric|min:0',
            'subsidy_meal' => 'nullable|numeric|min:0',
            'subsidy_transport' => 'nullable|numeric|min:0',
            'bank_name' => 'nullable|string|max:100',
            'iban' => 'nullable|string|max:100',
            'work_days' => 'nullable|integer|min:0|max:31',
            'is_active' => 'boolean',
            'is_retired' => 'boolean',
            'is_master_data' => 'boolean',
            'attachments.*' => 'nullable|file|max:10240',
        ];
    }
}

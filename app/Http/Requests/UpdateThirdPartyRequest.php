<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateThirdPartyRequest extends FormRequest
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
            'is_customer' => 'boolean',
            'is_supplier' => 'boolean',
            'is_master_data' => 'boolean',
            'account_code' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'observations' => 'nullable|string',
            'is_active' => 'boolean',
            'attachments.*' => 'nullable|file|max:10240', // max 10MB per file
        ];
    }
}

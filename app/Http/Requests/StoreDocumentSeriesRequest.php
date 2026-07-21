<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentSeriesRequest extends FormRequest
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
            'document_type' => 'required|string|max:50',
            'identifier' => 'required|string|max:50',
            'description' => 'nullable|string',
            'current_number' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'company_id' => 'required|exists:companies,id',
        ];
    }
}

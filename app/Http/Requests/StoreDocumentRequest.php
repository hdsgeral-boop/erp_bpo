<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Delega a autorização real para a DocumentPolicy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png', // Limite de 10MB
            'document_type_id' => 'required|exists:document_types,id',
            'documentable_type' => 'required|string',
            'documentable_id' => 'required|integer',
        ];
    }
}

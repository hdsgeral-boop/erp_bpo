<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoogleOnboardingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Dados da Empresa
            'company_name' => ['required', 'string', 'max:255'],
            'company_nif' => ['required', 'string', 'max:30', 'unique:companies,nif'],
            'company_email' => ['nullable', 'email', 'max:255'],
            'company_phone' => ['nullable', 'string', 'max:30'],

            // Dados do Utilizador
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => 'O nome da empresa é obrigatório.',
            'company_nif.required' => 'O NIF da empresa é obrigatório.',
            'company_nif.unique' => 'Já existe uma empresa registada com este NIF. Por favor, verifique o NIF introduzido.',
            'name.required' => 'O nome do utilizador é obrigatório.',
            'phone.required' => 'O número de telefone é obrigatório.',
        ];
    }
}

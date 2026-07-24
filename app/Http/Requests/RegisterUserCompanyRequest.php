<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserCompanyRequest extends FormRequest
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
            'company_address' => ['nullable', 'string', 'max:500'],

            // Dados do Utilizador
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => 'O nome da empresa é obrigatório.',
            'company_nif.required' => 'O NIF da empresa é obrigatório.',
            'company_nif.unique' => 'Já existe uma empresa registada com este NIF. Por favor, verifique o NIF introduzido.',
            'company_email.email' => 'Introduza um e-mail de empresa válido.',
            
            'name.required' => 'O nome do utilizador é obrigatório.',
            'email.required' => 'O endereço de e-mail é obrigatório.',
            'email.email' => 'Introduza um endereço de e-mail válido.',
            'phone.required' => 'O número de telefone é obrigatório.',
            'password.required' => 'A palavra-passe é obrigatória.',
            'password.min' => 'A palavra-passe deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'A confirmação da palavra-passe não corresponde.',
        ];
    }
}

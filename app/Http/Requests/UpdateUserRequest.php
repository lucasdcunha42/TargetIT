<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'          => 'nullable|string|max:255',
            'email'         => 'nullable|email|unique:users,email,' . auth()->id(),
            'phone'         => 'nullable|string|max:15',
            'cpf'           => 'nullable|string|max:14',
            'logradouro'    => 'nullable|string|max:255',
            'numero'        => 'nullable|string|max:10',
            'bairro'        => 'nullable|string|max:255',
            'complemento'   => 'nullable|string|max:255',
            'cep'           => 'nullable|string|max:10',
        ];
    }

    public function messages()
    {
        return [
            'email.unique'  => 'O e-mail informado já está em uso.',
            'name.required' => 'O nome é obrigatório.',
            'phone.max'     => 'O telefone não pode ter mais de 15 caracteres.',
        ];
    }
}

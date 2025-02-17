<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'name'          => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('users')->ignore($this->user?->id),
            ],
            'phone'         => 'sometimes|string|max:15',
            'cpf' => [
            'sometimes',
            'string',
            'size:11',
            Rule::unique('users')->ignore($this->user?->id),
            ],
            'password'      => 'nullable|string|min:6',

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

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
            "name"      => "required",
            "email"     => "required|email|unique:users",
            "password"  => "required|min:6|confirmed",
        ];
    }
    public function messages(): array
    {
        return [
            'email.unique'  => 'O e-mail informado já está em uso.',
            'name.required' => 'O nome é obrigatório.',
            'password.min'  => 'A senha deve ter no mínimo 6 caracteres.',
            'password.confirmed' => 'As senhas não conferem.',
        ];
    }
}

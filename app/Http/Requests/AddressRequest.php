<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:10',
            'district' => 'required|string|max:255',
            'complement' => 'nullable|string|max:255',
            'zip_code' => 'required|string|max:10',
        ];
    }

    public function messages(): array
    {
        return [
            'street.required' => 'The street field is required.',
            'street.string' => 'The street must be a text.',
            'street.max' => 'The street may not be greater than 255 characters.',

            'number.required' => 'The number field is required.',
            'number.string' => 'The number must be a text.',
            'number.max' => 'The number may not be greater than 10 characters.',

            'district.required' => 'The district field is required.',
            'district.string' => 'The district must be a text.',
            'district.max' => 'The district may not be greater than 255 characters.',

            'complement.string' => 'The complement must be a text.',
            'complement.max' => 'The complement may not be greater than 255 characters.',

            'zip_code.required' => 'The zip code field is required.',
            'zip_code.string' => 'The zip code must be a text.',
            'zip_code.max' => 'The zip code may not be greater than 10 characters.',
        ];
    }
}

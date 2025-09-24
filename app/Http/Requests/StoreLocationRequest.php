<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLocationRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:locations,name',
            'address' => 'nullable|string|max:500',
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The location name is required.',
            'name.string'   => 'The location name must be a valid string.',
            'name.max'      => 'The location name must not exceed 255 characters.',
            'name.unique'   => 'This location name is already taken. Please choose another.',

            'address.string' => 'The address must be a valid string.',
            'address.max'    => 'The address must not exceed 500 characters.',
        ];
    }
}

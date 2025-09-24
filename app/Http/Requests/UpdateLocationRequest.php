<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLocationRequest extends FormRequest
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
        $locationId = $this->route('location')->id ?? null;

        return [
            'name' => 'required|string|max:255|unique:locations,name,' . $locationId,
            'address' => 'nullable|string|max:500',
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The location name is required.',
            'name.string'   => 'The location name must be a valid string.',
            'name.max'      => 'The location name may not exceed 255 characters.',
            'name.unique'   => 'This location name is already taken.',

            'address.string' => 'The address must be a valid string.',
            'address.max'    => 'The address may not exceed 500 characters.',
        ];
    }

    /**
     * Customize attribute names for error messages.
     */
    public function attributes(): array
    {
        return [
            'name' => 'Location Name',
            'address' => 'Location Address',
        ];
    }
}

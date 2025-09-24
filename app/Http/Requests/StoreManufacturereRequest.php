<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreManufacturereRequest extends FormRequest
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
            'name'          => 'required|string|max:255|unique:manufacturers,name',
            'url'           => 'nullable|url|max:255',
            'support_url'   => 'nullable|url|max:255',
            'support_phone' => 'nullable|string|max:50',
            'support_email' => 'nullable|email|max:255',
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'   => 'The manufacturer name is required.',
            'name.unique'     => 'This manufacturer name is already taken.',
            'url.url'         => 'The website URL must be a valid URL.',
            'support_url.url' => 'The support URL must be a valid URL.',
            'support_phone.max' => 'The support phone number may not be greater than 50 characters.',
            'support_email.email' => 'The support email must be a valid email address.',
        ];
    }
}

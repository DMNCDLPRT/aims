<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryrequest extends FormRequest
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
        $itemId = $this->route('category');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($itemId)],
            'description' => 'nullable | string | max:500',
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The category name is required.',
            'name.string'   => 'The category name must be a valid string.',
            'name.max'      => 'The category name may not exceed 255 characters.',
            'name.unique'   => 'This category name has already been taken.',

            'description.string' => 'The description must be a valid string.',
            'description.max'    => 'The description may not exceed 500 characters.',
        ];
    }

    /**
     * Custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'name' => 'Category Name',
            'description' => 'Category Description',
        ];
    }
}

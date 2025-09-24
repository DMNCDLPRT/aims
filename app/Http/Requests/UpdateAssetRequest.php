<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // allow updates, adjust if you add policies
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $assetId = $this->route('asset'); // assumes route-model binding or asset/{asset}

        return [
            'asset_tag'          => [
                'required',
                'string',
                'max:255',
                Rule::unique('assets', 'asset_tag')->ignore($assetId),
            ],
            'name'               => ['required', 'string', 'max:255'],
            'serial_number'      => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('assets', 'serial_number')->ignore($assetId),
            ],
            'model_name'         => ['nullable', 'string', 'max:255'],
            'purchase_date'      => ['nullable', 'date'],
            'purchase_price'     => ['nullable', 'numeric', 'min:0'],
            'status'             => ['required', 'string'], // can be validated against enum values if needed
            'notes'              => ['nullable', 'string'],
            'category_id'        => ['required', 'exists:categories,id'],
            'manufacturer_id'    => ['nullable', 'exists:manufacturers,id'],
            'location_id'        => ['nullable', 'exists:locations,id'],
            'assigned_to_user_id' => ['nullable', 'exists:users,id'],
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
            'asset_tag.required' => 'The asset tag is required.',
            'asset_tag.unique'   => 'This asset tag is already in use.',
            'name.required'      => 'The asset name is required.',
            'serial_number.unique' => 'This serial number is already assigned to another asset.',
            'purchase_date.date' => 'The purchase date must be a valid date.',
            'purchase_price.numeric' => 'The purchase price must be a number.',
            'purchase_price.min' => 'The purchase price cannot be negative.',
            'status.required'    => 'The asset status is required.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists'   => 'The selected category does not exist.',
            'manufacturer_id.exists' => 'The selected manufacturer does not exist.',
            'location_id.exists'     => 'The selected location does not exist.',
            'assigned_to_user_id.exists' => 'The selected user does not exist.',
        ];
    }
}

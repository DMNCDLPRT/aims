<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'asset_tag' => $this->asset_tag,
            'name' => $this->name,
            'serial_number' => $this->serial_number,
            'model_name' => $this->model_name,
            'purchase_date' => $this->purchase_date ? $this->purchase_date->toDateString() : null, // Format date
            'purchase_price' => $this->purchase_price,
            'status' => $this->status,
            'notes' => $this->notes,

            // Foreign key IDs
            'category_id' => $this->category_id,
            'manufacturer_id' => $this->manufacturer_id,
            'location_id' => $this->location_id,
            'assigned_to_user_id' => $this->assigned_to_user_id,

            // Related names
            'category' => [
                'name' => $this->category?->name,
            ],
            'manufacturer' => [
                'name' => $this->manufacturer?->name,
            ],
            'location' => [
                'name' => $this->location?->name,
            ],
            'assignedToUser' => $this->assignedToUser
                ? ['name' => $this->assignedToUser->name]
                : null,
        ];
    }
}

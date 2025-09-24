<?php

namespace App\Models;

use App\AssetStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_tag',
        'name',
        'serial_number',
        'model_name',
        'purchase_date',
        'purchase_price',
        'status',
        'notes',
        'category_id',
        'manufacturer_id',
        'location_id',
        'assigned_to_user_id',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
        'status' => AssetStatusEnum::class,
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function assignedToUser()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }
}

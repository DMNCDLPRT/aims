<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manufacturer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'support_email',
        'support_url',
        'support_phone',
    ];

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}

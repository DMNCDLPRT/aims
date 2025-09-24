<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manufacturer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'url', 'support_url', 'support_phone', 'support_email'];

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}

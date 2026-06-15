<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_name',
        'country_name',
        'lat',
        'lon',
        'rating',
        'image_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OurLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_name',
        'destination_id',
        'home_teaser',
        'home_image',
        'featured_on_home',
        'display_order'
    ];

    protected $casts = [
        'featured_on_home' => 'boolean',
        'display_order' => 'integer',
    ];
}

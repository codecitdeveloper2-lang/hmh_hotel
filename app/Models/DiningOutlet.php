<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiningOutlet extends Model
{
    protected $table = 'dining_outlets';

    protected $fillable = [
        'property_id',
        'name',
        'description',
        'slug',
        'cuisine_type',
        'opening_hours',
        'has_table_booking',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'cuisine_type' => 'array',
        'opening_hours' => 'array',
        'has_table_booking' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}

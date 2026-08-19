<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Attraction extends Model
{
    use HasFactory, HasSlug, HasTranslations;

    protected $translatable = ['name', 'description'];

    protected $fillable = [
        'property_id', 'name', 'description', 'slug',
        'distance_from_hotel', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class); // hotel rows only
    }
}

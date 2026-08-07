<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class City extends Model
{
    use HasFactory, HasSlug, HasTranslations;

    protected $translatable = ['name', 'description'];

    protected $fillable = [
        'destination_id', 'name', 'description', 'slug', 'latitude', 'longitude',
        'city_image', 'city_link', 'layout_type', 'sort_order', 'is_active', 'hotel_labels'
    ];

    protected $casts = [
        'hotel_labels' => 'array',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }
}

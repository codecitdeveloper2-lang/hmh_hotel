<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Attraction extends Model implements HasMedia
{
    use HasFactory, HasSlug, HasTranslations, InteractsWithMedia;

    protected $table = 'attractions';

    protected $translatable = ['name', 'description'];

    protected $fillable = [
        'property_id', 'name', 'description', 'slug',
        'distance_from_hotel', 'is_active', 'sort_order',
        'category', 'read_more_label', 'read_more_link',
        'address', 'google_maps_url',
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
        return $this->belongsTo(Property::class);
    }

    public function seoMetadata()
    {
        return $this->morphOne(\App\Models\SeoMetadata::class, 'seoable');
    }
}

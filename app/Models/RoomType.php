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

class RoomType extends Model implements HasMedia
{
    use HasFactory, HasSlug, HasTranslations, InteractsWithMedia;

    protected $translatable = ['name', 'description'];

    protected $fillable = [
        'property_id', 'name', 'description', 'slug', 'max_adults', 'max_children',
        'size_sqm', 'bed_type', 'travelclick_roomtype_id', 'is_active', 'sort_order',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}

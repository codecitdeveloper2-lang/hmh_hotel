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

    protected $table = 'room_types';

    protected $translatable = ['name', 'description', 'meta_title', 'meta_description'];

    protected $fillable = [
        'property_id', 'name', 'description', 'slug',
        'size_sqm', 'bed_type', 'travelclick_roomtype_id', 'is_active', 'sort_order',
        'meta_title', 'meta_description',
        'read_more_label', 'read_more_link', 'book_now_label', 'book_now_link',
        'starting_price', 'special_features',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'special_features' => 'array',
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

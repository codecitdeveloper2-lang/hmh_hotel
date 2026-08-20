<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Offer extends Model implements HasMedia
{
    use HasFactory, HasSlug, HasTranslations, InteractsWithMedia;

    protected $translatable = ['name', 'description', 'terms_conditions', 'details_content'];

    protected $fillable = [
        'name', 'description', 'terms_conditions', 'slug', 'identifier_code',
        'valid_from', 'valid_to', 'is_active', 'sort_order',
        'hotel', 'offer_type', 'status', 'booking_period', 'banner_image', 'images', 'details_content',
        'meta_title', 'meta_description', 'meta_keywords'
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_to' => 'date',
        'images' => 'array',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }

    // works identically for brand-level and hotel-level offers
    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'offer_property')
            ->withPivot('travelclick_rate_plan_id')
            ->withTimestamps();
    }
}

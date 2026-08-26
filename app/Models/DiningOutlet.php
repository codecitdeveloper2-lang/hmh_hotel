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

class DiningOutlet extends Model implements HasMedia
{
    use HasFactory, HasSlug, HasTranslations, InteractsWithMedia;

    protected $table = 'dining_outlets';

    protected $translatable = ['name', 'description', 'cuisine_type', 'opening_hours'];

    protected $fillable = [
        'property_id', 'name', 'description', 'slug', 'cuisine_type',
        'opening_hours', 'has_table_booking', 'is_active', 'sort_order',
        'read_more_label', 'read_more_link',
        'contact_details', 'book_table_label', 'book_table_link',
    ];

    protected $casts = [
        'has_table_booking' => 'boolean',
        'is_active' => 'boolean',
        'contact_details' => 'array',
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
        return $this->morphOne(SeoMetadata::class, 'seoable');
    }
}

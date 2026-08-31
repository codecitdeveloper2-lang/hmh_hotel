<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class MeetingEventPage extends Model
{
    use HasFactory, HasTranslations;

    protected $translatable = ['title', 'description', 'capacity_details', 'subtitle', 'details_content'];

    protected $fillable = [
        'property_id', 'type', 'title', 'description', 'capacity_details', 'is_active',
        'subtitle', 'rfp_url', 'banner_slides', 'event_cards', 'gallery', 'slug', 'status',
        'details_content', 'capacity_table', 'contact_details', 'image',
        'area_sqft', 'area_sqm', 'ceiling_height', 'highlights', 'capacities'
    ];

    protected $casts = [
        'banner_slides' => 'array',
        'event_cards' => 'array',
        'gallery' => 'array',
        'capacity_table' => 'array',
        'contact_details' => 'array',
        'highlights' => 'array',
        'capacities' => 'array',
        'is_active' => 'boolean',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function seoMetadata()
    {
        return $this->morphOne(\App\Models\SeoMetadata::class, 'seoable');
    }
}

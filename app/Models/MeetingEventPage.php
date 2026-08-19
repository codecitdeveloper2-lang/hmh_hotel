<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class MeetingEventPage extends Model
{
    use HasFactory, HasTranslations;

    protected $translatable = ['title', 'description', 'capacity_details', 'subtitle'];

    protected $fillable = [
        'property_id', 'type', 'title', 'description', 'capacity_details', 'is_active',
        'subtitle', 'rfp_url', 'banner_slides', 'event_cards', 'gallery', 'slug', 'status'
    ];

    protected $casts = [
        'banner_slides' => 'array',
        'event_cards' => 'array',
        'gallery' => 'array',
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

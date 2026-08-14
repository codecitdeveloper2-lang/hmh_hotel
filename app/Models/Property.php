<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    protected $guarded = [];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'meta_title' => 'array',
        'meta_description' => 'array',
        'intro_subtitle' => 'array',
        'intro_title' => 'array',
        'intro_text' => 'array',
        'banner_slides' => 'array',
        'banner_images' => 'array',
        // cover_image and logo are single file uploads (string), not arrays
        'star_rating' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the display name (English by default).
     */
    public function getDisplayNameAttribute(): string
    {
        $name = $this->name;
        if (is_array($name)) {
            return $name['en'] ?? $name['ar'] ?? '';
        }
        return $name ?? '';
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Property::class, 'parent_id');
    }
}

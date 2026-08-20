<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Page extends Model
{
    use HasFactory, HasSlug, HasTranslations;

    protected $translatable = ['title', 'body', 'meta_title', 'meta_description'];

    protected $fillable = [
        'property_id', 'page_type', 'slug', 'title', 'body',
        'meta_title', 'meta_description', 'is_active',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            // slug only needs to be unique within the same property (null = group-level)
            ->extraScope(fn ($query) => $query->where('property_id', $this->property_id));
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class); // nullable = group-level page
    }

    protected static function booted()
    {
        static::saved(function ($page) {
            \Illuminate\Support\Facades\Cache::forget('pages:index:en');
            \Illuminate\Support\Facades\Cache::forget('pages:index:ar');
            \Illuminate\Support\Facades\Cache::forget("pages:show:{$page->slug}:en");
            \Illuminate\Support\Facades\Cache::forget("pages:show:{$page->slug}:ar");
        });

        static::deleted(function ($page) {
            \Illuminate\Support\Facades\Cache::forget('pages:index:en');
            \Illuminate\Support\Facades\Cache::forget('pages:index:ar');
            \Illuminate\Support\Facades\Cache::forget("pages:show:{$page->slug}:en");
            \Illuminate\Support\Facades\Cache::forget("pages:show:{$page->slug}:ar");
        });
    }
}


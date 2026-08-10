<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class NewsPost extends Model implements HasMedia
{
    use HasFactory, HasSlug, HasTranslations, InteractsWithMedia;

    protected $translatable = ['title', 'excerpt', 'body', 'meta_title', 'meta_description'];

    protected $fillable = [
        'channel', 'title', 'excerpt', 'body', 'slug', 'published_at',
        'meta_title', 'meta_description', 'is_active',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('title')->saveSlugsTo('slug');
    }

    public function scopeNews($query)
    {
        return $query->where('channel', 'news');
    }

    public function scopePressReleases($query)
    {
        return $query->where('channel', 'press-release');
    }
}

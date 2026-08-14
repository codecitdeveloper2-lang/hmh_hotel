<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class GalleryItem extends Model implements HasMedia
{
    use HasFactory, HasTranslations, InteractsWithMedia;

    protected $translatable = ['caption'];

    protected $fillable = ['property_id', 'caption', 'sort_order'];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class); // nullable = group-level gallery
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class GalleryItem extends Model
{
    use HasTranslations;

    protected $guarded = [];

    public $translatable = ['caption'];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}

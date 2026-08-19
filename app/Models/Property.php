<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Property extends Model implements HasMedia
{
    use HasFactory, HasSlug, HasTranslations, InteractsWithMedia;

    protected $translatable = ['name', 'description', 'meta_title', 'meta_description'];

    protected $fillable = [
        'parent_id', 'type', 'name', 'description', 'slug', 'star_rating',
        'address', 'city', 'country', 'latitude', 'longitude', 'phone', 'email',
        'travelclick_hotel_id', 'attractions_page_slug', 'status',
        'check_in_time', 'check_out_time', 'meta_title', 'meta_description',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_active' => 'boolean',
        'star_rating' => 'integer',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    // slug is unique across the WHOLE table (brand + hotel share one flat namespace)
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    // --- self-referencing brand/hotel relationship ---

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'parent_id');
    }
    /**
     * Get the display name (English by default).
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->belongsTo(Property::class, 'parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'parent_id');
    }

    public function hotels(): HasMany
    {
        return $this->hasMany(Property::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Property::class, 'parent_id');
    }

    public function scopeBrands($query)
    {
        return $query->where('type', 'brand');
    }

    public function scopeHotels($query)
    {
        return $query->where('type', 'hotel');
    }

    // --- child content ---

    public function roomTypes(): HasMany
    {
        return $this->hasMany(RoomType::class); // hotel rows only
    }

    public function diningOutlets(): HasMany
    {
        return $this->hasMany(DiningOutlet::class); // brand OR hotel
    }

    public function attractions(): HasMany
    {
        return $this->hasMany(Attraction::class); // hotel rows only
    }

    public function faqItems(): HasMany
    {
        return $this->hasMany(FaqItem::class);
    }

    public function meetingEventPages(): HasMany
    {
        return $this->hasMany(MeetingEventPage::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    public function galleryItems(): HasMany
    {
        return $this->hasMany(GalleryItem::class);
    }

    public function offers(): BelongsToMany
    {
        return $this->belongsToMany(Offer::class, 'offer_property')
            ->withPivot('travelclick_rate_plan_id')
            ->withTimestamps();
    }

    // --- row-level access control ---

    public function assignedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'property_user')->withPivot('role')->withTimestamps();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
        $this->addMediaCollection('hero_images');
        $this->addMediaCollection('gallery');
    }

    public function getDisplayNameAttribute(): string
    {
        $name = $this->name;
        if (is_array($name)) {
            return $name['en'] ?? $name['ar'] ?? '';
        }
        return $name ?? '';
    }
}

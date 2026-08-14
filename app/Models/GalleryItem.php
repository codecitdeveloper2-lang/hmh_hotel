<?php

namespace App\Models;

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

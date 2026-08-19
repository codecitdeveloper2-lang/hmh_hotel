<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class MeetingEventPage extends Model
{
    use HasFactory, HasTranslations;

    protected $translatable = ['title', 'description', 'capacity_details'];

    protected $fillable = ['property_id', 'type', 'title', 'description', 'capacity_details', 'is_active'];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class); // brand OR hotel
    }
}

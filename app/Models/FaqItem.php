<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class FaqItem extends Model
{
    use HasFactory, HasTranslations;

    protected $table = 'faq_items';

    protected $translatable = ['question', 'answer'];

    protected $fillable = ['property_id', 'question', 'answer', 'sort_order'];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}

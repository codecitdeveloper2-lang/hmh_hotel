<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class FaqItem extends Model
{
    use HasFactory, HasTranslations;

    protected $translatable = ['question', 'answer'];

    protected $fillable = ['property_id', 'question', 'answer', 'sort_order'];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class); // brand OR hotel
use Illuminate\Database\Eloquent\Model;

class FaqItem extends Model
{
    protected $table = 'faq_items';

    protected $fillable = [
        'property_id',
        'question',
        'answer',
        'sort_order',
    ];

    protected $casts = [
        'question' => 'array',
        'answer' => 'array',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}

<?php

namespace App\Models;

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

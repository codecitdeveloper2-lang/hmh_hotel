<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactSubmission extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $guarded = [];

    protected $casts = [
        'title' => 'array',
        'body' => 'array',
        'meta_title' => 'array',
        'meta_description' => 'array',
    ];
}

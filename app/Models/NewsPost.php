<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class NewsPost extends Model
{
    use HasTranslations;

    protected $guarded = [];

    public $translatable = ['title', 'excerpt', 'body', 'meta_title', 'meta_description'];
}

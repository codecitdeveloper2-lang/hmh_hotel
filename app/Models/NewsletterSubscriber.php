<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['email', 'locale', 'is_active', 'subscribed_at'];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'is_active' => 'boolean',
    ];
    protected $guarded = [];

    public $timestamps = false;
}

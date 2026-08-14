<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    protected $guarded = [];

    protected $hidden = ['password', 'remember_token'];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}

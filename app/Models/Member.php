<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Member extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'password',
        'country', 'loyalty_tier', 'loyalty_points',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

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

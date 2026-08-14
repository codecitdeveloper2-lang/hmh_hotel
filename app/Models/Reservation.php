<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id', 'property_id', 'confirmation_number', 'travelclick_reservation_id',
        'check_in', 'check_out', 'adults', 'children', 'rooms', 'rate_plan_id',
        'status', 'total_amount', 'currency', 'raw_payload',
    ];
    protected $guarded = [];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'raw_payload' => 'array',
        'total_amount' => 'decimal:2',
    ];

    // local cache/mirror only — TravelClick/Amadeus remains the source of truth

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
        'total_amount' => 'decimal:2',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}

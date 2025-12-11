<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlightLandingFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'scheduled_flight_id',
        'airport_id',
        'mtow_used_kg',
        'fee_amount',
        'currency',
        'notes',
    ];

    protected $casts = [
        'mtow_used_kg' => 'decimal:2',
        'fee_amount' => 'decimal:2',
    ];

    /**
     * Get the scheduled flight that owns this landing fee.
     */
    public function scheduledFlight(): BelongsTo
    {
        return $this->belongsTo(ScheduledFlight::class);
    }

    /**
     * Get the airport where this landing fee was incurred.
     */
    public function airport(): BelongsTo
    {
        return $this->belongsTo(Airport::class);
    }
}

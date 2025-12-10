<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class FlightScheduleRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'scheduled_flight_id',
        'route_order',
        'origin_airport_id',
        'destination_airport_id',
        'actual_departure_time',
        'actual_arrival_time',
        'notes',
    ];

    protected $casts = [
        'actual_departure_time' => 'datetime',
        'actual_arrival_time' => 'datetime',
        'route_order' => 'integer',
    ];

    /**
     * Get the scheduled flight that owns this route segment.
     */
    public function scheduledFlight(): BelongsTo
    {
        return $this->belongsTo(ScheduledFlight::class);
    }

    /**
     * Get the origin airport for this route segment.
     */
    public function originAirport(): BelongsTo
    {
        return $this->belongsTo(Airport::class, 'origin_airport_id');
    }

    /**
     * Get the destination airport for this route segment.
     */
    public function destinationAirport(): BelongsTo
    {
        return $this->belongsTo(Airport::class, 'destination_airport_id');
    }


    /**
     * Get route segment display name.
     */
    public function getRouteSegmentDisplayAttribute(): string
    {
        $origin = $this->originAirport?->code ?? 'N/A';
        $destination = $this->destinationAirport?->code ?? 'N/A';
        return "{$origin} → {$destination}";
    }

    /**
     * Scope to order by route order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('route_order');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledFlight extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_code',
        'aircraft_id',
        'flight_status_id',
        'flight_type',
        'primary_customer_id',
        'total_departure_time',
        'total_arrival_time',
        'total_duration_minutes',
        'total_distance_km',
        'total_segments',
        'price',
        'passenger_count',
        'total_cargo_weight_kg',
        'cargo_items_count',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'total_departure_time' => 'datetime',
        'total_arrival_time' => 'datetime',
        'total_duration_minutes' => 'integer',
        'total_distance_km' => 'decimal:2',
        'total_segments' => 'integer',
        'price' => 'decimal:2',
        'passenger_count' => 'integer',
        'total_cargo_weight_kg' => 'decimal:2',
        'cargo_items_count' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($flight) {
            if (!$flight->flight_code) {
                $flight->flight_code = static::generateFlightCode();
            }

            // Note: capacity and available_seats are now calculated dynamically from aircraft data
        });
    }

    /**
     * Get the aircraft for this scheduled flight.
     */
    public function aircraft(): BelongsTo
    {
        return $this->belongsTo(Aircraft::class);
    }

    /**
     * Get the flight status for this scheduled flight.
     */
    public function flightStatus(): BelongsTo
    {
        return $this->belongsTo(FlightStatus::class);
    }

    /**
     * Get the primary customer for this scheduled flight.
     */
    public function primaryCustomer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'primary_customer_id');
    }

    /**
     * Get all route segments for this scheduled flight.
     */
    public function scheduleRoutes(): HasMany
    {
        return $this->hasMany(FlightScheduleRoute::class)->orderBy('route_order');
    }

    /**
     * Get all passengers for this scheduled flight.
     */
    public function passengers(): HasMany
    {
        return $this->hasMany(FlightPassenger::class);
    }

    /**
     * Get all cargo items for this scheduled flight.
     */
    public function cargo(): HasMany
    {
        return $this->hasMany(FlightCargo::class);
    }

    /**
     * Get all landing fees for this scheduled flight.
     */
    public function landingFees(): HasMany
    {
        return $this->hasMany(FlightLandingFee::class);
    }

    /**
     * Get all fuel consumption records for this scheduled flight.
     */
    public function fuelConsumption(): HasMany
    {
        return $this->hasMany(FlightFuelConsumption::class);
    }

    /**
     * Get the first route segment (origin).
     */
    public function firstRoute()
    {
        return $this->scheduleRoutes()->where('route_order', 1)->first();
    }

    /**
     * Get the last route segment (final destination).
     */
    public function lastRoute()
    {
        return $this->scheduleRoutes()->orderBy('route_order', 'desc')->first();
    }

    /**
     * Get the origin airport (from first route segment).
     */
    public function getOriginAirportAttribute()
    {
        return $this->firstRoute()?->originAirport;
    }

    /**
     * Get the destination airport (from last route segment).
     */
    public function getDestinationAirportAttribute()
    {
        return $this->lastRoute()?->destinationAirport;
    }

    /**
     * Scope to get only active flights.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get flights departing today.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('total_departure_time', today());
    }

    /**
     * Scope to get upcoming flights.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('total_departure_time', '>=', now());
    }

    public static function generateFlightCode()
    {
        do {
            $number = str_pad(rand(100, 9999), 3, '0', STR_PAD_LEFT);
            $code = 'FZ' . $number;
        } while (static::where('flight_code', $code)->exists());

        return $code;
    }

    /**
     * Get the full route display string showing all segments.
     */
    public function getRouteDisplayAttribute()
    {
        $routes = $this->scheduleRoutes;
        if ($routes->isEmpty()) {
            return 'No routes defined';
        }

        if ($routes->count() === 1) {
            $route = $routes->first();
            return ($route->originAirport?->code ?? 'N/A') . ' → ' . ($route->destinationAirport?->code ?? 'N/A');
        }

        // Multi-segment route
        $segments = [];
        foreach ($routes as $route) {
            $segments[] = $route->originAirport?->code ?? 'N/A';
        }
        // Add final destination
        $segments[] = $routes->last()->destinationAirport?->code ?? 'N/A';

        return implode(' → ', $segments);
    }

    /**
     * Get simplified route display (origin to final destination).
     */
    public function getSimpleRouteDisplayAttribute()
    {
        $origin = $this->originAirport?->code ?? 'N/A';
        $destination = $this->destinationAirport?->code ?? 'N/A';

        if ($this->total_segments > 1) {
            return "{$origin} → {$destination} ({$this->total_segments} stops)";
        }

        return "{$origin} → {$destination}";
    }

    /**
     * Get the flight status color.
     */
    public function getStatusColorAttribute()
    {
        return $this->flightStatus->color ?? '#6c757d';
    }

    /**
     * Get formatted duration string.
     */
    public function getFormattedDurationAttribute()
    {
        if ($this->total_duration_minutes) {
            $hours = intval($this->total_duration_minutes / 60);
            $mins = $this->total_duration_minutes % 60;
            return sprintf('%dh %02dm', $hours, $mins);
        }
        return 'N/A';
    }

    /**
     * Calculate and update total values from route segments.
     */
    public function calculateTotals()
    {
        $routes = $this->scheduleRoutes;

        if ($routes->isEmpty()) {
            return;
        }

        // Only update segment count - departure and arrival times are set from form
        $this->total_segments = $routes->count();

        // Note: duration_minutes and distance_km are no longer stored in routes
        // These would need to be calculated based on airport coordinates and flight plans

        // Update passenger and cargo counts
        $this->updatePassengerAndCargoStats();

        $this->save();
    }

    /**
     * Update passenger and cargo statistics.
     */
    public function updatePassengerAndCargoStats()
    {
        $this->passenger_count = $this->passengers()->count();
        $this->cargo_items_count = $this->cargo()->count();
        $this->total_cargo_weight_kg = $this->cargo()->sum('weight_kg');
    }

    /**
     * Check if flight is passenger type.
     */
    public function isPassengerFlight(): bool
    {
        return in_array($this->flight_type, ['passenger', 'mixed']);
    }

    /**
     * Check if flight carries cargo.
     */
    public function isCargoFlight(): bool
    {
        return in_array($this->flight_type, ['cargo', 'mixed']);
    }

    /**
     * Get passenger breakdown by type.
     */
    public function getPassengerBreakdownAttribute(): array
    {
        if (!$this->isPassengerFlight()) {
            return [];
        }

        return [
            'adults' => $this->passengers()->byPassengerType('adult')->count(),
            'children' => $this->passengers()->byPassengerType('child')->count(),
            'infants' => $this->passengers()->byPassengerType('infant')->count(),
            'checked_in' => $this->passengers()->checkedIn()->count(),
        ];
    }

    /**
     * Get cargo breakdown by status.
     */
    public function getCargoBreakdownAttribute(): array
    {
        if (!$this->isCargoFlight()) {
            return [];
        }

        return [
            'total_items' => $this->cargo_items_count,
            'total_weight' => $this->total_cargo_weight_kg,
            'pending' => $this->cargo()->byStatus('pending')->count(),
            'loaded' => $this->cargo()->byStatus('loaded')->count(),
            'hazardous' => $this->cargo()->hazardous()->count(),
            'refrigerated' => $this->cargo()->requiresRefrigeration()->count(),
        ];
    }

    /**
     * Check if flight is overbooked.
     */
    public function isOverbooked(): bool
    {
        return $this->passenger_count > $this->capacity;
    }

    /**
     * Get aircraft capacity (dynamic from aircraft data).
     */
    public function getCapacityAttribute(): int
    {
        return $this->aircraft?->max_passengers ?? 0;
    }

    /**
     * Get available seats count.
     */
    public function getAvailableSeatsAttribute(): int
    {
        return max(0, $this->capacity - $this->passenger_count);
    }

    /**
     * Get occupancy percentage.
     */
    public function getOccupancyPercentageAttribute(): int
    {
        if (!$this->capacity) {
            return 0;
        }
        return min(100, round(($this->passenger_count / $this->capacity) * 100));
    }
}

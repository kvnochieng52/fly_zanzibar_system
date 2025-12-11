<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlightFuelConsumption extends Model
{
    use HasFactory;

    protected $table = 'flight_fuel_consumption';

    protected $fillable = [
        'scheduled_flight_id',
        'fuel_consumed',
        'fuel_unit',
        'distance_km',
        'flight_time_hours',
        'bloc_time_hours',
        'total_time_hours',
        'fuel_burn_rate',
        'notes',
    ];

    protected $casts = [
        'fuel_consumed' => 'decimal:2',
        'distance_km' => 'decimal:2',
        'flight_time_hours' => 'decimal:2',
        'bloc_time_hours' => 'decimal:2',
        'total_time_hours' => 'decimal:2',
        'fuel_burn_rate' => 'decimal:2',
    ];

    /**
     * Get the scheduled flight that owns this fuel consumption record.
     */
    public function scheduledFlight(): BelongsTo
    {
        return $this->belongsTo(ScheduledFlight::class);
    }

    /**
     * Calculate fuel consumption efficiency.
     */
    public function getFuelEfficiencyAttribute(): ?float
    {
        if (!$this->distance_km || !$this->fuel_consumed) {
            return null;
        }

        // Fuel efficiency in kilometers per unit of fuel
        return $this->distance_km / $this->fuel_consumed;
    }

    /**
     * Get formatted fuel consumption with unit.
     */
    public function getFormattedFuelConsumedAttribute(): string
    {
        return number_format($this->fuel_consumed, 2) . ' ' . $this->fuel_unit;
    }

    /**
     * Get formatted total time as HH:MM.
     */
    public function getFormattedTotalTimeAttribute(): ?string
    {
        if (!$this->total_time_hours) {
            return null;
        }

        $totalMinutes = (int)($this->total_time_hours * 60);
        $hours = intval($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        return sprintf('%02d:%02d', $hours, $minutes);
    }
}

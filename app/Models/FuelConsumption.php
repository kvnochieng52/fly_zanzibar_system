<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuelConsumption extends Model
{
    use HasFactory;

    protected $table = 'fuel_consumption';

    protected $fillable = [
        'flight_number',
        'flight_date',
        'aircraft_id',
        'route_from',
        'route_to',
        'fuel_consumed',
        'fuel_unit_id',
        'flight_hours',
        'distance_km',
        'average_fuel_flow',
        'fuel_efficiency',
        'weather_conditions',
        'passenger_count',
        'cargo_weight',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'flight_date' => 'date',
        'fuel_consumed' => 'decimal:2',
        'flight_hours' => 'decimal:2',
        'distance_km' => 'decimal:2',
        'average_fuel_flow' => 'decimal:2',
        'fuel_efficiency' => 'decimal:4',
        'cargo_weight' => 'decimal:2',
        'passenger_count' => 'integer'
    ];

    public function aircraft(): BelongsTo
    {
        return $this->belongsTo(Aircraft::class);
    }

    public function fuelUnit(): BelongsTo
    {
        return $this->belongsTo(FuelUnit::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

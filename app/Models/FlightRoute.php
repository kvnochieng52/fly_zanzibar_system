<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'origin_airport_id',
        'destination_airport_id',
        'distance_km',
        'duration_minutes',
        'base_price',
        'description',
        'is_active',
    ];

    protected $casts = [
        'distance_km' => 'decimal:2',
        'base_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function originAirport()
    {
        return $this->belongsTo(Airport::class, 'origin_airport_id');
    }

    public function destinationAirport()
    {
        return $this->belongsTo(Airport::class, 'destination_airport_id');
    }

    public function scheduledFlights()
    {
        return $this->hasMany(ScheduledFlight::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getRouteDisplayAttribute()
    {
        if ($this->originAirport && $this->destinationAirport) {
            return $this->originAirport->code . ' → ' . $this->destinationAirport->code;
        }
        return 'N/A';
    }

    public function getFullRouteNameAttribute()
    {
        if ($this->originAirport && $this->destinationAirport) {
            return $this->originAirport->name . ' to ' . $this->destinationAirport->name;
        }
        return 'Unknown Route';
    }

    public static function generateRouteCode($originCode, $destinationCode)
    {
        return strtoupper($originCode . $destinationCode);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Airport extends Model
{
    use HasFactory;

    protected $fillable = [
        'icao_code',
        'iata_code',
        'name',
        'city',
        'country',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    protected $appends = [
        'code'
    ];

    public function landingFees(): HasMany
    {
        return $this->hasMany(LandingFee::class);
    }

    public function fuelPurchases(): HasMany
    {
        return $this->hasMany(FuelPurchase::class);
    }

    public function fuelAccountBalances(): HasMany
    {
        return $this->hasMany(FuelAccountBalance::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the airport code (prefer IATA, fallback to ICAO).
     */
    public function getCodeAttribute()
    {
        return $this->iata_code ?: $this->icao_code;
    }
}

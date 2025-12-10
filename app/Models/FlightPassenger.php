<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlightPassenger extends Model
{
    use HasFactory;

    protected $fillable = [
        'scheduled_flight_id',
        'customer_name',
        'first_name',
        'last_name',
        'id_number',
        'id_type',
        'nationality',
        'date_of_birth',
        'gender',
        'phone_number',
        'email',
        'special_requirements',
        'passenger_type',
        'seat_preference',
        'checked_in',
        'check_in_time',
        'notes'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'checked_in' => 'boolean',
        'check_in_time' => 'datetime'
    ];

    protected $appends = ['full_name'];

    public function scheduledFlight(): BelongsTo
    {
        return $this->belongsTo(ScheduledFlight::class);
    }

    public function getFullNameAttribute(): string
    {
        // Use customer_name if available, otherwise fallback to first_name + last_name for backward compatibility
        return $this->customer_name ?: trim("{$this->first_name} {$this->last_name}");
    }

    public function getAgeAttribute(): ?int
    {
        if (!$this->date_of_birth) {
            return null;
        }

        return $this->date_of_birth->diffInYears(now());
    }

    public function scopeCheckedIn($query)
    {
        return $query->where('checked_in', true);
    }

    public function scopeByPassengerType($query, string $type)
    {
        return $query->where('passenger_type', $type);
    }

    public function scopeByNationality($query, string $nationality)
    {
        return $query->where('nationality', $nationality);
    }

    public function checkIn(): void
    {
        $this->update([
            'checked_in' => true,
            'check_in_time' => now()
        ]);
    }

    public function isMinor(): bool
    {
        return in_array($this->passenger_type, ['child', 'infant']);
    }

    public function hasSpecialRequirements(): bool
    {
        return !empty($this->special_requirements);
    }
}
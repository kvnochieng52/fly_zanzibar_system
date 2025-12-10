<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlightCargo extends Model
{
    use HasFactory;

    protected $table = 'flight_cargo';

    protected $fillable = [
        'scheduled_flight_id',
        'cargo_type',
        'description',
        'weight_kg',
        'volume_m3',
        'dimensions',
        'declared_value',
        'currency',
        'shipper_name',
        'shipper_contact',
        'consignee_name',
        'consignee_contact',
        'special_handling_instructions',
        'requires_refrigeration',
        'hazardous',
        'tracking_number',
        'status',
        'notes'
    ];

    protected $casts = [
        'weight_kg' => 'decimal:2',
        'volume_m3' => 'decimal:3',
        'declared_value' => 'decimal:2',
        'requires_refrigeration' => 'boolean',
        'hazardous' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        // Auto-generate tracking number if not provided
        static::creating(function ($cargo) {
            if (empty($cargo->tracking_number)) {
                $cargo->tracking_number = 'TRK-' . strtoupper(uniqid());
            }
        });
    }

    public function scheduledFlight(): BelongsTo
    {
        return $this->belongsTo(ScheduledFlight::class);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByCargoType($query, string $type)
    {
        return $query->where('cargo_type', $type);
    }

    public function scopeHazardous($query)
    {
        return $query->where('hazardous', true);
    }

    public function scopeRequiresRefrigeration($query)
    {
        return $query->where('requires_refrigeration', true);
    }

    public function getFormattedWeightAttribute(): string
    {
        return number_format($this->weight_kg, 2) . ' kg';
    }

    public function getFormattedVolumeAttribute(): string
    {
        if (!$this->volume_m3) {
            return 'N/A';
        }
        return number_format($this->volume_m3, 3) . ' m³';
    }

    public function getFormattedValueAttribute(): string
    {
        if (!$this->declared_value) {
            return 'Not declared';
        }
        return $this->currency . ' ' . number_format($this->declared_value, 2);
    }

    public function isHighValue(): bool
    {
        return $this->declared_value && $this->declared_value > 10000;
    }

    public function requiresSpecialHandling(): bool
    {
        return $this->hazardous ||
               $this->requires_refrigeration ||
               !empty($this->special_handling_instructions) ||
               $this->isHighValue();
    }

    public function updateStatus(string $status, string $notes = ''): void
    {
        $this->update([
            'status' => $status,
            'notes' => $notes ? $this->notes . "\n" . now()->format('Y-m-d H:i') . ": " . $notes : $this->notes
        ]);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'loaded' => 'info',
            'in_transit' => 'primary',
            'delivered' => 'success',
            'damaged' => 'danger',
            default => 'secondary'
        };
    }
}
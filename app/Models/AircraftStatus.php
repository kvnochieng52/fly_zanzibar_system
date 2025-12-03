<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AircraftStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'color',
        'allows_operation',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'allows_operation' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    // Relationships
    public function aircraft(): HasMany
    {
        return $this->hasMany(Aircraft::class, 'status_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeOperational($query)
    {
        return $query->where('allows_operation', true);
    }

    // Constants for common status codes
    public const IN_SERVICE = 'IN_SERVICE';
    public const MAINTENANCE = 'MAINTENANCE';
    public const AOG = 'AOG';
    public const RETIRED = 'RETIRED';

    // Helper methods
    public static function getInServiceId(): ?int
    {
        return self::where('code', self::IN_SERVICE)->value('id');
    }

    public static function getMaintenanceId(): ?int
    {
        return self::where('code', self::MAINTENANCE)->value('id');
    }

    public static function getAOGId(): ?int
    {
        return self::where('code', self::AOG)->value('id');
    }

    public static function getRetiredId(): ?int
    {
        return self::where('code', self::RETIRED)->value('id');
    }
}

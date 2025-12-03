<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AircraftDocumentStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'color',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    // Relationships
    public function documents(): HasMany
    {
        return $this->hasMany(AircraftDocument::class, 'document_status_id');
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

    // Constants for common status codes
    public const VALID = 'VALID';
    public const EXPIRING = 'EXPIRING';
    public const EXPIRED = 'EXPIRED';
    public const PENDING = 'PENDING';

    // Helper methods
    public static function getValidId(): ?int
    {
        return self::where('code', self::VALID)->value('id');
    }

    public static function getExpiringId(): ?int
    {
        return self::where('code', self::EXPIRING)->value('id');
    }

    public static function getExpiredId(): ?int
    {
        return self::where('code', self::EXPIRED)->value('id');
    }

    public static function getPendingId(): ?int
    {
        return self::where('code', self::PENDING)->value('id');
    }
}

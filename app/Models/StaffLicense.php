<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class StaffLicense extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'license_type_id',
        'license_type', // Keep for backward compatibility until migration
        'license_number',
        'issuing_authority',
        'issue_date',
        'expiry_date',
        'restrictions',
        'notes',
        'document_file',
        'license_status_id',
        'status', // Keep for backward compatibility until migration
        'is_current',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'is_current' => 'boolean',
    ];

    // Relationships
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function licenseType(): BelongsTo
    {
        return $this->belongsTo(LicenseType::class);
    }

    public function licenseStatus(): BelongsTo
    {
        return $this->belongsTo(LicenseStatus::class);
    }

    public function typeRatings(): HasMany
    {
        return $this->hasMany(StaffTypeRating::class);
    }

    // Accessors
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'expired' => 'danger',
            'suspended' => 'warning',
            'revoked' => 'dark',
            default => 'secondary'
        };
    }

    public function getIsExpiredAttribute(): bool
    {
        if (!$this->expiry_date) {
            return false; // No expiry date means permanent
        }
        return Carbon::now()->gt($this->expiry_date);
    }

    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->expiry_date) {
            return null; // No expiry
        }
        return Carbon::now()->diffInDays($this->expiry_date, false);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiry_date', '<=', Carbon::now()->addDays($days))
                     ->where('expiry_date', '>', Carbon::now());
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($license) {
            // Auto-update status based on expiry date
            if ($license->expiry_date && Carbon::now()->gt($license->expiry_date)) {
                $license->status = 'expired';
            }

            // Ensure only one current license per type per staff
            if ($license->is_current) {
                static::where('staff_id', $license->staff_id)
                      ->where('license_type', $license->license_type)
                      ->where('id', '!=', $license->id)
                      ->update(['is_current' => false]);
            }
        });
    }
}
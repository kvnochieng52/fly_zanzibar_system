<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class StaffTypeRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'aircraft_type',
        'rating_type',
        'issue_date',
        'expiry_date',
        'issuing_authority',
        'limitations',
        'notes',
        'document_file',
        'is_current',
        'status'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'is_current' => 'boolean'
    ];

    /**
     * Relationship to staff member
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    /**
     * Get days until expiry
     */
    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->expiry_date) {
            return null;
        }

        return Carbon::now()->diffInDays($this->expiry_date, false);
    }

    /**
     * Get status color for badges
     */
    public function getStatusColorAttribute(): string
    {
        if (!$this->expiry_date) {
            return 'secondary';
        }

        $daysUntilExpiry = $this->days_until_expiry;

        if ($daysUntilExpiry < 0) {
            return 'danger';   // Expired
        } elseif ($daysUntilExpiry < 30) {
            return 'warning';  // Expiring soon
        } elseif ($daysUntilExpiry < 90) {
            return 'info';     // Due within 3 months
        } else {
            return 'success';  // Current
        }
    }

    /**
     * Auto-update status based on expiry date
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($typeRating) {
            if ($typeRating->expiry_date) {
                $daysUntilExpiry = Carbon::now()->diffInDays($typeRating->expiry_date, false);

                if ($daysUntilExpiry < 0) {
                    $typeRating->status = 'expired';
                } elseif ($daysUntilExpiry < 30) {
                    $typeRating->status = 'expiring_soon';
                } else {
                    $typeRating->status = 'active';
                }
            } else {
                $typeRating->status = 'active';
            }

            // Ensure only one current rating per aircraft type for the staff member
            if ($typeRating->is_current) {
                static::where('staff_id', $typeRating->staff_id)
                    ->where('aircraft_type', $typeRating->aircraft_type)
                    ->where('id', '!=', $typeRating->id ?? 0)
                    ->update(['is_current' => false]);
            }
        });
    }
}

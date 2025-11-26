<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class EmploymentContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'contract_file',
        'start_date',
        'end_date',
        'contract_type',
        'status',
        'notes',
        'is_current',
        'uploaded_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    // Relationships
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    // Accessors
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'expired' => 'danger',
            'terminated' => 'warning',
            'pending' => 'info',
            default => 'secondary'
        };
    }

    public function getIsExpiredAttribute(): bool
    {
        if (!$this->end_date) {
            return false; // Permanent contracts don't expire
        }
        return Carbon::now()->gt($this->end_date);
    }

    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->end_date) {
            return null; // Permanent contracts don't expire
        }
        return Carbon::now()->diffInDays($this->end_date, false);
    }

    // Scopes
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
                     ->orWhere(function($q) {
                         $q->where('end_date', '<', Carbon::now())
                           ->where('status', 'active');
                     });
    }

    // Boot method to automatically update status based on dates
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($contract) {
            // Auto-update status based on dates
            if ($contract->end_date && Carbon::now()->gt($contract->end_date)) {
                $contract->status = 'expired';
            } elseif ($contract->start_date && Carbon::now()->gte($contract->start_date) && $contract->status === 'pending') {
                $contract->status = 'active';
            }

            // Ensure only one current contract per staff
            if ($contract->is_current) {
                static::where('staff_id', $contract->staff_id)
                      ->where('id', '!=', $contract->id)
                      ->update(['is_current' => false]);
            }
        });
    }
}

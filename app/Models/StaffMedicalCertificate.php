<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class StaffMedicalCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'medical_class_id',
        'certificate_number',
        'issue_date',
        'expiry_date',
        'examining_doctor',
        'examining_facility',
        'medical_status_id',
        'limitations',
        'notes',
        'document_file',
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

    public function medicalClass(): BelongsTo
    {
        return $this->belongsTo(MedicalClass::class);
    }

    public function medicalStatus(): BelongsTo
    {
        return $this->belongsTo(MedicalStatus::class);
    }

    // Accessors
    public function getStatusColorAttribute(): string
    {
        $status = $this->medicalStatus->name ?? 'unknown';
        return match(strtolower($status)) {
            'valid' => 'success',
            'expired' => 'danger',
            'suspended' => 'warning',
            'revoked' => 'dark',
            default => 'secondary'
        };
    }

    public function getIsExpiredAttribute(): bool
    {
        return Carbon::now()->gt($this->expiry_date);
    }

    public function getDaysUntilExpiryAttribute(): int
    {
        return Carbon::now()->diffInDays($this->expiry_date, false);
    }

    // Scopes
    public function scopeValid($query)
    {
        return $query->whereHas('medicalStatus', function($q) {
            $q->where('name', 'valid');
        });
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

        static::saving(function ($medical) {
            // Auto-update status based on expiry date
            if ($medical->expiry_date && Carbon::now()->gt($medical->expiry_date)) {
                // Find the "expired" status ID
                $expiredStatus = \App\Models\MedicalStatus::where('name', 'expired')->first();
                if ($expiredStatus) {
                    $medical->medical_status_id = $expiredStatus->id;
                }
            }

            // Ensure only one current medical per class per staff
            if ($medical->is_current) {
                static::where('staff_id', $medical->staff_id)
                      ->where('medical_class_id', $medical->medical_class_id)
                      ->where('id', '!=', $medical->id ?? 0)
                      ->update(['is_current' => false]);
            }
        });
    }
}
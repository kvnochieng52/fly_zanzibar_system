<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class StaffProficiency extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'proficiency_type_id',
        'epl_level',
        'testing_authority',
        'training_provider',
        'certificate_number',
        'examiner_name',
        'rating_type',
        'renewal_status',
        'check_type',
        'check_status',
        'route_sector',
        'issue_date',
        'expiry_date',
        'last_completion_date',
        'next_due_date',
        'document_file',
        'notes',
        'status',
        'is_current',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'last_completion_date' => 'date',
        'next_due_date' => 'date',
        'is_current' => 'boolean',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function proficiencyType(): BelongsTo
    {
        return $this->belongsTo(ProficiencyType::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'expired' => 'danger',
            'due_soon' => 'warning',
            'suspended' => 'warning',
            'revoked' => 'dark',
            default => 'secondary'
        };
    }

    public function getIsExpiredAttribute(): bool
    {
        $dueDate = $this->next_due_date ?: $this->expiry_date;
        if (!$dueDate) {
            return false;
        }
        return Carbon::now()->gt($dueDate);
    }

    public function getDaysUntilDueAttribute(): ?int
    {
        $dueDate = $this->next_due_date ?: $this->expiry_date;
        if (!$dueDate) {
            return null;
        }
        return Carbon::now()->diffInDays($dueDate, false);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeDueSoon($query, $days = 30)
    {
        return $query->where(function($q) use ($days) {
            $q->where('next_due_date', '<=', Carbon::now()->addDays($days))
              ->where('next_due_date', '>', Carbon::now())
              ->orWhere(function($q2) use ($days) {
                  $q2->whereNull('next_due_date')
                     ->where('expiry_date', '<=', Carbon::now()->addDays($days))
                     ->where('expiry_date', '>', Carbon::now());
              });
        });
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($proficiency) {
            $dueDate = $proficiency->next_due_date ?: $proficiency->expiry_date;

            if ($dueDate && Carbon::now()->gt($dueDate)) {
                $proficiency->status = 'expired';
            } elseif ($dueDate && Carbon::now()->addDays(30)->gte($dueDate)) {
                $proficiency->status = 'due_soon';
            }

            if ($proficiency->is_current) {
                static::where('staff_id', $proficiency->staff_id)
                      ->where('proficiency_type_id', $proficiency->proficiency_type_id)
                      ->where('id', '!=', $proficiency->id)
                      ->update(['is_current' => false]);
            }
        });
    }
}

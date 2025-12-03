<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class CompanyDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_type_id',
        'document_number',
        'title',
        'issue_date',
        'expiry_date',
        'version_revision',
        'issuing_authority_id',
        'responsible_department_id',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'status_id',
        'notes',
        'metadata',
        'is_current_version',
        'created_by',
        'updated_by',
        'renewed_at',
        'renewed_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'renewed_at' => 'datetime',
        'metadata' => 'array',
        'is_current_version' => 'boolean',
    ];

    protected $appends = [
        'days_until_expiry',
        'is_expired',
        'is_expiring_soon'
    ];

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(CompanyDocumentType::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(CompanyDocumentStatus::class);
    }

    public function issuingAuthority(): BelongsTo
    {
        return $this->belongsTo(IssuingAuthority::class);
    }

    public function responsibleDepartment(): BelongsTo
    {
        return $this->belongsTo(ResponsibleDepartment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function renewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'renewed_by');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(CompanyDocumentVersion::class, 'document_id');
    }

    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->expiry_date) {
            return null;
        }

        return Carbon::now()->diffInDays($this->expiry_date, false);
    }

    public function getIsExpiredAttribute(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        return Carbon::now()->greaterThan($this->expiry_date);
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        $daysUntilExpiry = $this->days_until_expiry;

        if ($daysUntilExpiry === null) {
            return false;
        }

        return $daysUntilExpiry <= 30 && $daysUntilExpiry >= 0;
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
                    ->where('expiry_date', '<=', Carbon::now()->addDays($days))
                    ->where('expiry_date', '>=', Carbon::now());
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
                    ->where('expiry_date', '<', Carbon::now());
    }

    public function scopeActive($query)
    {
        return $query->where('is_current_version', true);
    }

    public function scopeByType($query, $typeId)
    {
        return $query->where('document_type_id', $typeId);
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('responsible_department_id', $departmentId);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AircraftDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'aircraft_id',
        'document_type_id',
        'document_number',
        'issue_date',
        'expiry_date',
        'issuing_authority',
        'file_path',
        'is_current',
        'document_details',
        'document_status_id',
        'notes'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'is_current' => 'boolean',
        'document_details' => 'array',
    ];

    protected $appends = [
        'days_until_expiry',
        'is_expiring_soon',
        'is_expired',
        'status_color'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($document) {
            $document->updateComplianceStatus();
        });
    }

    // Relationships
    public function aircraft(): BelongsTo
    {
        return $this->belongsTo(Aircraft::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(AircraftDocumentType::class, 'document_type_id');
    }

    public function documentStatus(): BelongsTo
    {
        return $this->belongsTo(AircraftDocumentStatus::class, 'document_status_id');
    }

    // Accessors
    public function getDaysUntilExpiryAttribute(): ?int
    {
        return $this->expiry_date ? now()->diffInDays($this->expiry_date, false) : null;
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        if (!$this->expiry_date) return false;
        return $this->days_until_expiry !== null && $this->days_until_expiry <= 30 && $this->days_until_expiry > 0;
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getStatusColorAttribute(): string
    {
        return $this->documentStatus?->color ?? '#6c757d';
    }

    // Scopes
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeValid($query)
    {
        return $query->where('document_status_id', AircraftDocumentStatus::getValidId());
    }

    public function scopeExpiring($query)
    {
        return $query->where('document_status_id', AircraftDocumentStatus::getExpiringId());
    }

    public function scopeExpired($query)
    {
        return $query->where('document_status_id', AircraftDocumentStatus::getExpiredId());
    }

    public function scopeByType($query, $typeId)
    {
        return $query->where('document_type_id', $typeId);
    }

    // Methods
    public function updateComplianceStatus(): void
    {
        if (!$this->expiry_date || !$this->documentType?->requires_expiry) {
            $this->document_status_id = AircraftDocumentStatus::getValidId();
            return;
        }

        $daysUntilExpiry = now()->diffInDays($this->expiry_date, false);

        if ($daysUntilExpiry < 0) {
            $this->document_status_id = AircraftDocumentStatus::getExpiredId();
        } elseif ($daysUntilExpiry <= 30) {
            $this->document_status_id = AircraftDocumentStatus::getExpiringId();
        } else {
            $this->document_status_id = AircraftDocumentStatus::getValidId();
        }
    }

    public function getRequiredFields(): array
    {
        return $this->documentType?->getRequiredFields() ?? [];
    }
}

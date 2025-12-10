<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aircraft extends Model
{
    use HasFactory;

    protected $table = 'aircraft';

    protected $fillable = [
        'registration_number',
        'manufacturer_id',
        'model_id',
        'serial_number',
        'year_of_manufacture',
        'total_airframe_hours',
        'total_cycles',
        'status_id',
        'home_base',
        'seating_configuration',
        'photos',
        'notes'
    ];

    protected $casts = [
        'year_of_manufacture' => 'integer',
        'total_airframe_hours' => 'decimal:2',
        'total_cycles' => 'integer',
        'photos' => 'array',
    ];

    protected $appends = [
        'age_in_years',
        'status_color'
    ];

    // Relationships
    public function documents(): HasMany
    {
        return $this->hasMany(AircraftDocument::class);
    }

    public function currentDocuments(): HasMany
    {
        return $this->hasMany(AircraftDocument::class)->where('is_current', true);
    }

    public function validDocuments(): HasMany
    {
        return $this->hasMany(AircraftDocument::class)->where('document_status_id', AircraftDocumentStatus::getValidId());
    }

    public function expiringDocuments(): HasMany
    {
        return $this->hasMany(AircraftDocument::class)->where('document_status_id', AircraftDocumentStatus::getExpiringId());
    }

    public function expiredDocuments(): HasMany
    {
        return $this->hasMany(AircraftDocument::class)->where('document_status_id', AircraftDocumentStatus::getExpiredId());
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(AircraftStatus::class, 'status_id');
    }

    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(AircraftManufacturer::class, 'manufacturer_id');
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(AircraftModel::class, 'model_id');
    }

    // Accessors
    public function getAgeInYearsAttribute(): ?int
    {
        return $this->year_of_manufacture ? now()->year - $this->year_of_manufacture : null;
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status?->color ?? '#6c757d';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status_id', AircraftStatus::getInServiceId());
    }

    public function scopeInService($query)
    {
        return $query->where('status_id', AircraftStatus::getInServiceId());
    }

    public function scopeInMaintenance($query)
    {
        return $query->where('status_id', AircraftStatus::getMaintenanceId());
    }

    public function scopeAOG($query)
    {
        return $query->where('status_id', AircraftStatus::getAOGId());
    }

    public function scopeByManufacturer($query, $manufacturerId)
    {
        return $query->where('manufacturer_id', $manufacturerId);
    }

    public function scopeByModel($query, $modelId)
    {
        return $query->where('model_id', $modelId);
    }

    public function scopeByHomeBase($query, $homeBase)
    {
        return $query->where('home_base', $homeBase);
    }

    // Methods
    public function getComplianceStatus(): array
    {
        $documents = $this->documents()->where('is_current', true)->with('documentStatus')->get();

        $validId = AircraftDocumentStatus::getValidId();
        $expiringId = AircraftDocumentStatus::getExpiringId();
        $expiredId = AircraftDocumentStatus::getExpiredId();
        $pendingId = AircraftDocumentStatus::getPendingId();

        $compliance = [
            'total' => $documents->count(),
            'valid' => $documents->where('document_status_id', $validId)->count(),
            'expiring' => $documents->where('document_status_id', $expiringId)->count(),
            'expired' => $documents->where('document_status_id', $expiredId)->count(),
            'pending' => $documents->where('document_status_id', $pendingId)->count(),
        ];

        $compliance['compliance_percentage'] = $compliance['total'] > 0
            ? round(($compliance['valid'] / $compliance['total']) * 100, 1)
            : 0;

        return $compliance;
    }

    public function isCompliant(): bool
    {
        return $this->expiredDocuments()->count() === 0;
    }

    public function hasExpiringDocuments(): bool
    {
        return $this->expiringDocuments()->count() > 0;
    }
}

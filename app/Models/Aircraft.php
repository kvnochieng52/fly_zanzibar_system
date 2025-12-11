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
        'total_capacity',
        'max_passengers',
        'max_takeoff_weight',
        'max_landing_weight',
        'empty_weight',
        'useful_load',
        'engine_type',
        'number_of_engines',
        'fuel_capacity',
        'service_ceiling',
        'maximum_range',
        'cruise_speed',
        'aircraft_category',
        'certification_basis',
        'ifr_certified',
        'rvsm_approved',
        'etops_rating',
        'certificate_of_airworthiness',
        'coa_issue_date',
        'coa_expiry_date',
        'avionics_suite',
        'propeller_type',
        'noise_certification',
        'photos',
        'notes'
    ];

    protected $casts = [
        'year_of_manufacture' => 'integer',
        'total_airframe_hours' => 'decimal:2',
        'total_cycles' => 'integer',
        'total_capacity' => 'integer',
        'max_passengers' => 'integer',
        'max_takeoff_weight' => 'decimal:2',
        'max_landing_weight' => 'decimal:2',
        'empty_weight' => 'decimal:2',
        'useful_load' => 'decimal:2',
        'number_of_engines' => 'integer',
        'fuel_capacity' => 'decimal:2',
        'service_ceiling' => 'integer',
        'maximum_range' => 'integer',
        'cruise_speed' => 'integer',
        'ifr_certified' => 'boolean',
        'rvsm_approved' => 'boolean',
        'coa_issue_date' => 'date',
        'coa_expiry_date' => 'date',
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

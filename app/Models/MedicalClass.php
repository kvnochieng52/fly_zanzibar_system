<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'validity_period_months',
        'privileges',
        'limitations',
        'requires_renewal',
        'is_active',
    ];

    protected $casts = [
        'privileges' => 'array',
        'limitations' => 'array',
        'requires_renewal' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function staffMedicalCertificates(): HasMany
    {
        return $this->hasMany(StaffMedicalCertificate::class, 'medical_class', 'code');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

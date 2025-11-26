<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'color',
        'description',
        'is_active_status',
        'is_active',
    ];

    protected $casts = [
        'is_active_status' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function staffMedicalCertificates(): HasMany
    {
        return $this->hasMany(StaffMedicalCertificate::class, 'medical_status', 'code');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeActiveStatus($query)
    {
        return $query->where('is_active_status', true);
    }
}

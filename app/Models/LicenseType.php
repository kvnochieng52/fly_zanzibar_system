<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LicenseType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'renewal_period_months',
        'required_fields',
        'requires_medical',
        'is_active',
    ];

    protected $casts = [
        'required_fields' => 'array',
        'requires_medical' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function staffLicenses(): HasMany
    {
        return $this->hasMany(StaffLicense::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

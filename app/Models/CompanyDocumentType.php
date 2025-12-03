<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyDocumentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'requires_expiry',
        'default_validity_months',
        'is_active',
    ];

    protected $casts = [
        'requires_expiry' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function documents(): HasMany
    {
        return $this->hasMany(CompanyDocument::class, 'document_type_id');
    }

    public function alertThresholds(): HasMany
    {
        return $this->hasMany(DocumentAlertThreshold::class, 'document_type_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

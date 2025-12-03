<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AircraftDocumentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'required_fields',
        'requires_expiry',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'required_fields' => 'array',
        'requires_expiry' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    // Relationships
    public function documents(): HasMany
    {
        return $this->hasMany(AircraftDocument::class, 'document_type_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Methods
    public function getRequiredFields(): array
    {
        return $this->required_fields ?? [];
    }
}

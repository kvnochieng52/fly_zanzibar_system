<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IssuingAuthority extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'country',
        'contact_email',
        'contact_phone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function documents(): HasMany
    {
        return $this->hasMany(CompanyDocument::class, 'issuing_authority_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

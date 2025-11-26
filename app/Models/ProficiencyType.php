<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProficiencyType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'renewal_period_months',
        'has_levels',
        'required_fields',
        'is_active',
    ];

    protected $casts = [
        'has_levels' => 'boolean',
        'is_active' => 'boolean',
        'required_fields' => 'array',
    ];

    public function staffProficiencies(): HasMany
    {
        return $this->hasMany(StaffProficiency::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

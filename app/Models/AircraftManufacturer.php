<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AircraftManufacturer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'country',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relationships
    public function models()
    {
        return $this->hasMany(AircraftModel::class, 'manufacturer_id');
    }

    public function aircraft()
    {
        return $this->hasMany(Aircraft::class, 'manufacturer_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}

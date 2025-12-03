<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AircraftModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'manufacturer_id',
        'name',
        'code',
        'description',
        'category',
        'typical_seating',
        'engine_type',
        'specifications',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'specifications' => 'array'
    ];

    // Relationships
    public function manufacturer()
    {
        return $this->belongsTo(AircraftManufacturer::class, 'manufacturer_id');
    }

    public function aircraft()
    {
        return $this->hasMany(Aircraft::class, 'model_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByManufacturer($query, $manufacturerId)
    {
        return $query->where('manufacturer_id', $manufacturerId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->manufacturer->name . ' ' . $this->name;
    }
}

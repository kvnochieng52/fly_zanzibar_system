<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'category',
        'color',
        'requires_downtime',
        'regulatory_required',
        'estimated_hours',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'requires_downtime' => 'boolean',
        'regulatory_required' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function maintenanceSchedules()
    {
        return $this->hasMany(AircraftMaintenanceSchedule::class);
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}

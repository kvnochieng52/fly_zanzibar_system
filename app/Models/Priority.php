<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Priority extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'color',
        'level',
        'context',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'level' => 'integer'
    ];

    // Relationships
    public function maintenanceSchedules()
    {
        return $this->hasMany(AircraftMaintenanceSchedule::class, 'priority_id');
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class, 'priority_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('level');
    }

    public function scopeByContext($query, $context)
    {
        return $query->whereIn('context', [$context, 'general']);
    }

    public function scopeForMaintenance($query)
    {
        return $query->whereIn('context', ['maintenance', 'general']);
    }

    public function scopeForWorkOrders($query)
    {
        return $query->whereIn('context', ['work_order', 'general']);
    }

    // Accessors
    public function getBadgeClassAttribute()
    {
        $levelMapping = [
            1 => 'badge-success',
            2 => 'badge-info',
            3 => 'badge-warning',
            4 => 'badge-danger',
            5 => 'badge-dark',
        ];

        return $levelMapping[$this->level] ?? 'badge-secondary';
    }

    public function getDisplayNameAttribute()
    {
        return $this->name . ' (' . $this->code . ')';
    }
}
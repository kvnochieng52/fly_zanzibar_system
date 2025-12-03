<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceOrganization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'description',
        'contact_person',
        'email',
        'phone',
        'address',
        'certification_number',
        'certification_expiry',
        'capabilities',
        'hourly_rate_usd',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'certification_expiry' => 'date',
        'capabilities' => 'json',
        'hourly_rate_usd' => 'decimal:2',
        'is_active' => 'boolean'
    ];

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

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeInternal($query)
    {
        return $query->where('type', 'internal');
    }

    public function scopeExternal($query)
    {
        return $query->where('type', 'external');
    }
}

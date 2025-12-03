<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'color',
        'is_active',
        'is_final_status',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_final_status' => 'boolean'
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

    public function scopeFinal($query)
    {
        return $query->where('is_final_status', true);
    }

    public function scopeOpen($query)
    {
        return $query->where('is_final_status', false);
    }
}

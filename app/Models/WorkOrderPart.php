<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderPart extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_id',
        'part_number',
        'part_name',
        'part_description',
        'serial_number',
        'batch_lot_number',
        'quantity_required',
        'quantity_used',
        'quantity_removed',
        'condition',
        'status',
        'unit_cost',
        'total_cost',
        'installation_position',
        'installed_at',
        'removed_at',
        'part_total_hours',
        'part_total_cycles',
        'manufacture_date',
        'expiry_date',
        'vendor',
        'purchase_order_number',
        'delivery_date',
        'certification_reference',
        'approved_part',
        'compliance_notes',
        'notes',
        'issued_by',
        'installed_by'
    ];

    protected $casts = [
        'installed_at' => 'datetime',
        'removed_at' => 'datetime',
        'manufacture_date' => 'date',
        'expiry_date' => 'date',
        'delivery_date' => 'date',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'approved_part' => 'boolean'
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function installedBy()
    {
        return $this->belongsTo(User::class, 'installed_by');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeInstalled($query)
    {
        return $query->where('status', 'installed');
    }

    public function scopeByCondition($query, $condition)
    {
        return $query->where('condition', $condition);
    }

    public function calculateTotalCost()
    {
        $total = $this->quantity_used * $this->unit_cost;
        $this->update(['total_cost' => $total]);
        return $total;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($part) {
            if ($part->quantity_used && $part->unit_cost) {
                $part->total_cost = $part->quantity_used * $part->unit_cost;
            }
        });
    }
}

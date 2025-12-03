<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class WorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_number',
        'aircraft_id',
        'maintenance_type_id',
        'maintenance_organization_id',
        'work_order_status_id',
        'maintenance_schedule_id',
        'title',
        'description',
        'work_scope',
        'priority',
        'scheduled_start',
        'scheduled_completion',
        'actual_start',
        'actual_completion',
        'aircraft_hours_at_start',
        'aircraft_cycles_at_start',
        'estimated_cost',
        'actual_cost',
        'labor_cost',
        'parts_cost',
        'external_cost',
        'estimated_hours',
        'actual_hours',
        'aircraft_grounded_at',
        'aircraft_released_at',
        'downtime_hours',
        'quality_check_required',
        'quality_check_passed',
        'quality_check_date',
        'quality_notes',
        'regulatory_sign_off_required',
        'regulatory_sign_off_completed',
        'regulatory_reference',
        'notes',
        'completion_notes',
        'custom_fields',
        'created_by',
        'assigned_to'
    ];

    protected $casts = [
        'scheduled_start' => 'datetime',
        'scheduled_completion' => 'datetime',
        'actual_start' => 'datetime',
        'actual_completion' => 'datetime',
        'aircraft_grounded_at' => 'datetime',
        'aircraft_released_at' => 'datetime',
        'quality_check_date' => 'datetime',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'labor_cost' => 'decimal:2',
        'parts_cost' => 'decimal:2',
        'external_cost' => 'decimal:2',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'quality_check_required' => 'boolean',
        'quality_check_passed' => 'boolean',
        'regulatory_sign_off_required' => 'boolean',
        'regulatory_sign_off_completed' => 'boolean',
        'custom_fields' => 'json'
    ];

    // Relationships
    public function aircraft()
    {
        return $this->belongsTo(Aircraft::class);
    }

    public function maintenanceType()
    {
        return $this->belongsTo(MaintenanceType::class);
    }

    public function maintenanceOrganization()
    {
        return $this->belongsTo(MaintenanceOrganization::class);
    }

    public function workOrderStatus()
    {
        return $this->belongsTo(WorkOrderStatus::class);
    }

    public function maintenanceSchedule()
    {
        return $this->belongsTo(AircraftMaintenanceSchedule::class);
    }

    public function parts()
    {
        return $this->hasMany(WorkOrderPart::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Scopes
    public function scopeByStatus($query, $statusCode)
    {
        return $query->whereHas('workOrderStatus', function($q) use ($statusCode) {
            $q->where('code', $statusCode);
        });
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeOverdue($query)
    {
        return $query->where('scheduled_completion', '<', now())
                    ->whereNotIn('work_order_status_id', function($q) {
                        $q->select('id')->from('work_order_statuses')->where('is_final_status', true);
                    });
    }

    public function scopeOpen($query)
    {
        return $query->whereNotIn('work_order_status_id', function($q) {
            $q->select('id')->from('work_order_statuses')->where('is_final_status', true);
        });
    }

    // Business logic methods
    public function calculateDowntime()
    {
        if ($this->aircraft_grounded_at && $this->aircraft_released_at) {
            $downtime = $this->aircraft_grounded_at->diffInHours($this->aircraft_released_at);
            $this->update(['downtime_hours' => $downtime]);
            return $downtime;
        }
        return null;
    }

    public function updateTotalCost()
    {
        $total = $this->labor_cost + $this->parts_cost + $this->external_cost;
        $this->update(['actual_cost' => $total]);
        return $total;
    }

    public function generateWorkOrderNumber()
    {
        $prefix = 'WO';
        $year = date('Y');
        $month = date('m');
        $lastNumber = static::where('work_order_number', 'like', "{$prefix}-{$year}{$month}%")
                           ->orderBy('work_order_number', 'desc')
                           ->first();

        if ($lastNumber) {
            $lastSequence = intval(substr($lastNumber->work_order_number, -4));
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        return "{$prefix}-{$year}{$month}" . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($workOrder) {
            if (empty($workOrder->work_order_number)) {
                $workOrder->work_order_number = $workOrder->generateWorkOrderNumber();
            }
        });
    }
}

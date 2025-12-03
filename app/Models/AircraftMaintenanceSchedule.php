<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AircraftMaintenanceSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'aircraft_id',
        'maintenance_type_id',
        'schedule_name',
        'description',
        'due_hours',
        'due_cycles',
        'due_calendar_date',
        'current_hours',
        'current_cycles',
        'last_completed_date',
        'last_completed_hours',
        'last_completed_cycles',
        'next_due_date',
        'next_due_hours',
        'next_due_cycles',
        'interval_hours',
        'interval_cycles',
        'interval_days',
        'compliance_status',
        'priority',
        'regulatory_required',
        'is_active',
        'warning_hours',
        'warning_cycles',
        'warning_days'
    ];

    protected $casts = [
        'due_calendar_date' => 'date',
        'last_completed_date' => 'date',
        'next_due_date' => 'date',
        'regulatory_required' => 'boolean',
        'is_active' => 'boolean'
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

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class, 'maintenance_schedule_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDueSoon($query)
    {
        return $query->where('compliance_status', 'due_soon');
    }

    public function scopeOverdue($query)
    {
        return $query->where('compliance_status', 'overdue');
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    // Business logic methods
    public function updateComplianceStatus()
    {
        $status = 'current';
        $now = Carbon::now();

        // Check calendar due date
        if ($this->next_due_date) {
            $warningDate = $this->next_due_date->subDays($this->warning_days);
            if ($now > $this->next_due_date) {
                $status = 'overdue';
            } elseif ($now >= $warningDate) {
                $status = 'due_soon';
            }
        }

        // Check hours-based due
        if ($this->next_due_hours && $this->current_hours) {
            $hoursRemaining = $this->next_due_hours - $this->current_hours;
            if ($hoursRemaining <= 0) {
                $status = 'overdue';
            } elseif ($hoursRemaining <= $this->warning_hours) {
                $status = 'due_soon';
            }
        }

        // Check cycles-based due
        if ($this->next_due_cycles && $this->current_cycles) {
            $cyclesRemaining = $this->next_due_cycles - $this->current_cycles;
            if ($cyclesRemaining <= 0) {
                $status = 'overdue';
            } elseif ($cyclesRemaining <= $this->warning_cycles) {
                $status = 'due_soon';
            }
        }

        $this->update(['compliance_status' => $status]);
        return $status;
    }

    public function calculateNextDue()
    {
        if ($this->last_completed_date) {
            // Calendar-based
            if ($this->interval_days) {
                $this->next_due_date = $this->last_completed_date->addDays($this->interval_days);
            }

            // Hours-based
            if ($this->interval_hours && $this->last_completed_hours) {
                $this->next_due_hours = $this->last_completed_hours + $this->interval_hours;
            }

            // Cycles-based
            if ($this->interval_cycles && $this->last_completed_cycles) {
                $this->next_due_cycles = $this->last_completed_cycles + $this->interval_cycles;
            }

            $this->save();
        }
    }
}

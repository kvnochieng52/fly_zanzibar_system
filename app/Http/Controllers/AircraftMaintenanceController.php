<?php

namespace App\Http\Controllers;

use App\Models\Aircraft;
use App\Models\AircraftMaintenanceSchedule;
use App\Models\MaintenanceType;
use App\Models\MaintenanceOrganization;
use App\Models\Priority;
use App\Models\WorkOrder;
use App\Models\WorkOrderStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class AircraftMaintenanceController extends Controller
{
    /**
     * Store a new maintenance schedule
     */
    public function storeSchedule(Request $request, Aircraft $aircraft)
    {
        $request->validate([
            'maintenance_type_id' => 'required|exists:maintenance_types,id',
            'schedule_name' => 'required|string|max:255',
            'description' => 'nullable|string',

            // Due criteria (at least one must be set)
            'due_hours' => 'nullable|integer|min:0',
            'due_cycles' => 'nullable|integer|min:0',
            'due_calendar_date' => 'nullable|date',

            // Current status
            'current_hours' => 'nullable|integer|min:0',
            'current_cycles' => 'nullable|integer|min:0',

            // Last maintenance info
            'last_completed_date' => 'nullable|date|before_or_equal:today',
            'last_completed_hours' => 'nullable|integer|min:0',
            'last_completed_cycles' => 'nullable|integer|min:0',

            // Intervals for recurring maintenance
            'interval_hours' => 'nullable|integer|min:1',
            'interval_cycles' => 'nullable|integer|min:1',
            'interval_days' => 'nullable|integer|min:1',

            // Settings
            'priority' => 'required|in:low,normal,high,critical',
            'regulatory_required' => 'boolean',
            'warning_hours' => 'nullable|integer|min:0',
            'warning_cycles' => 'nullable|integer|min:0',
            'warning_days' => 'nullable|integer|min:0',
        ]);

        // Validate that at least one due criteria is set
        if (!$request->due_hours && !$request->due_cycles && !$request->due_calendar_date) {
            return Redirect::back()->withErrors(['due_criteria' => 'At least one due criteria (hours, cycles, or calendar date) must be specified.']);
        }

        $maintenanceType = MaintenanceType::findOrFail($request->maintenance_type_id);

        // Calculate next due dates if intervals are provided
        $nextDueDate = $request->due_calendar_date;
        $nextDueHours = $request->due_hours;
        $nextDueCycles = $request->due_cycles;

        if ($request->last_completed_date && $request->interval_days) {
            $nextDueDate = \Carbon\Carbon::parse($request->last_completed_date)->addDays($request->interval_days)->toDateString();
        }

        if ($request->last_completed_hours && $request->interval_hours) {
            $nextDueHours = $request->last_completed_hours + $request->interval_hours;
        }

        if ($request->last_completed_cycles && $request->interval_cycles) {
            $nextDueCycles = $request->last_completed_cycles + $request->interval_cycles;
        }

        $schedule = AircraftMaintenanceSchedule::create([
            'aircraft_id' => $aircraft->id,
            'maintenance_type_id' => $request->maintenance_type_id,
            'schedule_name' => $request->schedule_name,
            'description' => $request->description,

            // Due criteria
            'due_hours' => $request->due_hours,
            'due_cycles' => $request->due_cycles,
            'due_calendar_date' => $request->due_calendar_date,

            // Current status
            'current_hours' => $request->current_hours ?? 0,
            'current_cycles' => $request->current_cycles ?? 0,

            // Last maintenance
            'last_completed_date' => $request->last_completed_date,
            'last_completed_hours' => $request->last_completed_hours,
            'last_completed_cycles' => $request->last_completed_cycles,

            // Next due (calculated)
            'next_due_date' => $nextDueDate,
            'next_due_hours' => $nextDueHours,
            'next_due_cycles' => $nextDueCycles,

            // Intervals
            'interval_hours' => $request->interval_hours,
            'interval_cycles' => $request->interval_cycles,
            'interval_days' => $request->interval_days,

            // Status and settings
            'compliance_status' => 'current',
            'priority' => $request->priority,
            'regulatory_required' => $request->regulatory_required ?? $maintenanceType->regulatory_required,
            'is_active' => true,
            'warning_hours' => $request->warning_hours ?? 10,
            'warning_cycles' => $request->warning_cycles ?? 5,
            'warning_days' => $request->warning_days ?? 7,
        ]);

        // Update compliance status based on current vs due criteria
        $schedule->updateComplianceStatus();

        return Redirect::back()->with('success', 'Maintenance schedule created successfully!');
    }

    /**
     * Store a new work order
     */
    public function storeWorkOrder(Request $request, Aircraft $aircraft)
    {
        $request->validate([
            'work_order_number' => 'nullable|string|unique:work_orders,work_order_number',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'nullable|string', // This will be stored as work_scope
            'priority_id' => 'required|exists:priorities,id',
            'status_id' => 'nullable|exists:work_order_statuses,id',
            'maintenance_type_id' => 'nullable|exists:maintenance_types,id',
            'maintenance_organization_id' => 'nullable|exists:maintenance_organizations,id',

            // Scheduling
            'scheduled_start_date' => 'nullable|date',
            'due_date' => 'nullable|date', // Will map to scheduled_completion
            'estimated_hours' => 'nullable|numeric|min:0',

            // Assignment
            'assigned_user_id' => 'nullable|exists:users,id',

            // Quality and regulatory
            'regulatory_required' => 'boolean',
            'requires_inspection' => 'boolean',
            'requires_certification' => 'boolean',

            // Cost estimates
            'estimated_labor_cost' => 'nullable|numeric|min:0',
            'estimated_parts_cost' => 'nullable|numeric|min:0',

            // Notes
            'notes' => 'nullable|string',
        ]);

        // Get priority value from priorities table
        $priority = null;
        if ($request->priority_id) {
            $priorityRecord = Priority::find($request->priority_id);
            $priority = $priorityRecord ? strtolower($priorityRecord->code) : 'normal';
        }

        // Don't pass work_order_number if it's empty - let the model auto-generate it
        $data = [
            'aircraft_id' => $aircraft->id,
            'title' => $request->title,
            'description' => $request->description,
            'work_scope' => $request->type, // Map type to work_scope
            'priority' => $priority ?? 'normal', // Use enum value, not ID
            'work_order_status_id' => $request->status_id ?? 1, // Default to first status if not provided
            'maintenance_type_id' => $request->maintenance_type_id ?? 1, // Make required with default
            'maintenance_organization_id' => $request->maintenance_organization_id ?? 1, // Make required with default

            // Scheduling
            'scheduled_start' => $request->scheduled_start_date,
            'scheduled_completion' => $request->due_date,
            'estimated_hours' => $request->estimated_hours,

            // Assignment
            'assigned_to' => $request->assigned_user_id,
            'created_by' => auth()->id(),

            // Quality and regulatory
            'regulatory_sign_off_required' => $request->regulatory_required ?? false,
            'quality_check_required' => $request->requires_inspection ?? false,
            // Note: requires_certification doesn't have a direct field, could be stored in custom_fields

            // Cost estimates
            'labor_cost' => $request->estimated_labor_cost ?? 0,
            'parts_cost' => $request->estimated_parts_cost ?? 0,

            // Notes
            'notes' => $request->notes,
        ];

        // Only add work_order_number if provided and not empty
        if (!empty($request->work_order_number)) {
            $data['work_order_number'] = $request->work_order_number;
        }

        $workOrder = WorkOrder::create($data);

        return Redirect::back()->with('success', 'Work order ' . $workOrder->work_order_number . ' created successfully!');
    }

    /**
     * Get maintenance types for dropdown
     */
    public function getMaintenanceTypes()
    {
        $types = MaintenanceType::active()->ordered()->get();
        return response()->json($types);
    }

    /**
     * Get maintenance organizations for dropdown
     */
    public function getMaintenanceOrganizations()
    {
        $organizations = MaintenanceOrganization::active()->ordered()->get();
        return response()->json($organizations);
    }

    /**
     * Get priorities for dropdown
     */
    public function getPriorities($context = 'general')
    {
        $priorities = Priority::active()->byContext($context)->ordered()->get();
        return response()->json($priorities);
    }

    /**
     * Update work order
     */
    public function updateWorkOrder(Request $request, WorkOrder $workOrder)
    {
        $request->validate([
            'maintenance_type_id' => 'required|exists:maintenance_types,id',
            'maintenance_organization_id' => 'required|exists:maintenance_organizations,id',
            'work_order_status_id' => 'required|exists:work_order_statuses,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'work_scope' => 'nullable|string',
            'priority' => 'required|in:low,normal,high,critical',
            'scheduled_start' => 'nullable|date',
            'scheduled_completion' => 'nullable|date|after_or_equal:scheduled_start',
            'actual_start' => 'nullable|date',
            'actual_completion' => 'nullable|date|after_or_equal:actual_start',
            'aircraft_hours_at_start' => 'nullable|integer|min:0',
            'aircraft_cycles_at_start' => 'nullable|integer|min:0',
            'estimated_cost' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'labor_cost' => 'nullable|numeric|min:0',
            'parts_cost' => 'nullable|numeric|min:0',
            'external_cost' => 'nullable|numeric|min:0',
            'estimated_hours' => 'nullable|numeric|min:0',
            'actual_hours' => 'nullable|numeric|min:0',
            'aircraft_grounded_at' => 'nullable|date',
            'aircraft_released_at' => 'nullable|date|after_or_equal:aircraft_grounded_at',
            'quality_check_required' => 'boolean',
            'quality_check_passed' => 'nullable|boolean',
            'quality_check_date' => 'nullable|date',
            'quality_notes' => 'nullable|string',
            'regulatory_sign_off_required' => 'boolean',
            'regulatory_sign_off_completed' => 'nullable|boolean',
            'regulatory_reference' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'completion_notes' => 'nullable|string',
        ]);

        $workOrder->update($request->all());

        // Calculate downtime if aircraft was grounded and released
        if ($request->aircraft_grounded_at && $request->aircraft_released_at) {
            $grounded = \Carbon\Carbon::parse($request->aircraft_grounded_at);
            $released = \Carbon\Carbon::parse($request->aircraft_released_at);
            $workOrder->downtime_hours = $grounded->diffInHours($released);
            $workOrder->save();
        }

        return Redirect::back()->with('success', 'Work order updated successfully!');
    }

    /**
     * Update work order status
     */
    public function updateWorkOrderStatus(Request $request, WorkOrder $workOrder)
    {
        $request->validate([
            'work_order_status_id' => 'required|exists:work_order_statuses,id',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $workOrder->workOrderStatus;
        $newStatus = WorkOrderStatus::findOrFail($request->work_order_status_id);

        $workOrder->update([
            'work_order_status_id' => $request->work_order_status_id,
        ]);

        // Handle status-specific actions
        if ($newStatus->code === 'IN_PROGRESS' && !$workOrder->actual_start) {
            $workOrder->update(['actual_start' => now()]);
        }

        if ($newStatus->is_final_status && !$workOrder->actual_completion) {
            $workOrder->update(['actual_completion' => now()]);
        }

        return Redirect::back()->with('success', 'Work order status updated to ' . $newStatus->name . ' successfully!');
    }

    /**
     * Get aircraft maintenance data for show page
     */
    public function getMaintenanceData(Aircraft $aircraft)
    {
        $maintenanceSchedules = AircraftMaintenanceSchedule::where('aircraft_id', $aircraft->id)
            ->with(['maintenanceType', 'workOrders'])
            ->orderBy('next_due_date')
            ->get()
            ->map(function ($schedule) {
                // Debug: ensure maintenanceType is loaded or provide fallback
                if (!$schedule->maintenanceType && $schedule->maintenance_type_id) {
                    // Try to load the maintenance type manually if relationship failed
                    $schedule->maintenanceType = MaintenanceType::find($schedule->maintenance_type_id);
                }

                // Add fallback maintenance type if still missing
                if (!$schedule->maintenanceType) {
                    $schedule->maintenance_type = (object)[
                        'id' => null,
                        'name' => 'General Maintenance',
                        'code' => 'GEN'
                    ];
                }

                return $schedule;
            });

        $workOrders = WorkOrder::where('aircraft_id', $aircraft->id)
            ->with(['workOrderStatus', 'maintenanceOrganization', 'maintenanceType', 'createdBy', 'assignedTo'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($workOrder) {
                // Add computed fields to match frontend expectations
                $workOrder->status = $workOrder->workOrderStatus;
                $workOrder->type = $workOrder->work_scope ?: 'corrective';
                $workOrder->due_date = $workOrder->scheduled_completion;
                $workOrder->assigned_user = $workOrder->assignedTo;

                // Create priority object from enum
                if ($workOrder->priority) {
                    $workOrder->priority = (object)[
                        'code' => strtoupper($workOrder->priority),
                        'name' => ucfirst($workOrder->priority)
                    ];
                }

                return $workOrder;
            });

        $maintenanceTypes = MaintenanceType::active()->ordered()->get();
        $maintenanceOrganizations = MaintenanceOrganization::active()->ordered()->get();
        $workOrderStatuses = WorkOrderStatus::active()->ordered()->get();
        $maintenancePriorities = Priority::active()->forMaintenance()->ordered()->get();
        $workOrderPriorities = Priority::active()->forWorkOrders()->ordered()->get();
        $priorities = Priority::active()->ordered()->get();
        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        return [
            'schedules' => $maintenanceSchedules,
            'workOrders' => $workOrders,
            'maintenanceTypes' => $maintenanceTypes,
            'maintenanceOrganizations' => $maintenanceOrganizations,
            'workOrderStatuses' => $workOrderStatuses,
            'maintenancePriorities' => $maintenancePriorities,
            'workOrderPriorities' => $workOrderPriorities,
            'priorities' => $priorities,
            'users' => $users,
        ];
    }

    /**
     * Delete maintenance schedule
     */
    public function deleteSchedule(AircraftMaintenanceSchedule $schedule)
    {
        $schedule->delete();
        return Redirect::back()->with('success', 'Maintenance schedule deleted successfully!');
    }

    /**
     * Delete work order
     */
    public function deleteWorkOrder(WorkOrder $workOrder)
    {
        $workOrder->delete();
        return Redirect::back()->with('success', 'Work order deleted successfully!');
    }
}
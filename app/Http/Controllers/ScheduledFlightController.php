<?php

namespace App\Http\Controllers;

use App\Models\ScheduledFlight;
use App\Models\FlightScheduleRoute;
use App\Models\Aircraft;
use App\Models\Airport;
use App\Models\FlightStatus;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ScheduledFlightController extends Controller
{
    public function index(Request $request)
    {
        $query = ScheduledFlight::with([
            'aircraft.manufacturer',
            'aircraft.model',
            'aircraft.status',
            'flightStatus',
            'scheduleRoutes.originAirport',
            'scheduleRoutes.destinationAirport'
        ]);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('flight_code', 'like', '%' . $request->search . '%')
                  ->orWhereHas('aircraft', function ($subQ) use ($request) {
                      $subQ->where('registration_number', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->status_id) {
            $query->where('flight_status_id', $request->status_id);
        }

        if ($request->date) {
            $query->whereDate('total_departure_time', $request->date);
        }

        $flights = $query->orderBy('total_departure_time', 'desc')->paginate(15);

        return Inertia::render('ScheduledFlights/Index', [
            'flights' => $flights,
            'filters' => $request->only(['search', 'status_id', 'date']),
            'statuses' => FlightStatus::active()->ordered()->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('ScheduledFlights/Create', [
            'aircraft' => Aircraft::with(['manufacturer', 'model', 'status'])
                ->where('status_id', '!=', \App\Models\AircraftStatus::getAOGId())
                ->orderBy('registration_number')
                ->get(),
            'airports' => Airport::active()->orderBy('name')->get(),
            'statuses' => FlightStatus::active()->ordered()->get(),
            'customers' => Customer::select('id', 'first_name', 'last_name', 'company_name', 'email')->orderBy('company_name')->orderBy('last_name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'aircraft_id' => 'required|exists:aircraft,id',
            'flight_status_id' => 'required|exists:flight_statuses,id',
            'flight_type' => 'required|in:passenger,cargo,mixed',
            'primary_customer_id' => 'nullable|exists:customers,id',
            'total_departure_time' => 'required|date',
            'total_arrival_time' => 'nullable|date|after:total_departure_time',
            'notes' => 'nullable|string|max:1000',
            'routes' => 'required|array|min:1',
            'routes.*.origin_airport_id' => 'required|exists:airports,id',
            'routes.*.destination_airport_id' => 'required|exists:airports,id|different:routes.*.origin_airport_id',
            'routes.*.notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            // Create the main scheduled flight record
            $flight = ScheduledFlight::create([
                'aircraft_id' => $validated['aircraft_id'],
                'flight_status_id' => $validated['flight_status_id'],
                'flight_type' => $validated['flight_type'],
                'primary_customer_id' => $validated['primary_customer_id'],
                'total_departure_time' => $validated['total_departure_time'],
                'total_arrival_time' => $validated['total_arrival_time'],
                'notes' => $validated['notes'],
            ]);

            // Create route segments
            $routeOrder = 1;
            foreach ($validated['routes'] as $routeData) {
                FlightScheduleRoute::create([
                    'scheduled_flight_id' => $flight->id,
                    'route_order' => $routeOrder++,
                    'origin_airport_id' => $routeData['origin_airport_id'],
                    'destination_airport_id' => $routeData['destination_airport_id'],
                    'notes' => $routeData['notes'] ?? null,
                ]);
            }

            // Calculate and update totals
            $flight->calculateTotals();

            DB::commit();

            return redirect()->route('scheduled-flights.index')
                ->with('success', 'Scheduled flight created successfully with code: ' . $flight->flight_code);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withErrors(['error' => 'Failed to create scheduled flight: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(ScheduledFlight $scheduledFlight)
    {
        $scheduledFlight->load([
            'aircraft.manufacturer',
            'aircraft.model',
            'aircraft.status',
            'flightStatus',
            'scheduleRoutes.originAirport',
            'scheduleRoutes.destinationAirport',
            'passengers',
            'cargo',
            'landingFees.airport',
            'fuelConsumption'
        ]);

        return Inertia::render('ScheduledFlights/Show', [
            'flight' => $scheduledFlight,
            'airports' => Airport::active()->orderBy('name')->get(),
        ]);
    }

    public function edit(ScheduledFlight $scheduledFlight)
    {
        $scheduledFlight->load([
            'aircraft.manufacturer',
            'aircraft.model',
            'aircraft.status',
            'flightStatus',
            'scheduleRoutes.originAirport',
            'scheduleRoutes.destinationAirport'
        ]);

        return Inertia::render('ScheduledFlights/Edit', [
            'flight' => $scheduledFlight,
            'aircraft' => Aircraft::with(['manufacturer', 'model', 'status'])
                ->where('status_id', '!=', \App\Models\AircraftStatus::getAOGId())
                ->orderBy('registration_number')
                ->get(),
            'airports' => Airport::active()->orderBy('name')->get(),
            'statuses' => FlightStatus::active()->ordered()->get(),
            'customers' => Customer::select('id', 'first_name', 'last_name', 'company_name', 'email')->orderBy('company_name')->orderBy('last_name')->get(),
        ]);
    }

    public function update(Request $request, ScheduledFlight $scheduledFlight)
    {
        $validated = $request->validate([
            'aircraft_id' => 'required|exists:aircraft,id',
            'flight_status_id' => 'required|exists:flight_statuses,id',
            'primary_customer_id' => 'nullable|exists:customers,id',
            'total_departure_time' => 'required|date',
            'total_arrival_time' => 'nullable|date|after:total_departure_time',
            'notes' => 'nullable|string|max:1000',
            'routes' => 'required|array|min:1',
            'routes.*.id' => 'nullable|exists:flight_schedule_routes,id',
            'routes.*.origin_airport_id' => 'required|exists:airports,id',
            'routes.*.destination_airport_id' => 'required|exists:airports,id|different:routes.*.origin_airport_id',
            'routes.*.actual_departure_time' => 'nullable|date',
            'routes.*.actual_arrival_time' => 'nullable|date',
            'routes.*.notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            // Update the main flight record
            $scheduledFlight->update([
                'aircraft_id' => $validated['aircraft_id'],
                'flight_status_id' => $validated['flight_status_id'],
                'primary_customer_id' => $validated['primary_customer_id'],
                'total_departure_time' => $validated['total_departure_time'],
                'total_arrival_time' => $validated['total_arrival_time'],
                'notes' => $validated['notes'],
            ]);

            // Get existing route IDs
            $existingRouteIds = $scheduledFlight->scheduleRoutes->pluck('id')->toArray();
            $submittedRouteIds = collect($validated['routes'])->pluck('id')->filter()->toArray();

            // Delete routes that were removed
            $routesToDelete = array_diff($existingRouteIds, $submittedRouteIds);
            if (!empty($routesToDelete)) {
                FlightScheduleRoute::whereIn('id', $routesToDelete)->delete();
            }

            // Update or create route segments
            $routeOrder = 1;
            foreach ($validated['routes'] as $routeData) {
                $routeSegmentData = [
                    'scheduled_flight_id' => $scheduledFlight->id,
                    'route_order' => $routeOrder++,
                    'origin_airport_id' => $routeData['origin_airport_id'],
                    'destination_airport_id' => $routeData['destination_airport_id'],
                    'actual_departure_time' => $routeData['actual_departure_time'] ?? null,
                    'actual_arrival_time' => $routeData['actual_arrival_time'] ?? null,
                    'notes' => $routeData['notes'] ?? null,
                ];

                if (!empty($routeData['id'])) {
                    // Update existing route
                    FlightScheduleRoute::where('id', $routeData['id'])->update($routeSegmentData);
                } else {
                    // Create new route
                    FlightScheduleRoute::create($routeSegmentData);
                }
            }

            // Recalculate totals
            $scheduledFlight->calculateTotals();

            DB::commit();

            return redirect()->route('scheduled-flights.index')
                ->with('success', 'Scheduled flight updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withErrors(['error' => 'Failed to update scheduled flight: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(ScheduledFlight $scheduledFlight)
    {
        $flightCode = $scheduledFlight->flight_code;
        $scheduledFlight->delete();

        return redirect()->route('scheduled-flights.index')
            ->with('success', 'Scheduled flight ' . $flightCode . ' deleted successfully.');
    }
}

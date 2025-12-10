<?php

namespace App\Http\Controllers;

use App\Models\FlightRoute;
use App\Models\Airport;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FlightRouteController extends Controller
{
    public function index(Request $request)
    {
        $query = FlightRoute::with(['originAirport', 'destinationAirport']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%')
                  ->orWhereHas('originAirport', function ($subQ) use ($request) {
                      $subQ->where('code', 'like', '%' . $request->search . '%')
                           ->orWhere('name', 'like', '%' . $request->search . '%');
                  })
                  ->orWhereHas('destinationAirport', function ($subQ) use ($request) {
                      $subQ->where('code', 'like', '%' . $request->search . '%')
                           ->orWhere('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        $routes = $query->orderBy('name')->paginate(15);

        return Inertia::render('FlightRoutes/Index', [
            'routes' => $routes,
            'filters' => $request->only(['search', 'is_active']),
        ]);
    }

    public function create()
    {
        return Inertia::render('FlightRoutes/Create', [
            'airports' => Airport::active()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:flight_routes',
            'origin_airport_id' => 'required|exists:airports,id',
            'destination_airport_id' => 'required|exists:airports,id|different:origin_airport_id',
            'distance_km' => 'nullable|numeric|min:0',
            'duration_minutes' => 'nullable|integer|min:1',
            'base_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        // Generate route code from airport codes
        $originAirport = Airport::find($validated['origin_airport_id']);
        $destinationAirport = Airport::find($validated['destination_airport_id']);

        $validated['code'] = FlightRoute::generateRouteCode($originAirport->code, $destinationAirport->code);

        // Check if route code already exists and make it unique
        $baseCode = $validated['code'];
        $counter = 1;
        while (FlightRoute::where('code', $validated['code'])->exists()) {
            $validated['code'] = $baseCode . $counter;
            $counter++;
        }

        $route = FlightRoute::create($validated);

        return redirect()->route('flight-routes.index')
            ->with('success', 'Flight route created successfully.');
    }

    public function show(FlightRoute $flightRoute)
    {
        $flightRoute->load(['originAirport', 'destinationAirport', 'scheduledFlights.aircraft']);

        return Inertia::render('FlightRoutes/Show', [
            'route' => $flightRoute,
        ]);
    }

    public function edit(FlightRoute $flightRoute)
    {
        $flightRoute->load(['originAirport', 'destinationAirport']);

        return Inertia::render('FlightRoutes/Edit', [
            'route' => $flightRoute,
            'airports' => Airport::active()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, FlightRoute $flightRoute)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:flight_routes,name,' . $flightRoute->id,
            'origin_airport_id' => 'required|exists:airports,id',
            'destination_airport_id' => 'required|exists:airports,id|different:origin_airport_id',
            'distance_km' => 'nullable|numeric|min:0',
            'duration_minutes' => 'nullable|integer|min:1',
            'base_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        // Only regenerate code if airports changed
        if ($validated['origin_airport_id'] != $flightRoute->origin_airport_id ||
            $validated['destination_airport_id'] != $flightRoute->destination_airport_id) {

            $originAirport = Airport::find($validated['origin_airport_id']);
            $destinationAirport = Airport::find($validated['destination_airport_id']);

            $newCode = FlightRoute::generateRouteCode($originAirport->code, $destinationAirport->code);

            // Check if new code already exists and make it unique
            $baseCode = $newCode;
            $counter = 1;
            while (FlightRoute::where('code', $newCode)->where('id', '!=', $flightRoute->id)->exists()) {
                $newCode = $baseCode . $counter;
                $counter++;
            }

            $validated['code'] = $newCode;
        }

        $flightRoute->update($validated);

        return redirect()->route('flight-routes.index')
            ->with('success', 'Flight route updated successfully.');
    }

    public function destroy(FlightRoute $flightRoute)
    {
        $routeName = $flightRoute->name;

        // Check if route is used in any scheduled flights
        if ($flightRoute->scheduledFlights()->count() > 0) {
            return redirect()->route('flight-routes.index')
                ->with('error', 'Cannot delete route "' . $routeName . '" because it is used in scheduled flights.');
        }

        $flightRoute->delete();

        return redirect()->route('flight-routes.index')
            ->with('success', 'Flight route "' . $routeName . '" deleted successfully.');
    }
}

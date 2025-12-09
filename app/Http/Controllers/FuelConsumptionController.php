<?php

namespace App\Http\Controllers;

use App\Models\FuelConsumption;
use App\Models\Aircraft;
use App\Models\FuelUnit;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class FuelConsumptionController extends Controller
{
    public function index(): Response
    {
        $fuelConsumption = FuelConsumption::with([
            'aircraft.manufacturer',
            'aircraft.model',
            'fuelUnit'
        ])
        ->orderBy('flight_date', 'desc')
        ->paginate(20);

        return Inertia::render('FeeTracking/FuelConsumption/Index', [
            'fuelConsumption' => $fuelConsumption,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('FeeTracking/FuelConsumption/Create', [
            'aircraft' => Aircraft::with(['manufacturer', 'model'])->get(),
            'fuelUnits' => FuelUnit::where('is_active', true)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'flight_number' => 'required|string|max:255',
            'flight_date' => 'required|date',
            'aircraft_id' => 'required|exists:aircraft,id',
            'route_from' => 'required|string|max:4',
            'route_to' => 'required|string|max:4',
            'fuel_consumed' => 'required|numeric|min:0',
            'fuel_unit_id' => 'required|exists:fuel_units,id',
            'flight_hours' => 'required|numeric|min:0',
            'distance_km' => 'required|numeric|min:0',
            'average_fuel_flow' => 'nullable|numeric|min:0',
            'fuel_efficiency' => 'nullable|numeric|min:0',
            'weather_conditions' => 'nullable|string|max:255',
            'passenger_count' => 'nullable|integer|min:0',
            'cargo_weight' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();

        // Convert route codes to uppercase
        $validated['route_from'] = strtoupper($validated['route_from']);
        $validated['route_to'] = strtoupper($validated['route_to']);

        FuelConsumption::create($validated);

        return redirect()->route('fuel-consumption.index')
            ->with('success', 'Fuel consumption record created successfully.');
    }

    public function show(FuelConsumption $fuelConsumption): Response
    {
        $fuelConsumption->load([
            'aircraft.manufacturer',
            'aircraft.model',
            'fuelUnit',
            'creator',
            'updater'
        ]);

        return Inertia::render('FeeTracking/FuelConsumption/Show', [
            'fuelConsumption' => $fuelConsumption,
        ]);
    }

    public function edit(FuelConsumption $fuelConsumption): Response
    {
        return Inertia::render('FeeTracking/FuelConsumption/Edit', [
            'fuelConsumption' => $fuelConsumption,
            'aircraft' => Aircraft::with(['manufacturer', 'model'])->get(),
            'fuelUnits' => FuelUnit::where('is_active', true)->get(),
        ]);
    }

    public function update(Request $request, FuelConsumption $fuelConsumption): RedirectResponse
    {
        $validated = $request->validate([
            'flight_number' => 'required|string|max:255',
            'flight_date' => 'required|date',
            'aircraft_id' => 'required|exists:aircraft,id',
            'route_from' => 'required|string|max:4',
            'route_to' => 'required|string|max:4',
            'fuel_consumed' => 'required|numeric|min:0',
            'fuel_unit_id' => 'required|exists:fuel_units,id',
            'flight_hours' => 'required|numeric|min:0',
            'distance_km' => 'required|numeric|min:0',
            'average_fuel_flow' => 'nullable|numeric|min:0',
            'fuel_efficiency' => 'nullable|numeric|min:0',
            'weather_conditions' => 'nullable|string|max:255',
            'passenger_count' => 'nullable|integer|min:0',
            'cargo_weight' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['updated_by'] = auth()->id();

        // Convert route codes to uppercase
        $validated['route_from'] = strtoupper($validated['route_from']);
        $validated['route_to'] = strtoupper($validated['route_to']);

        $fuelConsumption->update($validated);

        return redirect()->route('fuel-consumption.index')
            ->with('success', 'Fuel consumption record updated successfully.');
    }

    public function destroy(FuelConsumption $fuelConsumption): RedirectResponse
    {
        $fuelConsumption->delete();

        return redirect()->route('fuel-consumption.index')
            ->with('success', 'Fuel consumption record deleted successfully.');
    }
}

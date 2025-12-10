<?php

namespace App\Http\Controllers;

use App\Models\FlightCargo;
use App\Models\ScheduledFlight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FlightCargoController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'scheduled_flight_id' => 'required|exists:scheduled_flights,id',
            'cargo_name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'cargo_type' => 'required|string|max:100',
            'quantity' => 'required|integer|min:1',
            'weight_kg' => 'required|numeric|min:0.1',
            'volume_m3' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'packaging_type' => 'required|string|max:100',
            'shipper_name' => 'required|string|max:255',
            'shipper_contact' => 'nullable|string|max:255',
            'consignee_name' => 'required|string|max:255',
            'consignee_contact' => 'nullable|string|max:255',
            'declared_value' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'fragile' => 'boolean',
            'hazardous' => 'boolean',
            'requires_refrigeration' => 'boolean',
            'special_handling_instructions' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            // Combine cargo_name and description for the description field
            $validated['description'] = $validated['cargo_name'] . ': ' . $validated['description'];

            // Map fragile to requires special handling
            if (isset($validated['fragile']) && $validated['fragile']) {
                $validated['special_handling_instructions'] = 'FRAGILE - Handle with care. ' . ($validated['special_handling_instructions'] ?? '');
            }

            $cargo = FlightCargo::create($validated);

            // Update flight cargo stats
            $flight = ScheduledFlight::find($validated['scheduled_flight_id']);
            $flight->updatePassengerAndCargoStats();
            $flight->save();

            DB::commit();

            return response()->json([
                'message' => 'Cargo added successfully',
                'cargo' => $cargo->load('scheduledFlight'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error adding cargo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, FlightCargo $cargo)
    {
        $validated = $request->validate([
            'cargo_name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'cargo_type' => 'required|string|max:100',
            'quantity' => 'required|integer|min:1',
            'weight_kg' => 'required|numeric|min:0.1',
            'volume_m3' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'packaging_type' => 'required|string|max:100',
            'shipper_name' => 'required|string|max:255',
            'shipper_contact' => 'nullable|string|max:255',
            'consignee_name' => 'required|string|max:255',
            'consignee_contact' => 'nullable|string|max:255',
            'declared_value' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'fragile' => 'boolean',
            'hazardous' => 'boolean',
            'requires_refrigeration' => 'boolean',
            'special_handling_instructions' => 'nullable|string|max:1000',
            'status' => 'nullable|in:pending,loaded,in_transit,delivered,damaged',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            // Combine cargo_name and description for the description field
            $validated['description'] = $validated['cargo_name'] . ': ' . $validated['description'];

            // Map fragile to requires special handling
            if (isset($validated['fragile']) && $validated['fragile']) {
                $validated['special_handling_instructions'] = 'FRAGILE - Handle with care. ' . ($validated['special_handling_instructions'] ?? '');
            }

            $oldWeight = $cargo->weight_kg;
            $cargo->update($validated);

            // Update flight cargo stats if weight changed
            if ($oldWeight != $cargo->weight_kg) {
                $flight = $cargo->scheduledFlight;
                $flight->updatePassengerAndCargoStats();
                $flight->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'Cargo updated successfully',
                'cargo' => $cargo,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error updating cargo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(FlightCargo $cargo)
    {
        DB::beginTransaction();

        try {
            $flight = $cargo->scheduledFlight;
            $cargo->delete();

            // Update flight cargo stats
            $flight->updatePassengerAndCargoStats();
            $flight->save();

            DB::commit();

            return response()->json([
                'message' => 'Cargo removed successfully',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error removing cargo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, FlightCargo $cargo)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,loaded,in_transit,delivered,damaged',
            'notes' => 'nullable|string|max:500',
        ]);

        $cargo->updateStatus($validated['status'], $validated['notes'] ?? '');

        return response()->json([
            'message' => 'Cargo status updated successfully',
            'cargo' => $cargo,
        ]);
    }
}
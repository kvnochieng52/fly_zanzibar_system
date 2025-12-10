<?php

namespace App\Http\Controllers;

use App\Models\FlightPassenger;
use App\Models\ScheduledFlight;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FlightPassengerController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'scheduled_flight_id' => 'required|exists:scheduled_flights,id',
            'customer_name' => 'required|string|max:255',
            'id_number' => 'nullable|string|max:50',
            'id_type' => 'required|in:passport,national_id,drivers_license,other',
            'nationality' => 'required|string|max:100',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'special_requirements' => 'nullable|string|max:1000',
            'passenger_type' => 'required|in:adult,child,infant',
            'seat_preference' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {

            $passenger = FlightPassenger::create($validated);

            // Update flight passenger count
            $flight = ScheduledFlight::find($validated['scheduled_flight_id']);
            $flight->updatePassengerAndCargoStats();
            $flight->save();

            DB::commit();

            return response()->json([
                'message' => 'Passenger added successfully',
                'passenger' => $passenger->load('scheduledFlight'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error adding passenger: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, FlightPassenger $passenger)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'id_number' => 'nullable|string|max:50',
            'id_type' => 'required|in:passport,national_id,drivers_license,other',
            'nationality' => 'required|string|max:100',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'special_requirements' => 'nullable|string|max:1000',
            'passenger_type' => 'required|in:adult,child,infant',
            'seat_preference' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
            'checked_in' => 'boolean',
        ]);

        $passenger->update($validated);

        return response()->json([
            'message' => 'Passenger updated successfully',
            'passenger' => $passenger,
        ]);
    }

    public function destroy(FlightPassenger $passenger)
    {
        DB::beginTransaction();

        try {
            $flight = $passenger->scheduledFlight;
            $passenger->delete();

            // Update flight passenger count
            $flight->updatePassengerAndCargoStats();
            $flight->save();

            DB::commit();

            return response()->json([
                'message' => 'Passenger removed successfully',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error removing passenger: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkIn(FlightPassenger $passenger)
    {
        $passenger->checkIn();

        return response()->json([
            'message' => 'Passenger checked in successfully',
            'passenger' => $passenger,
        ]);
    }
}
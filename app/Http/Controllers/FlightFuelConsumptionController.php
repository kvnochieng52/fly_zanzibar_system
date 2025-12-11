<?php

namespace App\Http\Controllers;

use App\Models\FlightFuelConsumption;
use App\Models\ScheduledFlight;
use App\Utils\FlightCalculator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class FlightFuelConsumptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = FlightFuelConsumption::with(['scheduledFlight']);

        if ($request->has('scheduled_flight_id')) {
            $query->where('scheduled_flight_id', $request->scheduled_flight_id);
        }

        $fuelConsumption = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $fuelConsumption
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'scheduled_flight_id' => 'required|exists:scheduled_flights,id',
            'fuel_consumed' => 'required|numeric|min:0|max:999999999.99',
            'fuel_unit' => 'required|string|in:liters,gallons,kg',
            'distance_km' => 'nullable|numeric|min:0|max:999999999.99',
            'flight_time_hours' => 'nullable|numeric|min:0|max:999.99',
            'bloc_time_hours' => 'nullable|numeric|min:0|max:999.99',
            'total_time_hours' => 'nullable|numeric|min:0|max:999.99',
            'fuel_burn_rate' => 'nullable|numeric|min:0|max:999999.99',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $validator->validated();

            // Calculate fuel burn rate if not provided
            if (!isset($data['fuel_burn_rate']) && isset($data['fuel_consumed']) && isset($data['total_time_hours']) && $data['total_time_hours'] > 0) {
                $data['fuel_burn_rate'] = $data['fuel_consumed'] / $data['total_time_hours'];
            }

            $fuelConsumption = FlightFuelConsumption::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Fuel consumption recorded successfully',
                'data' => $fuelConsumption->load(['scheduledFlight'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record fuel consumption: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $fuelConsumption = FlightFuelConsumption::with(['scheduledFlight'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $fuelConsumption
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fuel consumption record not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'scheduled_flight_id' => 'required|exists:scheduled_flights,id',
            'fuel_consumed' => 'required|numeric|min:0|max:999999999.99',
            'fuel_unit' => 'required|string|in:liters,gallons,kg',
            'distance_km' => 'nullable|numeric|min:0|max:999999999.99',
            'flight_time_hours' => 'nullable|numeric|min:0|max:999.99',
            'bloc_time_hours' => 'nullable|numeric|min:0|max:999.99',
            'total_time_hours' => 'nullable|numeric|min:0|max:999.99',
            'fuel_burn_rate' => 'nullable|numeric|min:0|max:999999.99',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $fuelConsumption = FlightFuelConsumption::findOrFail($id);
            $data = $validator->validated();

            // Calculate fuel burn rate if not provided
            if (!isset($data['fuel_burn_rate']) && isset($data['fuel_consumed']) && isset($data['total_time_hours']) && $data['total_time_hours'] > 0) {
                $data['fuel_burn_rate'] = $data['fuel_consumed'] / $data['total_time_hours'];
            }

            $fuelConsumption->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Fuel consumption updated successfully',
                'data' => $fuelConsumption->load(['scheduledFlight'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update fuel consumption: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $fuelConsumption = FlightFuelConsumption::findOrFail($id);
            $fuelConsumption->delete();

            return response()->json([
                'success' => true,
                'message' => 'Fuel consumption record deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete fuel consumption record: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate flight metrics based on route data
     */
    public function calculateMetrics(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'scheduled_flight_id' => 'required|exists:scheduled_flights,id',
            'cruise_speed_knots' => 'nullable|integer|min:1|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $scheduledFlight = ScheduledFlight::with(['scheduleRoutes.originAirport', 'scheduleRoutes.destinationAirport', 'aircraft'])
                ->findOrFail($request->scheduled_flight_id);

            $coordinates = [];
            $locationNames = [];

            // Build coordinates array from flight routes
            foreach ($scheduledFlight->scheduleRoutes as $route) {
                if ($route->originAirport && $route->originAirport->latitude && $route->originAirport->longitude) {
                    $coordinates[] = [
                        'lat' => (float)$route->originAirport->latitude,
                        'lon' => (float)$route->originAirport->longitude,
                    ];
                    $locationNames[] = $route->originAirport->name;
                }
                if ($route->destinationAirport && $route->destinationAirport->latitude && $route->destinationAirport->longitude) {
                    $coordinates[] = [
                        'lat' => (float)$route->destinationAirport->latitude,
                        'lon' => (float)$route->destinationAirport->longitude,
                    ];
                    $locationNames[] = $route->destinationAirport->name;
                }
            }

            if (count($coordinates) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient coordinate data for calculation'
                ], 400);
            }

            // Calculate distances
            $totalDistance = FlightCalculator::calculateTotalDistance($coordinates);

            // Calculate flight times
            $cruiseSpeed = $request->cruise_speed_knots ?: ($scheduledFlight->aircraft->cruise_speed ?? 200);
            $numberOfLandings = count($scheduledFlight->scheduleRoutes);

            $flightTime = FlightCalculator::calculateTotalFlightTime(
                $totalDistance['nauticalMiles'],
                $cruiseSpeed,
                $numberOfLandings
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'distance' => $totalDistance,
                    'flight_time' => $flightTime,
                    'number_of_landings' => $numberOfLandings,
                    'cruise_speed_knots' => $cruiseSpeed,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate metrics: ' . $e->getMessage()
            ], 500);
        }
    }
}

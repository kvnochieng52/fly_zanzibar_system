<?php

namespace App\Http\Controllers;

use App\Models\FlightLandingFee;
use App\Models\ScheduledFlight;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class FlightLandingFeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = FlightLandingFee::with(['scheduledFlight', 'airport']);

        if ($request->has('scheduled_flight_id')) {
            $query->where('scheduled_flight_id', $request->scheduled_flight_id);
        }

        $landingFees = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $landingFees
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'scheduled_flight_id' => 'required|exists:scheduled_flights,id',
            'airport_id' => 'required|exists:airports,id',
            'mtow_used_kg' => 'required|numeric|min:0|max:999999999.99',
            'fee_amount' => 'required|numeric|min:0|max:999999999.99',
            'currency' => 'required|string|size:3',
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
            $landingFee = FlightLandingFee::create($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Landing fee added successfully',
                'data' => $landingFee->load(['scheduledFlight', 'airport'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create landing fee: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $landingFee = FlightLandingFee::with(['scheduledFlight', 'airport'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $landingFee
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Landing fee not found'
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
            'airport_id' => 'required|exists:airports,id',
            'mtow_used_kg' => 'required|numeric|min:0|max:999999999.99',
            'fee_amount' => 'required|numeric|min:0|max:999999999.99',
            'currency' => 'required|string|size:3',
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
            $landingFee = FlightLandingFee::findOrFail($id);
            $landingFee->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Landing fee updated successfully',
                'data' => $landingFee->load(['scheduledFlight', 'airport'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update landing fee: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $landingFee = FlightLandingFee::findOrFail($id);
            $landingFee->delete();

            return response()->json([
                'success' => true,
                'message' => 'Landing fee deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete landing fee: ' . $e->getMessage()
            ], 500);
        }
    }
}

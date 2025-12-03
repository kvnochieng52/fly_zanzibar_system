<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Return empty data for now until tables are created
        $quotations = [
            'data' => [],
            'links' => [],
            'total' => 0,
            'from' => 0,
            'to' => 0
        ];

        $stats = [
            'total' => 0,
            'accepted' => 0,
            'pending' => 0,
            'expired' => 0
        ];

        return Inertia::render('Quotations/Index', [
            'quotations' => $quotations,
            'stats' => $stats
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Return empty data for now until tables are created
        $customers = [];
        $currencies = [
            ['id' => 1, 'code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$'],
            ['id' => 2, 'code' => 'EUR', 'name' => 'Euro', 'symbol' => '€'],
            ['id' => 3, 'code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£'],
            ['id' => 4, 'code' => 'TZS', 'name' => 'Tanzanian Shilling', 'symbol' => 'TSh']
        ];
        $statuses = [
            ['id' => 1, 'name' => 'Draft', 'code' => 'DRAFT'],
            ['id' => 2, 'name' => 'Sent', 'code' => 'SENT'],
            ['id' => 3, 'name' => 'Accepted', 'code' => 'ACCEPTED']
        ];

        return Inertia::render('Quotations/Create', [
            'customers' => $customers,
            'currencies' => $currencies,
            'statuses' => $statuses
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'quote_number' => 'required|string|max:255',
            'quote_date' => 'required|date',
            'valid_until' => 'required|date|after:quote_date',
            'customer_id' => 'required|integer',
            'departure_airport' => 'nullable|string|max:4',
            'arrival_airport' => 'nullable|string|max:4',
            'departure_date' => 'nullable|date',
            'return_date' => 'nullable|date|after_or_equal:departure_date',
            'passengers' => 'nullable|integer|min:1|max:50',
            'aircraft_type' => 'nullable|string|max:100',
            'route_description' => 'nullable|string',
            'currency_id' => 'required|integer',
            'total_amount' => 'required|numeric|min:0',
            'status_id' => 'required|integer',
            'terms_conditions' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        // For now, simulate successful creation since tables don't exist yet
        // In real implementation, this would be:
        // $quotation = Quotation::create($validated + ['created_by' => auth()->id()]);

        return redirect()->route('quotations.index')->with('success',
            'Quotation "' . $validated['quote_number'] . '" has been created successfully. This will be saved to database once tables are set up.'
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

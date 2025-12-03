<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Return empty data for now until tables are created
        $customers = [
            'data' => [],
            'links' => [],
            'total' => 0,
            'from' => 0,
            'to' => 0
        ];

        $stats = [
            'total' => 0,
            'individual' => 0,
            'corporate' => 0,
            'active' => 0
        ];

        return Inertia::render('Customers/Index', [
            'customers' => $customers,
            'stats' => $stats
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $currencies = [
            ['id' => 1, 'code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$'],
            ['id' => 2, 'code' => 'EUR', 'name' => 'Euro', 'symbol' => '€'],
            ['id' => 3, 'code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£'],
            ['id' => 4, 'code' => 'TZS', 'name' => 'Tanzanian Shilling', 'symbol' => 'TSh']
        ];

        return Inertia::render('Customers/Create', [
            'currencies' => $currencies
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'customer_code' => 'required|string|max:255',
            'type' => 'required|in:individual,corporate',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'fax' => 'nullable|string|max:20',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state_province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'payment_terms' => 'required|in:immediate,net_7,net_15,net_30,net_45,net_60',
            'credit_limit' => 'nullable|numeric|min:0',
            'default_currency_id' => 'nullable|integer',
            'notes' => 'nullable|string'
        ];

        // Add conditional validation based on customer type
        if ($request->type === 'individual') {
            $rules['first_name'] = 'required|string|max:100';
            $rules['last_name'] = 'required|string|max:100';
            $rules['title'] = 'nullable|string|max:10';
        } else {
            $rules['company_name'] = 'required|string|max:255';
            $rules['contact_person'] = 'nullable|string|max:255';
            $rules['tax_number'] = 'nullable|string|max:50';
            $rules['registration_number'] = 'nullable|string|max:50';
        }

        $validated = $request->validate($rules);

        // For now, simulate successful creation since tables don't exist yet
        // In real implementation, this would be:
        // $customer = Customer::create($validated + ['created_by' => auth()->id()]);

        // Simulate success response
        session()->flash('success', 'Customer created successfully!');

        return redirect()->route('customers.index')->with('success',
            'Customer "' . ($validated['company_name'] ?? $validated['first_name'] . ' ' . $validated['last_name']) . '" has been created successfully. This will be saved to database once tables are set up.'
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

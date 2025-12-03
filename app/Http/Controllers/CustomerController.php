<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Currency;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => Customer::count(),
            'individual' => Customer::where('type', 'individual')->count(),
            'corporate' => Customer::where('type', 'corporate')->count(),
            'active' => Customer::where('is_active', true)->count()
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
        $currencies = Currency::where('is_active', true)
            ->orderBy('code')
            ->get(['id', 'code', 'name', 'symbol']);

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

        // Create customer in database
        $customer = Customer::create($validated + [
            'created_by' => auth()->id() ?? 1 // Default to user ID 1 if not authenticated
        ]);

        return redirect()->route('customers.index')->with('success',
            'Customer "' . ($customer->company_name ?? $customer->first_name . ' ' . $customer->last_name) . '" has been created successfully.'
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

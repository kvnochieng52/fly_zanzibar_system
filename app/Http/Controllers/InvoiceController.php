<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Return empty data for now until tables are created
        $invoices = [
            'data' => [],
            'links' => [],
            'total' => 0,
            'from' => 0,
            'to' => 0
        ];

        $stats = [
            'total' => 0,
            'paid' => 0,
            'unpaid' => 0,
            'overdue' => 0,
            'total_amount' => 0,
            'paid_amount' => 0,
            'outstanding_amount' => 0
        ];

        return Inertia::render('Invoices/Index', [
            'invoices' => $invoices,
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
            ['id' => 1, 'name' => 'Unpaid', 'code' => 'UNPAID'],
            ['id' => 2, 'name' => 'Partially Paid', 'code' => 'PARTIALLY_PAID'],
            ['id' => 3, 'name' => 'Paid', 'code' => 'PAID'],
            ['id' => 4, 'name' => 'Overdue', 'code' => 'OVERDUE']
        ];

        return Inertia::render('Invoices/Create', [
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
        //
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

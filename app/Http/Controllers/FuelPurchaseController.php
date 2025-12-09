<?php

namespace App\Http\Controllers;

use App\Models\FuelPurchase;
use App\Models\Aircraft;
use App\Models\Airport;
use App\Models\FuelSupplier;
use App\Models\FuelUnit;
use App\Models\Currency;
use App\Models\PaymentStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class FuelPurchaseController extends Controller
{
    public function index(): Response
    {
        $fuelPurchases = FuelPurchase::with([
            'aircraft.manufacturer',
            'aircraft.model',
            'airport',
            'fuelSupplier',
            'fuelUnit',
            'currency',
            'paymentStatus'
        ])
        ->orderBy('purchase_date', 'desc')
        ->paginate(20);

        return Inertia::render('FeeTracking/FuelPurchases/Index', [
            'fuelPurchases' => $fuelPurchases,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('FeeTracking/FuelPurchases/Create', [
            'aircraft' => Aircraft::with(['manufacturer', 'model'])->get(),
            'airports' => Airport::where('is_active', true)->get(),
            'fuelSuppliers' => FuelSupplier::where('is_active', true)->get(),
            'fuelUnits' => FuelUnit::where('is_active', true)->get(),
            'currencies' => Currency::where('is_active', true)->get(),
            'paymentStatuses' => PaymentStatus::where('is_active', true)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'purchase_number' => 'required|string|max:255|unique:fuel_purchases',
            'purchase_date' => 'required|date',
            'aircraft_id' => 'required|exists:aircraft,id',
            'airport_id' => 'required|exists:airports,id',
            'fuel_supplier_id' => 'required|exists:fuel_suppliers,id',
            'fuel_unit_id' => 'required|exists:fuel_units,id',
            'quantity' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'invoice_reference' => 'nullable|string|max:255',
            'invoice_date' => 'nullable|date',
            'payment_status_id' => 'required|exists:payment_statuses,id',
            'payment_date' => 'nullable|date',
            'supplier_document' => 'nullable|string',
            'fuel_grade' => 'nullable|string|max:255',
            'density' => 'nullable|numeric|min:0',
            'fuel_quality_rating' => 'nullable|numeric|min:0|max:10',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();

        FuelPurchase::create($validated);

        return redirect()->route('fuel-purchases.index')
            ->with('success', 'Fuel purchase record created successfully.');
    }

    public function show(FuelPurchase $fuelPurchase): Response
    {
        $fuelPurchase->load([
            'aircraft.manufacturer',
            'aircraft.model',
            'airport',
            'fuelSupplier',
            'fuelUnit',
            'currency',
            'paymentStatus',
            'creator',
            'updater'
        ]);

        return Inertia::render('FeeTracking/FuelPurchases/Show', [
            'fuelPurchase' => $fuelPurchase,
        ]);
    }

    public function edit(FuelPurchase $fuelPurchase): Response
    {
        return Inertia::render('FeeTracking/FuelPurchases/Edit', [
            'fuelPurchase' => $fuelPurchase,
            'aircraft' => Aircraft::with(['manufacturer', 'model'])->get(),
            'airports' => Airport::where('is_active', true)->get(),
            'fuelSuppliers' => FuelSupplier::where('is_active', true)->get(),
            'fuelUnits' => FuelUnit::where('is_active', true)->get(),
            'currencies' => Currency::where('is_active', true)->get(),
            'paymentStatuses' => PaymentStatus::where('is_active', true)->get(),
        ]);
    }

    public function update(Request $request, FuelPurchase $fuelPurchase): RedirectResponse
    {
        $validated = $request->validate([
            'purchase_number' => 'required|string|max:255|unique:fuel_purchases,purchase_number,' . $fuelPurchase->id,
            'purchase_date' => 'required|date',
            'aircraft_id' => 'required|exists:aircraft,id',
            'airport_id' => 'required|exists:airports,id',
            'fuel_supplier_id' => 'required|exists:fuel_suppliers,id',
            'fuel_unit_id' => 'required|exists:fuel_units,id',
            'quantity' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'invoice_reference' => 'nullable|string|max:255',
            'invoice_date' => 'nullable|date',
            'payment_status_id' => 'required|exists:payment_statuses,id',
            'payment_date' => 'nullable|date',
            'supplier_document' => 'nullable|string',
            'fuel_grade' => 'nullable|string|max:255',
            'density' => 'nullable|numeric|min:0',
            'fuel_quality_rating' => 'nullable|numeric|min:0|max:10',
            'notes' => 'nullable|string',
        ]);

        $validated['updated_by'] = auth()->id();

        $fuelPurchase->update($validated);

        return redirect()->route('fuel-purchases.index')
            ->with('success', 'Fuel purchase record updated successfully.');
    }

    public function destroy(FuelPurchase $fuelPurchase): RedirectResponse
    {
        $fuelPurchase->delete();

        return redirect()->route('fuel-purchases.index')
            ->with('success', 'Fuel purchase record deleted successfully.');
    }
}

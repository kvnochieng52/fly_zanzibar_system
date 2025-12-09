<?php

namespace App\Http\Controllers;

use App\Models\LandingFee;
use App\Models\Aircraft;
use App\Models\Airport;
use App\Models\Currency;
use App\Models\PaymentStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class LandingFeeController extends Controller
{
    public function index(): Response
    {
        $landingFees = LandingFee::with([
            'aircraft',
            'airport',
            'currency',
            'paymentStatus'
        ])
        ->orderBy('flight_date', 'desc')
        ->paginate(20);

        return Inertia::render('FeeTracking/LandingFees/Index', [
            'landingFees' => $landingFees,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('FeeTracking/LandingFees/Create', [
            'aircraft' => Aircraft::with(['manufacturer', 'model'])->get(),
            'airports' => Airport::where('is_active', true)->get(),
            'currencies' => Currency::where('is_active', true)->get(),
            'paymentStatuses' => PaymentStatus::where('is_active', true)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'flight_number' => 'required|string|max:255',
            'flight_date' => 'required|date',
            'aircraft_id' => 'required|exists:aircraft,id',
            'airport_id' => 'required|exists:airports,id',
            'mtow_used' => 'required|numeric|min:0',
            'fee_amount' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'payment_status_id' => 'required|exists:payment_statuses,id',
            'payment_date' => 'nullable|date',
            'receipt_reference' => 'nullable|string|max:255',
            'invoice_reference' => 'nullable|string|max:255',
            'authority_document' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_calculated_auto' => 'boolean',
            'manual_override_amount' => 'nullable|numeric|min:0',
            'override_reason' => 'nullable|string|max:255',
        ]);

        $validated['created_by'] = auth()->id();

        LandingFee::create($validated);

        return redirect()->route('landing-fees.index')
            ->with('success', 'Landing fee record created successfully.');
    }

    public function show(LandingFee $landingFee): Response
    {
        $landingFee->load([
            'aircraft',
            'airport',
            'currency',
            'paymentStatus',
            'creator',
            'updater'
        ]);

        return Inertia::render('FeeTracking/LandingFees/Show', [
            'landingFee' => $landingFee,
        ]);
    }

    public function edit(LandingFee $landingFee): Response
    {
        return Inertia::render('FeeTracking/LandingFees/Edit', [
            'landingFee' => $landingFee,
            'aircraft' => Aircraft::with(['manufacturer', 'model'])->get(),
            'airports' => Airport::where('is_active', true)->get(),
            'currencies' => Currency::where('is_active', true)->get(),
            'paymentStatuses' => PaymentStatus::where('is_active', true)->get(),
        ]);
    }

    public function update(Request $request, LandingFee $landingFee): RedirectResponse
    {
        $validated = $request->validate([
            'flight_number' => 'required|string|max:255',
            'flight_date' => 'required|date',
            'aircraft_id' => 'required|exists:aircraft,id',
            'airport_id' => 'required|exists:airports,id',
            'mtow_used' => 'required|numeric|min:0',
            'fee_amount' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'payment_status_id' => 'required|exists:payment_statuses,id',
            'payment_date' => 'nullable|date',
            'receipt_reference' => 'nullable|string|max:255',
            'invoice_reference' => 'nullable|string|max:255',
            'authority_document' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_calculated_auto' => 'boolean',
            'manual_override_amount' => 'nullable|numeric|min:0',
            'override_reason' => 'nullable|string|max:255',
        ]);

        $validated['updated_by'] = auth()->id();

        $landingFee->update($validated);

        return redirect()->route('landing-fees.index')
            ->with('success', 'Landing fee record updated successfully.');
    }

    public function destroy(LandingFee $landingFee): RedirectResponse
    {
        $landingFee->delete();

        return redirect()->route('landing-fees.index')
            ->with('success', 'Landing fee record deleted successfully.');
    }
}

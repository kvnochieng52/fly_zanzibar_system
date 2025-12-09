<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    public function index(Request $request)
    {
        $receipts = Receipt::with(['invoice', 'customer', 'paymentMethod'])
            ->when($request->search, function ($query, $search) {
                return $query->where('receipt_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('company_name', 'like', "%{$search}%")
                          ->orWhere('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%");
                    });
            })
            ->when($request->customer_id, function ($query, $customerId) {
                return $query->where('customer_id', $customerId);
            })
            ->when($request->payment_method_id, function ($query, $methodId) {
                return $query->where('payment_method_id', $methodId);
            })
            ->orderBy('date', 'desc')
            ->paginate(20)
            ->withQueryString();

        $customers = Customer::select('id', 'company_name', 'first_name', 'last_name')
            ->whereHas('receipts')
            ->get();

        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        return Inertia::render('Receipts/Index', [
            'receipts' => $receipts,
            'customers' => $customers,
            'paymentMethods' => $paymentMethods,
            'filters' => $request->only(['search', 'customer_id', 'payment_method_id'])
        ]);
    }

    public function create(Request $request)
    {
        $invoices = Invoice::with('customer')
            ->where('outstanding_balance', '>', 0)
            ->orderBy('invoice_date', 'desc')
            ->get();

        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        return Inertia::render('Receipts/Create', [
            'invoices' => $invoices,
            'paymentMethods' => $paymentMethods,
            'preselectedInvoiceId' => $request->query('invoice_id')
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'invoice_id' => 'required|exists:invoices,id',
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'transaction_reference' => 'nullable|string|max:255',
            'received_by' => 'required|string|max:255',
            'notes' => 'nullable|string'
        ]);

        $invoice = Invoice::find($validated['invoice_id']);

        if ($validated['payment_amount'] > $invoice->outstanding_balance) {
            return back()->withErrors([
                'payment_amount' => 'Payment amount cannot exceed outstanding balance of ' . $invoice->outstanding_balance
            ]);
        }

        DB::transaction(function () use ($validated, $invoice) {
            // Create receipt
            $receipt = Receipt::create([
                ...$validated,
                'customer_id' => $invoice->customer_id,
                'created_by' => Auth::id()
            ]);

            // Update invoice paid amount and outstanding balance
            $invoice->increment('paid_amount', $validated['payment_amount']);
            $invoice->decrement('outstanding_balance', $validated['payment_amount']);

            // Update payment status
            if ($invoice->outstanding_balance <= 0) {
                $invoice->update(['payment_status' => 'paid']);
            } elseif ($invoice->paid_amount > 0) {
                $invoice->update(['payment_status' => 'partially_paid']);
            }
        });

        return redirect()->route('receipts.index')
            ->with('success', 'Receipt created successfully.');
    }

    public function show(Receipt $receipt)
    {
        $receipt->load(['invoice', 'customer', 'paymentMethod', 'createdBy']);

        return Inertia::render('Receipts/Show', [
            'receipt' => $receipt
        ]);
    }

    public function edit(Receipt $receipt)
    {
        $receipt->load(['invoice', 'customer']);

        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        return Inertia::render('Receipts/Edit', [
            'receipt' => $receipt,
            'paymentMethods' => $paymentMethods
        ]);
    }

    public function update(Request $request, Receipt $receipt)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'transaction_reference' => 'nullable|string|max:255',
            'received_by' => 'required|string|max:255',
            'notes' => 'nullable|string'
        ]);

        $oldAmount = $receipt->payment_amount;
        $newAmount = $validated['payment_amount'];
        $difference = $newAmount - $oldAmount;

        $invoice = $receipt->invoice;

        // Check if new amount would exceed outstanding balance + current payment
        if ($difference > ($invoice->outstanding_balance + $oldAmount)) {
            return back()->withErrors([
                'payment_amount' => 'Payment amount cannot exceed available balance'
            ]);
        }

        DB::transaction(function () use ($receipt, $validated, $invoice, $difference) {
            $receipt->update($validated);

            // Update invoice amounts
            $invoice->increment('paid_amount', $difference);
            $invoice->decrement('outstanding_balance', $difference);

            // Update payment status
            if ($invoice->outstanding_balance <= 0) {
                $invoice->update(['payment_status' => 'paid']);
            } elseif ($invoice->paid_amount > 0) {
                $invoice->update(['payment_status' => 'partially_paid']);
            } else {
                $invoice->update(['payment_status' => 'unpaid']);
            }
        });

        return redirect()->route('receipts.index')
            ->with('success', 'Receipt updated successfully.');
    }

    public function destroy(Receipt $receipt)
    {
        DB::transaction(function () use ($receipt) {
            $invoice = $receipt->invoice;

            // Reverse the payment
            $invoice->decrement('paid_amount', $receipt->payment_amount);
            $invoice->increment('outstanding_balance', $receipt->payment_amount);

            // Update payment status
            if ($invoice->outstanding_balance >= $invoice->total_amount) {
                $invoice->update(['payment_status' => 'unpaid']);
            } elseif ($invoice->outstanding_balance > 0) {
                $invoice->update(['payment_status' => 'partially_paid']);
            }

            $receipt->delete();
        });

        return redirect()->route('receipts.index')
            ->with('success', 'Receipt deleted successfully.');
    }

    public function downloadPdf(Receipt $receipt)
    {
        $receipt->load(['invoice.currency', 'customer', 'paymentMethod', 'createdBy']);

        $pdf = Pdf::loadView('pdf.receipt', compact('receipt'));

        return $pdf->download('receipt-' . $receipt->receipt_number . '.pdf');
    }
}

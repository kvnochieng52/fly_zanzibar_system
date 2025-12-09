<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = Invoice::with(['customer', 'currency'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => Invoice::count(),
            'paid' => Invoice::where('payment_status', 'paid')->count(),
            'unpaid' => Invoice::where('payment_status', 'unpaid')->count(),
            'overdue' => Invoice::where('payment_status', 'overdue')->count(),
            'total_amount' => Invoice::sum('total_amount'),
            'paid_amount' => Invoice::sum('paid_amount'),
            'outstanding_amount' => Invoice::sum('outstanding_balance')
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
        $customers = Customer::where('is_active', true)
            ->orderBy('type')
            ->orderBy('company_name')
            ->orderBy('first_name')
            ->get(['id', 'type', 'first_name', 'last_name', 'company_name', 'email']);

        $currencies = Currency::where('is_active', true)
            ->orderBy('code')
            ->get(['id', 'code', 'name', 'symbol']);

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
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'currency_id' => 'required|exists:currencies,id',
            'invoice_number' => 'required|string|max:255|unique:invoices,invoice_number',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'subject' => 'nullable|string|max:255',
            'status' => 'required|in:UNPAID,PARTIALLY_PAID,PAID,OVERDUE',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:500',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            // Create the invoice
            $invoice = Invoice::create([
                'customer_id' => $validated['customer_id'],
                'currency_id' => $validated['currency_id'],
                'invoice_number' => $validated['invoice_number'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'subject' => $validated['subject'],
                'payment_status' => strtolower($validated['status']),
                'subtotal' => $validated['subtotal'],
                'tax_rate' => $validated['tax_rate'],
                'tax_amount' => $validated['tax_amount'],
                'total_amount' => $validated['total_amount'],
                'paid_amount' => 0,
                'outstanding_balance' => $validated['total_amount'],
                'notes' => $validated['notes'],
                'created_by' => auth()->id() ?? 1
            ]);

            // Create invoice items
            foreach ($validated['items'] as $index => $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                    'sort_order' => $index + 1
                ]);
            }

            DB::commit();

            return redirect()->route('invoices.index')->with('success',
                'Invoice "' . $invoice->invoice_number . '" has been created successfully.'
            );

        } catch (\Exception $e) {
            DB::rollback();

            return back()->withErrors([
                'error' => 'Failed to create invoice. Please try again.'
            ])->withInput();
        }
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

    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load(['customer', 'currency', 'items', 'quotation']);

        $pdf = Pdf::loadView('pdf.invoice', compact('invoice'));

        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }
}

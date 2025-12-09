<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\StatementService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class StatementController extends Controller
{
    protected $statementService;

    public function __construct(StatementService $statementService)
    {
        $this->statementService = $statementService;
    }

    public function index()
    {
        $customers = Customer::select('id', 'company_name', 'first_name', 'last_name', 'type')
            ->whereHas('invoices')
            ->orderBy('company_name')
            ->orderBy('first_name')
            ->get();

        return Inertia::render('Statements/Index', [
            'customers' => $customers
        ]);
    }

    public function show(Request $request, Customer $customer)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : null;

        $statement = $this->statementService->generateCustomerStatement(
            $customer,
            $startDate,
            $endDate
        );

        return Inertia::render('Statements/Show', [
            'statement' => $statement
        ]);
    }

    public function agingReport(Request $request)
    {
        $asOfDate = $request->as_of_date ? Carbon::parse($request->as_of_date) : null;

        $agingReport = $this->statementService->generateAllCustomersAgingReport($asOfDate);

        return Inertia::render('Statements/AgingReport', [
            'agingReport' => $agingReport,
            'asOfDate' => $asOfDate ?? Carbon::now()
        ]);
    }

    public function download(Request $request, Customer $customer)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : null;

        $statement = $this->statementService->generateCustomerStatement(
            $customer,
            $startDate,
            $endDate
        );

        // You could implement PDF generation here using a package like DomPDF or similar
        return response()->json([
            'message' => 'PDF download functionality would be implemented here',
            'statement' => $statement
        ]);
    }
}

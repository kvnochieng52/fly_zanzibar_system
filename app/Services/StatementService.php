<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Receipt;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StatementService
{
    public function generateCustomerStatement(Customer $customer, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? Carbon::now()->subMonths(3);
        $endDate = $endDate ?? Carbon::now();

        // Get invoices within date range
        $invoices = Invoice::where('customer_id', $customer->id)
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->with(['items', 'receipts'])
            ->orderBy('invoice_date')
            ->get();

        // Get receipts within date range
        $receipts = Receipt::where('customer_id', $customer->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['invoice', 'paymentMethod'])
            ->orderBy('date')
            ->get();

        // Build transaction history
        $transactions = $this->buildTransactionHistory($invoices, $receipts);

        // Calculate running balance
        $runningBalance = $this->calculateRunningBalance($transactions);

        // Get aging analysis
        $agingAnalysis = $this->generateAgingAnalysis($customer, $endDate);

        return [
            'customer' => $customer,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'transactions' => $runningBalance,
            'summary' => [
                'opening_balance' => $this->getOpeningBalance($customer, $startDate),
                'total_invoiced' => $invoices->sum('total_amount'),
                'total_paid' => $receipts->sum('payment_amount'),
                'outstanding_balance' => $invoices->sum('outstanding_balance'),
            ],
            'aging' => $agingAnalysis,
        ];
    }

    public function generateAgingAnalysis(Customer $customer, ?Carbon $asOfDate = null): array
    {
        $asOfDate = $asOfDate ?? Carbon::now();

        $overdue = Invoice::where('customer_id', $customer->id)
            ->where('outstanding_balance', '>', 0)
            ->where('due_date', '<', $asOfDate);

        return [
            'current' => Invoice::where('customer_id', $customer->id)
                ->where('outstanding_balance', '>', 0)
                ->where('due_date', '>=', $asOfDate)
                ->sum('outstanding_balance'),

            '1_30_days' => $overdue->clone()
                ->where('due_date', '>=', $asOfDate->clone()->subDays(30))
                ->sum('outstanding_balance'),

            '31_60_days' => $overdue->clone()
                ->where('due_date', '>=', $asOfDate->clone()->subDays(60))
                ->where('due_date', '<', $asOfDate->clone()->subDays(30))
                ->sum('outstanding_balance'),

            '61_90_days' => $overdue->clone()
                ->where('due_date', '>=', $asOfDate->clone()->subDays(90))
                ->where('due_date', '<', $asOfDate->clone()->subDays(60))
                ->sum('outstanding_balance'),

            'over_90_days' => $overdue->clone()
                ->where('due_date', '<', $asOfDate->clone()->subDays(90))
                ->sum('outstanding_balance'),
        ];
    }

    private function buildTransactionHistory(Collection $invoices, Collection $receipts): Collection
    {
        $transactions = collect();

        // Add invoices
        foreach ($invoices as $invoice) {
            $transactions->push([
                'date' => $invoice->invoice_date,
                'type' => 'invoice',
                'reference' => $invoice->invoice_number,
                'description' => $invoice->subject ?: 'Invoice',
                'debit' => $invoice->total_amount,
                'credit' => 0,
                'details' => $invoice,
            ]);
        }

        // Add receipts
        foreach ($receipts as $receipt) {
            $transactions->push([
                'date' => $receipt->date,
                'type' => 'payment',
                'reference' => $receipt->receipt_number,
                'description' => 'Payment - ' . $receipt->paymentMethod->name,
                'debit' => 0,
                'credit' => $receipt->payment_amount,
                'details' => $receipt,
            ]);
        }

        return $transactions->sortBy('date');
    }

    private function calculateRunningBalance(Collection $transactions): Collection
    {
        $runningBalance = 0;

        return $transactions->map(function ($transaction) use (&$runningBalance) {
            $runningBalance += $transaction['debit'] - $transaction['credit'];
            $transaction['running_balance'] = $runningBalance;
            return $transaction;
        });
    }

    private function getOpeningBalance(Customer $customer, Carbon $startDate): float
    {
        $invoicesTotal = Invoice::where('customer_id', $customer->id)
            ->where('invoice_date', '<', $startDate)
            ->sum('total_amount');

        $paymentsTotal = Receipt::where('customer_id', $customer->id)
            ->where('date', '<', $startDate)
            ->sum('payment_amount');

        return $invoicesTotal - $paymentsTotal;
    }

    public function generateAllCustomersAgingReport(?Carbon $asOfDate = null): Collection
    {
        $asOfDate = $asOfDate ?? Carbon::now();

        return Customer::with('currency')
            ->whereHas('invoices', function ($query) {
                $query->where('outstanding_balance', '>', 0);
            })
            ->get()
            ->map(function ($customer) use ($asOfDate) {
                $aging = $this->generateAgingAnalysis($customer, $asOfDate);
                $customer->aging_analysis = $aging;
                $customer->total_outstanding = array_sum($aging);
                return $customer;
            })
            ->filter(function ($customer) {
                return $customer->total_outstanding > 0;
            })
            ->sortByDesc('total_outstanding');
    }
}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.4;
        }

        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }

        .company-info {
            display: table-cell;
            vertical-align: top;
            width: 60%;
        }

        .logo-section {
            display: table-cell;
            text-align: right;
            vertical-align: top;
            width: 40%;
        }

        .logo {
            max-height: 80px;
            max-width: 200px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin: 0 0 5px 0;
        }

        .company-details {
            font-size: 12px;
            color: #666;
            margin: 0;
        }

        .document-title {
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            margin: 20px 0;
            color: #007bff;
            text-transform: uppercase;
        }

        .invoice-info {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .invoice-details, .customer-details {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 0 10px;
        }

        .invoice-details {
            padding-left: 0;
        }

        .customer-details {
            padding-right: 0;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }

        .detail-row {
            margin-bottom: 8px;
            font-size: 12px;
        }

        .detail-label {
            font-weight: bold;
            display: inline-block;
            width: 100px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
            border: 1px solid #ddd;
        }

        .items-table th {
            background-color: #007bff;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-size: 12px;
            font-weight: bold;
        }

        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #eee;
            font-size: 12px;
        }

        .items-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .summary-section {
            margin-top: 30px;
            display: table;
            width: 100%;
        }

        .summary-spacer {
            display: table-cell;
            width: 60%;
        }

        .summary-table {
            display: table-cell;
            width: 40%;
        }

        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .summary-label {
            display: table-cell;
            font-size: 12px;
            font-weight: bold;
            text-align: right;
            padding-right: 15px;
            width: 60%;
        }

        .summary-value {
            display: table-cell;
            font-size: 12px;
            text-align: right;
            width: 40%;
            padding: 5px 10px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
        }

        .total-row .summary-label,
        .total-row .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #007bff;
        }

        .total-row .summary-value {
            background-color: #007bff;
            color: white;
        }

        .payment-status {
            text-align: center;
            margin: 30px 0;
            padding: 15px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px;
        }

        .status-paid {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-partially-paid {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-unpaid {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .terms-section {
            margin: 30px 0;
            font-size: 11px;
            color: #666;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }

        @if($invoice->payment_status === 'paid')
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(40, 167, 69, 0.1);
            font-weight: bold;
            z-index: -1;
        }
        @endif
    </style>
</head>
<body>
    <!-- Watermark for paid invoices -->
    @if($invoice->payment_status === 'paid')
    <div class="watermark">PAID</div>
    @endif

    <!-- Header -->
    <div class="header">
        <div class="company-info">
            <h1 class="company-name">Zanzibar Fly System</h1>
            <p class="company-details">
                Aviation Management Solutions<br>
                P.O. Box 12345, Zanzibar<br>
                Phone: +255 123 456 789<br>
                Email: info@zanzibarfly.com<br>
                Website: www.zanzibarfly.com
            </p>
        </div>
        <div class="logo-section">
            <!-- Company Logo -->
            <img src="{{ public_path('images/logo.png') }}" alt="Company Logo" class="logo" style="display: block; margin: 0 auto;">
        </div>
    </div>

    <!-- Document Title -->
    <h1 class="document-title">Invoice</h1>

    <!-- Invoice & Customer Information -->
    <div class="invoice-info">
        <div class="invoice-details">
            <div class="section-title">Invoice Details</div>
            <div class="detail-row">
                <span class="detail-label">Invoice #:</span>
                <strong>{{ $invoice->invoice_number }}</strong>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date:</span>
                {{ $invoice->invoice_date->format('d F Y') }}
            </div>
            <div class="detail-row">
                <span class="detail-label">Due Date:</span>
                {{ $invoice->due_date->format('d F Y') }}
            </div>
            <div class="detail-row">
                <span class="detail-label">Currency:</span>
                {{ $invoice->currency->code }} ({{ $invoice->currency->symbol }})
            </div>
            @if($invoice->quotation_id && $invoice->quotation)
            <div class="detail-row">
                <span class="detail-label">Quotation:</span>
                {{ $invoice->quotation->quotation_number ?? 'N/A' }}
            </div>
            @endif
        </div>

        <div class="customer-details">
            <div class="section-title">Bill To</div>
            <div class="detail-row">
                <strong>{{ $invoice->customer->company_name ?: $invoice->customer->first_name . ' ' . $invoice->customer->last_name }}</strong>
            </div>
            @if($invoice->customer->company_name && ($invoice->customer->first_name || $invoice->customer->last_name))
            <div class="detail-row">
                Attn: {{ $invoice->customer->first_name }} {{ $invoice->customer->last_name }}
            </div>
            @endif
            @if($invoice->customer->address)
            <div class="detail-row">
                {{ $invoice->customer->address }}
            </div>
            @endif
            @if($invoice->customer->city || $invoice->customer->state || $invoice->customer->postal_code)
            <div class="detail-row">
                {{ $invoice->customer->city }}{{ $invoice->customer->city && ($invoice->customer->state || $invoice->customer->postal_code) ? ', ' : '' }}{{ $invoice->customer->state }} {{ $invoice->customer->postal_code }}
            </div>
            @endif
            @if($invoice->customer->country)
            <div class="detail-row">
                {{ $invoice->customer->country }}
            </div>
            @endif
            @if($invoice->customer->email)
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                {{ $invoice->customer->email }}
            </div>
            @endif
            @if($invoice->customer->phone)
            <div class="detail-row">
                <span class="detail-label">Phone:</span>
                {{ $invoice->customer->phone }}
            </div>
            @endif
        </div>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 45%;">Description</th>
                <th style="width: 10%;" class="text-center">Qty</th>
                <th style="width: 15%;" class="text-right">Unit Price</th>
                <th style="width: 10%;" class="text-right">Tax %</th>
                <th style="width: 15%;" class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $item->description }}</strong>
                    @if($item->details)
                    <br><small style="color: #666;">{{ $item->details }}</small>
                    @endif
                </td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">{{ $invoice->currency->symbol }}{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">{{ $item->tax_rate }}%</td>
                <td class="text-right">{{ $invoice->currency->symbol }}{{ number_format($item->total_amount, 2) }}</td>
            </tr>
            @endforeach
            @if($invoice->items->count() === 0)
            <tr>
                <td colspan="6" class="text-center" style="padding: 30px; color: #999;">
                    No items found for this invoice.
                </td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- Summary Section -->
    <div class="summary-section">
        <div class="summary-spacer"></div>
        <div class="summary-table">
            <div class="summary-row">
                <div class="summary-label">Subtotal:</div>
                <div class="summary-value">{{ $invoice->currency->symbol }}{{ number_format($invoice->subtotal, 2) }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Tax ({{ $invoice->tax_rate }}%):</div>
                <div class="summary-value">{{ $invoice->currency->symbol }}{{ number_format($invoice->tax_amount, 2) }}</div>
            </div>
            <div class="summary-row total-row">
                <div class="summary-label">Total:</div>
                <div class="summary-value">{{ $invoice->currency->symbol }}{{ number_format($invoice->total_amount, 2) }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Paid:</div>
                <div class="summary-value">{{ $invoice->currency->symbol }}{{ number_format($invoice->paid_amount, 2) }}</div>
            </div>
            <div class="summary-row total-row">
                <div class="summary-label">Balance Due:</div>
                <div class="summary-value">{{ $invoice->currency->symbol }}{{ number_format($invoice->outstanding_balance, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Payment Status -->
    <div class="payment-status status-{{ str_replace('_', '-', $invoice->payment_status) }}">
        STATUS: {{ strtoupper(str_replace('_', ' ', $invoice->payment_status)) }}
    </div>

    @if($invoice->notes)
    <!-- Notes -->
    <div style="margin: 30px 0;">
        <div class="section-title">Notes</div>
        <div style="font-size: 12px; background-color: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #007bff;">
            {{ $invoice->notes }}
        </div>
    </div>
    @endif

    <!-- Terms and Conditions -->
    <div class="terms-section">
        <div class="section-title">Terms and Conditions</div>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li>Payment is due within {{ $invoice->due_date->diffInDays($invoice->invoice_date) }} days from invoice date.</li>
            <li>Late payments may incur additional charges.</li>
            <li>All services are subject to our standard terms and conditions.</li>
            <li>Please reference the invoice number on all payments.</li>
        </ul>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Thank you for choosing Zanzibar Fly System for your aviation needs!</p>
        <p>Generated on {{ now()->format('d F Y \a\t H:i:s') }}</p>
    </div>
</body>
</html>
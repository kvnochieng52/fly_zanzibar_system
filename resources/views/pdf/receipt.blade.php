<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt - {{ $receipt->receipt_number }}</title>
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
            font-size: 28px;
            font-weight: bold;
            margin: 20px 0;
            color: #007bff;
            text-transform: uppercase;
        }

        .receipt-info {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .receipt-details, .customer-details {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 0 10px;
        }

        .receipt-details {
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
            width: 120px;
        }

        .payment-summary {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin: 30px 0;
        }

        .payment-amount {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
            margin: 15px 0;
        }

        .invoice-details {
            margin: 30px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
        }

        .invoice-header {
            background-color: #007bff;
            color: white;
            padding: 12px 15px;
            font-weight: bold;
            font-size: 14px;
        }

        .invoice-body {
            padding: 15px;
        }

        .invoice-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .invoice-label {
            display: table-cell;
            width: 40%;
            font-weight: bold;
            font-size: 12px;
        }

        .invoice-value {
            display: table-cell;
            width: 60%;
            font-size: 12px;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }

        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 0 20px;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
            font-size: 12px;
            font-weight: bold;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(0, 123, 255, 0.1);
            font-weight: bold;
            z-index: -1;
        }
    </style>
</head>
<body>
    <!-- Watermark -->
    <div class="watermark">PAID</div>

    <!-- Header -->
    <div class="header">
        <div class="company-info">
            <h1 class="company-name">Zanzibar Fly System</h1>
            <p class="company-details">
                Aviation Management Solutions<br>
                P.O. Box 12345, Zanzibar<br>
                Phone: +255 123 456 789<br>
                Email: info@zanzibarfly.com
            </p>
        </div>
        <div class="logo-section">
            <!-- Company Logo -->
            <img src="{{ public_path('images/logo.png') }}" alt="Company Logo" class="logo" style="display: block; margin: 0 auto;">
        </div>
    </div>

    <!-- Document Title -->
    <h1 class="document-title">Payment Receipt</h1>

    <!-- Receipt & Customer Information -->
    <div class="receipt-info">
        <div class="receipt-details">
            <div class="section-title">Receipt Details</div>
            <div class="detail-row">
                <span class="detail-label">Receipt Number:</span>
                <strong>{{ $receipt->receipt_number }}</strong>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date:</span>
                {{ $receipt->date->format('d F Y') }}
            </div>
            <div class="detail-row">
                <span class="detail-label">Received By:</span>
                {{ $receipt->received_by }}
            </div>
            <div class="detail-row">
                <span class="detail-label">Payment Method:</span>
                {{ $receipt->paymentMethod->name }}
            </div>
            @if($receipt->transaction_reference)
            <div class="detail-row">
                <span class="detail-label">Transaction Ref:</span>
                {{ $receipt->transaction_reference }}
            </div>
            @endif
        </div>

        <div class="customer-details">
            <div class="section-title">Customer Information</div>
            <div class="detail-row">
                <span class="detail-label">Customer:</span>
                {{ $receipt->customer->company_name ?: $receipt->customer->first_name . ' ' . $receipt->customer->last_name }}
            </div>
            @if($receipt->customer->company_name)
            <div class="detail-row">
                <span class="detail-label">Contact Person:</span>
                {{ $receipt->customer->first_name }} {{ $receipt->customer->last_name }}
            </div>
            @endif
            @if($receipt->customer->email)
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                {{ $receipt->customer->email }}
            </div>
            @endif
            @if($receipt->customer->phone)
            <div class="detail-row">
                <span class="detail-label">Phone:</span>
                {{ $receipt->customer->phone }}
            </div>
            @endif
        </div>
    </div>

    <!-- Payment Summary -->
    <div class="payment-summary">
        <div style="text-align: center; font-size: 16px; font-weight: bold; margin-bottom: 10px;">
            PAYMENT RECEIVED
        </div>
        <div class="payment-amount">
            ${{ number_format($receipt->payment_amount, 2) }}
        </div>
        <div style="text-align: center; font-size: 12px; color: #666;">
            ({{ ucwords(\NumberFormatter::create('en', \NumberFormatter::SPELLOUT)->format($receipt->payment_amount)) }} Dollars)
        </div>
    </div>

    <!-- Invoice Details -->
    <div class="invoice-details">
        <div class="invoice-header">
            Invoice Details - {{ $receipt->invoice->invoice_number }}
        </div>
        <div class="invoice-body">
            <div class="invoice-row">
                <div class="invoice-label">Invoice Date:</div>
                <div class="invoice-value">{{ $receipt->invoice->invoice_date->format('d F Y') }}</div>
            </div>
            <div class="invoice-row">
                <div class="invoice-label">Invoice Amount:</div>
                <div class="invoice-value">${{ number_format($receipt->invoice->total_amount, 2) }}</div>
            </div>
            <div class="invoice-row">
                <div class="invoice-label">Previous Payments:</div>
                <div class="invoice-value">${{ number_format($receipt->invoice->paid_amount - $receipt->payment_amount, 2) }}</div>
            </div>
            <div class="invoice-row">
                <div class="invoice-label">This Payment:</div>
                <div class="invoice-value"><strong>${{ number_format($receipt->payment_amount, 2) }}</strong></div>
            </div>
            <div class="invoice-row" style="border-top: 1px solid #ddd; padding-top: 8px; margin-top: 8px;">
                <div class="invoice-label"><strong>Remaining Balance:</strong></div>
                <div class="invoice-value"><strong>${{ number_format($receipt->invoice->outstanding_balance, 2) }}</strong></div>
            </div>
        </div>
    </div>

    @if($receipt->notes)
    <!-- Notes -->
    <div style="margin: 30px 0;">
        <div class="section-title">Notes</div>
        <div style="font-size: 12px; background-color: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #007bff;">
            {{ $receipt->notes }}
        </div>
    </div>
    @endif

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">Customer Signature</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">Authorized Signature</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This is a computer-generated receipt and is valid without signature.</p>
        <p>Thank you for your business!</p>
        <p>Generated on {{ now()->format('d F Y \a\t H:i:s') }}</p>
    </div>
</body>
</html>
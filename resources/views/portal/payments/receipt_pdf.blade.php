<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt - {{ $payment->receipt_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        .receipt {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #333;
            padding: 30px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-info {
            margin-bottom: 20px;
        }
        .receipt-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .receipt-number {
            font-size: 18px;
            font-weight: bold;
            color: #666;
        }
        .payment-details {
            margin: 30px 0;
        }
        .row {
            display: flex;
            margin-bottom: 15px;
        }
        .col-6 {
            width: 50%;
            padding: 0 10px;
        }
        .label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .value {
            font-size: 16px;
        }
        .amount-row {
            font-size: 20px;
            font-weight: bold;
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(0,0,0,0.1);
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="watermark">PAID</div>
    
    <div class="receipt">
        <div class="header">
            <div class="company-info">
                <h2>{{ $company_name }}</h2>
                <p>{{ $company_address }}</p>
                <p>{{ $company_email }} | {{ $company_phone }}</p>
            </div>
            <div class="receipt-title">OFFICIAL RECEIPT</div>
            <div class="receipt-number">Receipt #: {{ $receipt_number }}</div>
        </div>

        <div class="payment-details">
            <div class="row">
                <div class="col-6">
                    <div class="label">Date:</div>
                    <div class="value">{{ $date }}</div>
                </div>
                <div class="col-6">
                    <div class="label">Application Reference:</div>
                    <div class="value">{{ $application->reference }}</div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="label">Applicant Name:</div>
                    <div class="value">{{ $application->applicant->name }}</div>
                </div>
                <div class="col-6">
                    <div class="label">Payment Method:</div>
                    <div class="value">{{ ucfirst($payment->method) }}</div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="label">Payment Reference:</div>
                    <div class="value">{{ $reference }}</div>
                </div>
                <div class="col-6">
                    <div class="label">Payment Date:</div>
                    <div class="value">{{ $payment->payment_date ?? $payment->created_at->format('Y-m-d') }}</div>
                </div>
            </div>

            @if($payment->bank_name)
            <div class="row">
                <div class="col-6">
                    <div class="label">Bank:</div>
                    <div class="value">{{ $payment->bank_name }}</div>
                </div>
                <div class="col-6">
                    <div class="label">Status:</div>
                    <div class="value">
                        <span style="color: green; font-weight: bold;">✓ PAID</span>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="amount-row">
            <div class="row">
                <div class="col-6">
                    <div class="label">Total Amount Paid:</div>
                </div>
                <div class="col-6" style="text-align: right;">
                    <div class="value">{{ $currency }} {{ number_format($amount, 2) }}</div>
                </div>
            </div>
        </div>

        @if($application->application_type)
        <div class="row">
            <div class="col-12">
                <div class="label">Application Type:</div>
                <div class="value">{{ ucfirst($application->application_type) }} - {{ ucfirst($application->request_type ?? 'new') }}</div>
            </div>
        </div>
        @endif

        <div class="footer">
            <p><strong>This is an official receipt generated by Zimbabwe Media Commission</strong></p>
            <p>Thank you for your payment. This receipt serves as proof of payment.</p>
            <p>Generated on: {{ now()->format('d M Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>

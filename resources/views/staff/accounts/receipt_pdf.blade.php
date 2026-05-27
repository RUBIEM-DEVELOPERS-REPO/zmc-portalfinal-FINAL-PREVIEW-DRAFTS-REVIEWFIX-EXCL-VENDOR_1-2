<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $receipt_number ?? $application->reference }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #334155; line-height: 1.5; font-size: 14px; }
        .container { padding: 40px; }
        .header { display: table; width: 100%; border-bottom: 2px solid #1a1a1a; padding-bottom: 20px; margin-bottom: 30px; }
        .company-info { display: table-cell; vertical-align: top; }
        .receipt-label { display: table-cell; vertical-align: top; text-align: right; }
        .receipt-label h1 { margin: 0; color: #1a1a1a; font-size: 28px; text-transform: uppercase; letter-spacing: 2px; }
        .section { margin-bottom: 30px; }
        .section-title { font-weight: bold; font-size: 12px; text-transform: uppercase; color: #64748b; margin-bottom: 10px; border-bottom: 1px solid #f1f5f9; padding-bottom: 5px; }
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .details-table td { padding: 8px 0; vertical-align: top; }
        .details-table td.label { font-weight: bold; width: 35%; color: #475569; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 20px; }
        .amount-box { background: #f8fafc; border: 2px solid #1a1a1a; padding: 20px; text-align: right; border-radius: 8px; }
        .amount-box .total-label { font-size: 14px; color: #64748b; }
        .amount-box .total-value { font-size: 28px; font-weight: bold; color: #1a1a1a; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .badge-success { background-color: #dcfce7; color: #166534; }
        .watermark { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 80px; color: rgba(0,0,0,0.03); font-weight: bold; text-transform: uppercase; letter-spacing: 20px; z-index: -1; }
    </style>
</head>
<body>
    <div class="watermark">OFFICIAL RECEIPT</div>
    <div class="container">
        <div class="header">
            <div class="company-info">
                <div style="font-weight: bold; font-size: 20px; color: #1a1a1a;">{{ $company_name }}</div>
                <div style="color: #64748b; margin-top: 4px;">{{ $company_address }}</div>
                <div style="color: #64748b;">{{ $company_phone }} | {{ $company_email }}</div>
            </div>
            <div class="receipt-label">
                <h1>Receipt</h1>
                <div style="margin-top: 10px; color: #64748b;">
                    @if($receipt_number && $receipt_number !== 'N/A')
                    <div style="font-size: 16px; font-weight: bold; color: #1a1a1a;">{{ $receipt_number }}</div>
                    @endif
                    <div style="margin-top: 4px;">Date: {{ $date }}</div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Recipient</div>
            <div style="font-size: 16px; font-weight: bold; color: #1a1a1a;">{{ $application->applicant?->name ?? 'N/A' }}</div>
            <div>{{ $application->applicant?->email }}</div>
            @if(isset($application->applicant?->profile_data['address']))
                <div>{{ $application->applicant?->profile_data['address'] }}</div>
            @endif
        </div>

        <div class="section">
            <div class="section-title">Payment Information</div>
            <table class="details-table">
                <tr>
                    <td class="label">Application Reference:</td>
                    <td>{{ $application->reference }}</td>
                </tr>
                <tr>
                    <td class="label">Payment For:</td>
                    <td>{{ strtoupper($application->application_type ?? 'Accreditation') }} — {{ ucfirst($application->request_type ?? 'New') }}</td>
                </tr>
                <tr>
                    <td class="label">Payment Method:</td>
                    <td>
                        @php
                          $methodLabels = [
                            'paynow_reference' => 'PayNow',
                            'paynow' => 'PayNow',
                            'proof' => 'Proof of Payment',
                            'proof_upload' => 'Proof of Payment',
                            'waiver' => 'Fee Waiver',
                            'cash' => 'Cash',
                            'transfer' => 'Bank Transfer',
                            'general' => 'Other',
                          ];
                        @endphp
                        {{ $methodLabels[$payment_method] ?? ucfirst($payment_method ?? 'N/A') }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Payment Date:</td>
                    <td>{{ $payment_date instanceof \Carbon\Carbon ? $payment_date->format('d M Y, H:i') : $payment_date }}</td>
                </tr>
                <tr>
                    <td class="label">Payment Status:</td>
                    <td><span class="badge badge-success">Confirmed</span></td>
                </tr>
<<<<<<< HEAD
                @if($reference !== 'N/A')
                <tr>
                    <td class="label">Payment Reference:</td>
                    <td>{{ $reference }}</td>
=======
                @if($application->paynow_reference || ($payment && $payment->reference))
                <tr>
                    <td class="label">Payment Reference:</td>
                    <td>{{ $application->paynow_reference ?? $payment->reference ?? '—' }}</td>
>>>>>>> fcc1ae98e3f498fbea6f4be4c875cef714a0817b
                </tr>
                @endif
            </table>
        </div>

        <div class="amount-box">
            <div class="total-label">Amount Paid</div>
<<<<<<< HEAD
            <div class="total-value">{{ $currency }} {{ number_format($amount, 2) }}</div>
            <div style="font-size: 11px; color: #94a3b8; margin-top: 5px;">* This receipt is valid only for the specified application once payment is confirmed.</div>
=======
            <div class="total-value">USD {{ number_format($amount, 2) }}</div>
            <div style="font-size: 11px; color: #94a3b8; margin-top: 5px;">This receipt is valid only for the specified application.</div>
>>>>>>> fcc1ae98e3f498fbea6f4be4c875cef714a0817b
        </div>

        <div class="footer">
            Thank you for your payment.<br>
            This is a computer-generated receipt and is valid without signature.<br>
            &copy; {{ date('Y') }} {{ $company_name }}. Generated via ZMC Portal.
        </div>
    </div>
</body>
</html>

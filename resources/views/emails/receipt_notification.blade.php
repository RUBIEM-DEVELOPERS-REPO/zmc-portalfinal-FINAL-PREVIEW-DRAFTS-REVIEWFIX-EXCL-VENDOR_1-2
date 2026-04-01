<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt - Zimbabwe Media Commission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #2e7d32;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
            border-top: none;
        }
        .receipt-details {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border: 1px solid #e0e0e0;
        }
        .receipt-details h3 {
            color: #2e7d32;
            margin-top: 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: bold;
            color: #666;
        }
        .detail-value {
            font-weight: 600;
        }
        .amount {
            font-size: 20px;
            color: #2e7d32;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #2e7d32;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .company-info {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background: white;
            border-radius: 5px;
            border: 1px solid #e0e0e0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Zimbabwe Media Commission</h1>
        <p>Payment Receipt Confirmation</p>
    </div>

    <div class="content">
        <p>Dear {{ $application->applicant->name }},</p>

        <p>We are pleased to confirm that your payment has been successfully processed. Thank you for your prompt payment.</p>

        <div class="receipt-details">
            <h3>Payment Receipt Details</h3>
            
            <div class="detail-row">
                <span class="detail-label">Receipt Number:</span>
                <span class="detail-value">{{ $receiptNumber }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Application Reference:</span>
                <span class="detail-value">{{ $application->reference }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Payment Date:</span>
                <span class="detail-value">{{ $payment->created_at->format('d M Y') }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Payment Method:</span>
                <span class="detail-value">{{ ucfirst($payment->method) }}</span>
            </div>
            
            @if($payment->bank_name)
            <div class="detail-row">
                <span class="detail-label">Bank:</span>
                <span class="detail-value">{{ $payment->bank_name }}</span>
            </div>
            @endif
            
            <div class="detail-row">
                <span class="detail-label">Amount Paid:</span>
                <span class="detail-value amount">{{ $currency }} {{ number_format($amount, 2) }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value" style="color: #2e7d32;">✓ Paid</span>
            </div>
        </div>

        <p>A copy of your official receipt is attached to this email for your records. You may also access your receipt anytime through your portal payment history.</p>

        <div class="company-info">
            <strong>Zimbabwe Media Commission</strong><br>
            109 Rotten Row, Harare, Zimbabwe<br>
            Email: info@zmc.co.zw<br>
            Phone: +263 242 703351
        </div>

        <p>If you have any questions regarding this payment or need further assistance, please do not hesitate to contact us.</p>

        <p>Best regards,<br>
        Zimbabwe Media Commission<br>
        Finance Department</p>
    </div>

    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} Zimbabwe Media Commission. All rights reserved.</p>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt</title>
    <style>
        @page {
            size: A4;
            margin: 0.5cm;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background: #fff;
            position: relative;
        }
        
        /* Watermark */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            opacity: 0.1;
            z-index: -1;
            font-size: 80px;
            font-weight: bold;
            color: #000;
        }
        
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #333;
            padding: 30px;
            background: white;
            position: relative;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            margin-bottom: 10px;
        }
        
        .organization-name {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
            color: #1a1a1a;
        }
        
        .receipt-title {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin: 15px 0;
        }
        
        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 12px;
        }
        
        .receipt-number {
            font-weight: bold;
            color: #0066cc;
        }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
            color: #333;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px dotted #ccc;
        }
        
        .info-label {
            font-weight: bold;
            color: #555;
        }
        
        .info-value {
            color: #000;
        }
        
        .payment-details {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        
        .amount {
            font-size: 18px;
            font-weight: bold;
            color: #0066cc;
        }
        
        .payment-method {
            background: #e8f4f8;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }
        
        .signature-box {
            width: 45%;
            text-align: center;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
            height: 40px;
        }
        
        .signature-label {
            font-size: 12px;
            color: #666;
        }
        
        .verified-badge {
            background: #4CAF50;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            margin: 10px 0;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 11px;
            color: #666;
            border-top: 2px solid #333;
            padding-top: 20px;
        }
        
        .reference-highlight {
            background: #ffeb3b;
            padding: 2px 5px;
            border-radius: 3px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Watermark -->
    <div class="watermark">ZMC</div>
    
    <div class="receipt-container">
        <!-- Header -->
        <div class="header">
            <img src="{{ asset('images/zmc-logo.png') }}" alt="ZMC Logo" class="logo">
            <div class="organization-name">ZIMBABWE MEDIA COMMISSION</div>
            <div class="receipt-title">OFFICIAL PAYMENT RECEIPT</div>
            <div class="receipt-info">
                <div>
                    <strong>Receipt No:</strong> <span class="receipt-number">{{ $receipt->receipt_number ?? 'ZMC-REC-' . str_pad($receipt->id, 6, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div>
                    <strong>Date:</strong> {{ $receipt->created_at->format('d M Y H:i') }}
                </div>
            </div>
        </div>

        <!-- Applicant Information -->
        <div class="section">
            <div class="section-title">APPLICANT INFORMATION</div>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Applicant Name:</span>
                    <span class="info-value">{{ $receipt->applicant->name ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $receipt->applicant->email ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Phone:</span>
                    <span class="info-value">{{ $receipt->applicant->phone ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">ID Number:</span>
                    <span class="info-value">{{ $receipt->applicant->id_number ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Application Information -->
        <div class="section">
            <div class="section-title">APPLICATION DETAILS</div>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Reference Number:</span>
                    <span class="info-value reference-highlight">{{ $receipt->application->reference ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Application Type:</span>
                    <span class="info-value">{{ ucfirst($receipt->application->application_type ?? 'N/A') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Request Type:</span>
                    <span class="info-value">{{ ucfirst($receipt->application->request_type ?? 'N/A') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Submission Date:</span>
                    <span class="info-value">{{ $receipt->application->submitted_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="section">
            <div class="section-title">PAYMENT INFORMATION</div>
            <div class="payment-details">
                <div class="info-item">
                    <span class="info-label">Payment Reference:</span>
                    <span class="info-value">{{ $receipt->payment_reference ?? $receipt->application->reference ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Payment Date:</span>
                    <span class="info-value">{{ $receipt->payment_date->format('d M Y H:i') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Payment Method:</span>
                    <span class="info-value">{{ strtoupper($receipt->payment_method ?? 'N/A') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Amount Paid:</span>
                    <span class="info-value amount">${{ number_format($receipt->amount, 2) }}</span>
                </div>
            </div>
            
            <div class="payment-method">
                PAYMENT METHOD: {{ strtoupper($receipt->payment_method ?? 'N/A') }}
                @if($receipt->payment_method === 'paynow')
                    <div style="font-size: 10px; margin-top: 5px;">PayNow Transaction ID: {{ $receipt->transaction_id ?? 'N/A' }}</div>
                @endif
            </div>
        </div>

        <!-- ZMC Address -->
        <div style="text-align: center; margin: 20px 0; font-size: 12px; color: #666;">
            <strong>Zimbabwe Media Commission</strong><br>
            108 Swan Drive, Alexandra Park, Harare, Zimbabwe<br>
            Tel: +263 242 752 860/1 | Email: info@zmc.co.zw | Web: www.zmc.co.zw
        </div>

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">Accounts Officer Signature</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">Received By (Applicant)</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div><strong>IMPORTANT:</strong> This is an official receipt issued by the Zimbabwe Media Commission.</div>
            <div>Keep this receipt for your records.</div>
            <div>© {{ date('Y') }} Zimbabwe Media Commission. All rights reserved.</div>
        </div>
    </div>
</body>
</html>

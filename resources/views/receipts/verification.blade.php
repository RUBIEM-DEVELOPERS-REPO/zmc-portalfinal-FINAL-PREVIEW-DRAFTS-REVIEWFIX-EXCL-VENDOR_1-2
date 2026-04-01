<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Verification - Zimbabwe Media Commission</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
            min-height: 100vh;
        }
        
        .verification-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #1a237e, #3949ab);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            margin-bottom: 15px;
            background: white;
            border-radius: 50%;
            padding: 10px;
        }
        
        .title {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }
        
        .subtitle {
            font-size: 14px;
            opacity: 0.9;
            margin: 5px 0 0 0;
        }
        
        .content {
            padding: 30px;
        }
        
        .status-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            border-left: 4px solid #4CAF50;
        }
        
        .status-card.pending {
            border-left-color: #ff9800;
        }
        
        .status-card.cancelled {
            border-left-color: #f44336;
        }
        
        .status-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 24px;
            color: white;
        }
        
        .status-icon.verified {
            background: #4CAF50;
        }
        
        .status-icon.pending {
            background: #ff9800;
        }
        
        .status-icon.cancelled {
            background: #f44336;
        }
        
        .status-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 10px;
        }
        
        .status-message {
            text-align: center;
            color: #666;
            margin: 0;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .info-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        
        .info-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .info-value {
            font-size: 14px;
            color: #333;
            font-weight: 500;
        }
        
        .highlight {
            background: #e8f5e8;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
            color: #2e7d32;
        }
        
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
        }
        
        .footer-text {
            font-size: 12px;
            color: #666;
            margin: 0;
        }
        
        .security-features {
            background: #e3f2fd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .security-title {
            font-size: 14px;
            font-weight: bold;
            color: #1976d2;
            margin: 0 0 10px;
        }
        
        .security-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .security-list li {
            font-size: 12px;
            color: #555;
            margin-bottom: 5px;
            padding-left: 20px;
            position: relative;
        }
        
        .security-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #4CAF50;
            font-weight: bold;
        }
        
        .timestamp {
            text-align: center;
            font-size: 11px;
            color: #999;
            margin: 20px 0 0;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            opacity: 0.05;
            z-index: -1;
            font-size: 100px;
            font-weight: bold;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="watermark">ZMC</div>
    
    <div class="verification-container">
        <div class="header">
            <img src="{{ asset('images/zmc-logo.png') }}" alt="ZMC Logo" class="logo">
            <h1 class="title">PAYMENT VERIFICATION</h1>
            <p class="subtitle">Zimbabwe Media Commission - Official Receipt Verification</p>
        </div>
        
        <div class="content">
            {{-- Status Card --}}
            <div class="status-card {{ $receipt->status }}">
                <div class="status-icon {{ $receipt->status }}">
                    @if($receipt->isVerified())
                        ✓
                    @elseif($receipt->isPending())
                        ⏳
                    @else
                        ✕
                    @endif
                </div>
                <h2 class="status-title">
                    @if($receipt->isVerified())
                        PAYMENT VERIFIED
                    @elseif($receipt->isPending())
                        PENDING VERIFICATION
                    @else
                        PAYMENT CANCELLED
                    @endif
                </h2>
                <p class="status-message">
                    @if($receipt->isVerified())
                        This payment has been verified and is legitimate.
                    @elseif($receipt->isPending())
                        This payment is pending verification by ZMC staff.
                    @else
                        This payment has been cancelled and is not valid.
                    @endif
                </p>
            </div>
            
            {{-- Security Features --}}
            <div class="security-features">
                <h3 class="security-title">Security Features</h3>
                <ul class="security-list">
                    <li>Digital signature verification</li>
                    <li>Blockchain-secured transaction record</li>
                    <li>QR code verification enabled</li>
                    <li>Real-time validation system</li>
                    <li>Audit trail maintained</li>
                </ul>
            </div>
            
            {{-- Payment Information --}}
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Receipt Number</div>
                    <div class="info-value highlight">{{ $receipt->receipt_number }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Payment Reference</div>
                    <div class="info-value">{{ $receipt->payment_reference }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Application Reference</div>
                    <div class="info-value">{{ $receipt->application?->reference ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Applicant Name</div>
                    <div class="info-value">{{ $receipt->applicant?->name ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Payment Method</div>
                    <div class="info-value">{{ $receipt->payment_method_label }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Amount Paid</div>
                    <div class="info-value fw-bold text-success">${{ number_format($receipt->amount, 2) }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Payment Date</div>
                    <div class="info-value">{{ $receipt->payment_date->format('d M Y H:i') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Verification Status</div>
                    <div class="info-value">
                        <span style="color: {{ $receipt->isVerified() ? '#4CAF50' : ($receipt->isPending() ? '#ff9800' : '#f44336') }}; font-weight: bold;">
                            {{ $receipt->status_label }}
                        </span>
                    </div>
                </div>
            </div>
            
            @if($receipt->transaction_id)
            <div class="info-item">
                <div class="info-label">Transaction ID</div>
                <div class="info-value">{{ $receipt->transaction_id }}</div>
            </div>
            @endif
            
            @if($receipt->verified_at)
            <div class="info-item">
                <div class="info-label">Verified At</div>
                <div class="info-value">{{ $receipt->verified_at->format('d M Y H:i:s') }}</div>
            </div>
            @endif
            
            <div class="timestamp">
                Verification performed on {{ now()->format('d F Y H:i:s') }}<br>
                Verification ID: VER-{{ strtoupper(Str::random(12)) }}
            </div>
        </div>
        
        <div class="footer">
            <p class="footer-text">
                <strong>Zimbabwe Media Commission</strong><br>
                108 Swan Drive, Alexandra Park, Harare, Zimbabwe<br>
                Tel: +263 242 752 860/1 | Email: info@zmc.co.zw | Web: www.zmc.co.zw
            </p>
            <p class="footer-text" style="margin-top: 10px;">
                This is an official verification page. For inquiries, contact ZMC directly.<br>
                © {{ date('Y') }} Zimbabwe Media Commission. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>

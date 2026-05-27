<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZMC Portal - Temporary Credentials</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #334155;
            background-color: #f8fafc;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .content {
            padding: 30px;
        }
        .alert {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-title {
            font-weight: 700;
            color: #92400e;
            margin-bottom: 5px;
        }
        .credentials-box {
            background: #f1f5f9;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .credentials-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #64748b;
            margin-bottom: 8px;
        }
        .credentials-value {
            font-family: 'Courier New', monospace;
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            background: #ffffff;
            padding: 10px 20px;
            border-radius: 6px;
            display: inline-block;
            letter-spacing: 0.15em;
        }
        .role-badge {
            display: inline-block;
            background: #3b82f6;
            color: #ffffff;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin: 10px 0;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .info-label {
            font-weight: 600;
            color: #64748b;
        }
        .info-value {
            color: #1e293b;
        }
        .footer {
            background: #f8fafc;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
        .btn {
            display: inline-block;
            background: #3b82f6;
            color: #ffffff;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 20px;
        }
        .warning {
            color: #dc2626;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏛️ Zimbabwe Media Commission</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Portal Access - Temporary Credentials</p>
        </div>
        
        <div class="content">
            <p>Hello <strong>{{ $user->name }}</strong>,</p>
            
            <p>Your account has been assigned a new role in the ZMC Portal system. Below are your temporary login credentials:</p>
            
            <div class="alert">
                <div class="alert-title">⚠️ Important Security Notice</div>
                <p style="margin: 0; font-size: 14px;">These temporary credentials are valid for <strong>24 hours only</strong>. You will be required to change your password upon first login.</p>
            </div>

            <div style="text-align: center;">
                <span class="role-badge">{{ str_replace('_', ' ', $role) }}</span>
            </div>

            <div class="credentials-box">
                <div class="credentials-label">Temporary Password</div>
                <div class="credentials-value">{{ $tempPassword }}</div>
                
                <div style="margin-top: 20px; font-size: 14px;">
                    <div class="info-item">
                        <span class="info-label">Username:</span>
                        <span class="info-value">{{ $user->email }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Expires At:</span>
                        <span class="info-value warning">{{ $expiresAt }}</span>
                    </div>
                </div>
            </div>

            <p style="text-align: center;">
                <a href="{{ url('/login') }}" class="btn">Access Portal</a>
            </p>

            <div style="margin-top: 30px; padding: 15px; background: #fee2e2; border-radius: 6px; border-left: 4px solid #dc2626;">
                <p style="margin: 0; font-size: 13px; color: #991b1b;">
                    <strong>Security Tips:</strong><br>
                    • Do not share these credentials with anyone<br>
                    • Log out when finished using the system<br>
                    • Report any suspicious activity immediately
                </p>
            </div>
        </div>
        
        <div class="footer">
            <p>This is an automated message from Zimbabwe Media Commission Portal.</p>
            <p>If you did not request this role change, please contact IT support immediately.</p>
            <p style="margin-top: 10px; font-size: 11px;">
                © {{ date('Y') }} Zimbabwe Media Commission. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Accreditation Report</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #facc15; padding-bottom: 15px; }
        .header h1 { margin: 0; font-size: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #1e293b; color: white; padding: 8px; text-align: left; }
        td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Zimbabwe Media Commission</h1>
        <div>Monthly Accreditation Report - {{ $month }}</div>
    </div>
    
    <p><strong>Generated:</strong> {{ $generated_at }}</p>
    
    <h2>Monthly Trends</h2>
    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th>Submitted</th>
                <th>Issued/Reviewed</th>
                <th>Returned for Correction</th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthly_trends as $trend)
            <tr>
                <td>{{ $trend->month }}</td>
                <td>{{ $trend->total_submitted }}</td>
                <td>{{ $trend->total_approved }}</td>
                <td>{{ $trend->total_rejected }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

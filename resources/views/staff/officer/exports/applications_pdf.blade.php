<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2e7d32;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #2e7d32;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Zimbabwe Media Commission</h1>
        <p>{{ $title }}</p>
        <p>Generated on: {{ $exportDate }}</p>
    </div>

    @if($applications->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Reference Number</th>
                <th>Applicant Name</th>
                <th>Email Address</th>
                <th>Application Type</th>
                <th>Request Type</th>
                <th>Submission Date</th>
                <th>Submission Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applications as $app)
            <tr>
                <td>{{ $app->reference ?? ('#' . $app->id) }}</td>
                <td>{{ $app->applicant->name ?? '—' }}</td>
                <td>{{ $app->applicant->email ?? '—' }}</td>
                <td>{{ $app->applicationTypeLabel() }}</td>
                <td>{{ ucfirst($app->request_type ?? '—') }}</td>
                <td>{{ optional($app->submitted_at)->format('Y-m-d') ?? optional($app->created_at)->format('Y-m-d') }}</td>
                <td>{{ optional($app->submitted_at)->format('H:i') ?? optional($app->created_at)->format('H:i') }}</td>
                <td>{{ $app->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <p><strong>Total Records:</strong> {{ $applications->count() }}</p>
    </div>
    @else
    <div class="no-data">
        <p>No applications found matching the selected criteria.</p>
    </div>
    @endif

    <div class="footer">
        <p>Zimbabwe Media Commission - 109 Rotten Row, Harare, Zimbabwe</p>
        <p>Phone: +263 242 703351 | Email: info@zmc.co.zw</p>
        <p>Page {{ \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage() }}</p>
    </div>
</body>
</html>

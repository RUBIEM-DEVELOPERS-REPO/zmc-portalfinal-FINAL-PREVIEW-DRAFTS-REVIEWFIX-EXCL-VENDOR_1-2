<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1cm;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header h1 {
            font-size: 18px;
            margin: 0;
            color: #333;
        }
        
        .header p {
            font-size: 10px;
            margin: 5px 0;
            color: #666;
        }
        
        .filters {
            margin-bottom: 15px;
            font-size: 9px;
            color: #666;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        
        th {
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
            font-weight: bold;
            white-space: nowrap;
        }
        
        td {
            border: 1px solid #ddd;
            padding: 4px;
            vertical-align: top;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .status-active {
            color: #28a745;
            font-weight: bold;
        }
        
        .status-expired {
            color: #dc3545;
            font-weight: bold;
        }
        
        .badge {
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        
        .badge-primary {
            background-color: #007bff;
            color: white;
        }
        
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: black;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Zimbabwe Media Commission</p>
        <p>Generated on: {{ $exportDate->format('d F Y H:i') }}</p>
    </div>
    
    @if($filters->count() > 0)
    <div class="filters">
        <strong>Applied Filters:</strong>
        @foreach($filters as $key => $value)
            @if($value)
                {{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }} @if(!$loop->last), @endif
            @endif
        @endforeach
    </div>
    @endif
    
    <table>
        <thead>
            <tr>
                <th>Certificate No</th>
                <th>Applicant Name</th>
                <th>Email</th>
                <th>Organization</th>
                <th>Category</th>
                <th>Valid From</th>
                <th>Valid To</th>
                <th>Year</th>
                <th>ID Number</th>
                <th>Marital Status</th>
                <th>Sex</th>
                <th>Date of Birth</th>
                <th>Birth Place</th>
                <th>Nationality</th>
                <th>Home Address</th>
                <th>Town</th>
                <th>Phone</th>
                <th>Cell</th>
                <th>Medium</th>
                <th>Designation</th>
                <th>Status</th>
                <th>Collection Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($journalists as $journalist)
                @php
                    $app = $journalist->application;
                    $formData = $app ? $app->form_data : [];
                    $holder = $journalist->holder;
                @endphp
                <tr>
                    <td>{{ $journalist->certificate_no ?? '' }}</td>
                    <td>{{ $holder?->name ?? ($formData['first_name'] ?? '' . ' ' . $formData['surname'] ?? '') }}</td>
                    <td>{{ $holder?->email ?? '' }}</td>
                    <td>{{ $formData['organization'] ?? $formData['employer'] ?? '' }}</td>
                    <td>{{ $app?->categoryLabel() ?? '' }}</td>
                    <td>{{ optional($journalist->issued_at)->format('Y-m-d') ?? '' }}</td>
                    <td>{{ optional($journalist->expires_at)->format('Y-m-d') ?? '' }}</td>
                    <td>{{ $journalist->year ?? optional($journalist->issued_at)->format('Y') ?? '' }}</td>
                    <td>{{ $formData['id_number'] ?? $formData['national_id'] ?? '' }}</td>
                    <td>{{ $formData['marital_status'] ?? '' }}</td>
                    <td>{{ $formData['sex'] ?? $formData['gender'] ?? '' }}</td>
                    <td>{{ $formData['date_of_birth'] ?? '' }}</td>
                    <td>{{ $formData['place_of_birth'] ?? '' }}</td>
                    <td>{{ $formData['nationality'] ?? '' }}</td>
                    <td>{{ $formData['home_address'] ?? $formData['address'] ?? '' }}</td>
                    <td>{{ $formData['town'] ?? $formData['city'] ?? '' }}</td>
                    <td>{{ $holder?->phone ?? $formData['phone_number'] ?? '' }}</td>
                    <td>{{ $holder?->phone ?? $formData['cell_number'] ?? '' }}</td>
                    <td>{{ $formData['medium'] ?? '' }}</td>
                    <td>{{ $formData['designation'] ?? $formData['job_title'] ?? '' }}</td>
                    <td class="text-center">
                        <span class="status-{{ $journalist->status ?? 'active' }}">
                            {{ ucfirst($journalist->status ?? 'active') }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($journalist->collected_at)
                            <span class="badge badge-success">Collected</span>
                        @else
                            <span class="badge badge-warning">Uncollected</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>Total Records: {{ $journalists->count() }} | Page <span class="page"></span> of <span class="topage"></span></p>
        <p>© {{ date('Y') }} Zimbabwe Media Commission - Confidential Document</p>
    </div>
</body>
</html>

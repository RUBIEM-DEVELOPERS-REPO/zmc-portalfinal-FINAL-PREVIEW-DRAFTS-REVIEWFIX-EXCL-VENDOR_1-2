<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Registered Media Houses Export</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #1e293b; margin-bottom: 5px; }
        .header p { color: #64748b; margin: 0; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th { background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 8px; text-align: left; font-weight: bold; font-size: 9px; }
        td { border: 1px solid #dee2e6; padding: 6px; text-align: left; font-size: 9px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge { padding: 2px 4px; border-radius: 3px; font-size: 8px; }
        .badge-success { background-color: #d4edda; color: #155724; }
        .badge-warning { background-color: #fff3cd; color: #856404; }
        .badge-danger { background-color: #f8d7da; color: #721c24; }
        .small { font-size: 8px; }
        .mt-10 { margin-top: 10px; }
        .section-title { color: #1e293b; font-weight: bold; margin-top: 20px; margin-bottom: 10px; border-bottom: 2px solid #1e293b; padding-bottom: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>ZIMBABWE MEDIA COUNCIL</h1>
        <p>Registered Media Houses - Comprehensive Export</p>
        <p>Generated on: {{ now()->format('d F Y H:i') }}</p>
    </div>

    @php
        $count = 1;
    @endphp

    @foreach($mediahouses as $mediahouse)
        @php
            $app = $mediahouse->application;
            $formData = $app ? $app->form_data : [];
            $contact = $mediahouse->contact;
            $directors = $formData['directors'] ?? [];
            $services = $formData['services'] ?? [];
        @endphp

        @if($count == 1)
            <table class="main-table">
        @else
            <div style="page-break-before: always;"></div>
            <table class="main-table">
        @endif

        <thead>
            <tr>
                <th colspan="17" class="section-title">
                    {{ $count++ }}. {{ $formData['entity_name'] ?? $formData['company_name'] ?? 'Unknown Organization' }}
                </th>
            </tr>
            <tr>
                <th rowspan="2" width="120px;">Registration No</th>
                <th rowspan="2" width="200px;">Organization / Media Company</th>
                <th colspan="2">Directors</th>
                <th rowspan="2">Shareholding Structure</th>
                <th rowspan="2">Office Address</th>
                <th colspan="2">Contact Information</th>
                <th rowspan="2">Website</th>
                <th rowspan="2">Category</th>
                <th rowspan="2">Registration Status</th>
                <th colspan="2">Registration Dates</th>
                <th rowspan="2">License Status</th>
            </tr>
            <tr>
                <th>Sex</th>
                <th>Telephone(s)</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Registration Date</th>
                <th>Registration Year</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="fw-bold text-primary">{{ $mediahouse->registration_no ?? '—' }}</td>
                <td>
                    <div>{{ $formData['entity_name'] ?? $formData['company_name'] ?? '—' }}</div>
                    @if($contact && $contact->email)
                        <div class="small">{{ $contact->email }}</div>
                    @endif
                </td>
                <td>
                    @foreach($directors as $index => $director)
                        <div>{{ $director['name'] ?? '' }}</div>
                        @if($director['sex'])
                            <div class="small"><span class="badge badge-success">{{ ucfirst($director['sex']) }}</span></div>
                        @endif
                        @if($director['telephone'])
                            <div class="small">Tel: {{ $director['telephone'] }}</div>
                        @endif
                    @endforeach
                </td>
                <td>
                    @foreach($directors as $director)
                        @if($director['telephone'])
                            <div class="small">{{ $director['telephone'] }}</div>
                        @endif
                    @endforeach
                </td>
                <td>
                    <div>{{ $formData['shareholding_structure'] ?? '—' }}</div>
                    @if($formData['local_ownership_percentage'])
                        <div class="small"><span class="badge badge-success">{{ $formData['local_ownership_percentage'] }}% Local</span></div>
                    @endif
                </td>
                <td>{{ $formData['office_address'] ?? '—' }}</td>
                <td>{{ $contact?->phone ?? '—' }}</td>
                <td>{{ $contact?->email ?? '—' }}</td>
                <td>
                    @if($formData['website'])
                        <div class="small">{{ $formData['website'] }}</div>
                    @endif
                </td>
                <td>
                    <span class="badge badge-warning">{{ $formData['media_category'] ?? '—' }}</span>
                </td>
                <td>
                    @if($mediahouse->status === 'active')
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-warning">{{ ucfirst($mediahouse->status ?? '—') }}</span>
                    @endif
                </td>
                <td>{{ optional($mediahouse->issued_at)->format('d M Y') ?? '—' }}</td>
                <td>{{ optional($mediahouse->issued_at)->format('Y') ?? '—' }}</td>
                <td>
                    @if($mediahouse->license_status === 'valid')
                        <span class="badge badge-success">Valid</span>
                    @else
                        <span class="badge badge-danger">{{ ucfirst($mediahouse->license_status ?? '—') }}</span>
                    @endif
                </td>
            </tr>

            @if($includeDetails && !empty($services))
                <tr>
                    <td colspan="17" class="section-title">Services / Publications Details</td>
                </tr>
                @foreach($services as $service)
                    <tr>
                        <td colspan="2"><strong>{{ $service['name'] ?? '' }}</strong></td>
                        <td>{{ $service['type'] ?? '' }}</td>
                        <td>{{ $service['print_online'] ?? '' }}</td>
                        <td>{{ $service['frequency'] ?? '' }}</td>
                        <td>{{ $service['language'] ?? '' }}</td>
                        <td>{{ $service['focus'] ?? '' }}</td>
                        <td>{{ $service['scope'] ?? '' }}</td>
                        <td>{{ $service['reach'] ?? '' }}</td>
                        <td>{{ $service['provincial_reach'] ?? '' }}</td>
                        <td colspan="2">
                            @if(isset($service['contact']))
                                <div>{{ $service['contact']['name'] ?? '' }}</div>
                                <div class="small">{{ $service['contact']['email'] ?? '' }}</div>
                                <div class="small">{{ $service['contact']['phone'] ?? '' }}</div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endif

            <tr>
                <td colspan="8"><strong>Operational Status:</strong></td>
                <td colspan="2">{{ $formData['operational_status'] ?? 'Active' }}</td>
                <td><strong>Previous Renewal Year:</strong></td>
                <td>{{ $formData['previous_renewal_year'] ?? '—' }}</td>
                <td><strong>License Expiry Date:</strong></td>
                <td>{{ optional($mediahouse->expires_at)->format('d M Y') ?? '—' }}</td>
                <td><strong>License Expiry Year:</strong></td>
                <td>{{ optional($mediahouse->expires_at)->format('Y') ?? '—' }}</td>
            </tr>
        </tbody>
        </table>

        @if($loop->last)
            </div>
        @endif
    @endforeach
</body>
</html>

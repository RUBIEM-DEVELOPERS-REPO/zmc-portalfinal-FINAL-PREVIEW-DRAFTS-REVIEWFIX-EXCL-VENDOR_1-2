<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PortalApplicationDetailsController extends Controller
{
    public function show(Request $request, Application $application)
    {
        $user = Auth::user();
        if (!$user) abort(403);

        // Applicants only access their own applications; staff may access anything.
        if (!$user->hasAnyRole(['accreditation_officer','accounts_payments','registrar','production','super_admin'])) {
            if ((int)$application->applicant_user_id !== (int)$user->id) {
                abort(403);
            }
        }

        $application->loadMissing(['applicant','documents']);

        $fd = $application->form_data;
        if (is_string($fd)) {
            $decoded = json_decode($fd, true);
            $fd = is_array($decoded) ? $decoded : [];
        }
        if (!is_array($fd)) $fd = [];

        $app = $application->toArray();
        $app['applicant_name'] = $application->applicant?->name;
        $app['form_code'] = match ($application->application_type) {
            'accreditation' => 'AP3',
            'registration'  => 'AP1',
            default         => strtoupper((string)$application->application_type),
        };

        // Standardized labels for the frontend
        $labels = [
            'title' => 'Title',
            'surname' => 'Surname',
            'first_name' => 'First Name',
            'other_names' => 'Other Names',
            'dob' => 'Date of Birth',
            'gender' => 'Sex',
            'sex' => 'Sex',
            'birth_place' => 'Place & Country of Birth',
            'origin' => 'Country of Origin',
            'nationality' => 'Nationality',
            'id_passport_number' => 'ID/Passport Number',
            'passport_no' => 'Passport Number',
            'employer_name' => 'Employer/Media House',
            'medium_type' => 'Medium Type',
            'designation' => 'Designation',
            'assignment_brief' => 'Assignment Brief',
            'arrival_date' => 'Arrival Date',
            'departure_date' => 'Departure Date',
            'port_entry' => 'Port of Entry',
            'zim_local_address' => 'Local Address',
            'zim_address' => 'Zimbabwe Address',
            'phone' => 'Phone',
            'email' => 'Email',
            'org_name' => 'Organization Name',
            'reg_no' => 'Registration Number',
            'website' => 'Website',
            'head_office' => 'Head Office Address',
            'postal_address' => 'Postal Address',
            'contact_person' => 'Contact Person',
            'contact_email' => 'Contact Email',
            'contact_phone' => 'Contact Phone',
            'category' => 'Category',
            'operating_model' => 'Operating Model',
            'arrived_on' => 'Arrived On',
            'arrival_mode' => 'Arrival Mode',
            'departing_on' => 'Departing On',
            'special_assignment' => 'Special Assignment',
            'national_reg_no' => 'National ID',
            'marital_status' => 'Marital Status',
            'address' => 'Residential Address',
            'employment_type' => 'Employment Type',
            'media_org' => 'Media Organization',
        ];

        // Ensure common fields are at top level for legacy JS if needed, but we'll use fd mostly
        foreach ($labels as $k => $v) {
            if (array_key_exists($k, $fd) && !array_key_exists($k, $app)) {
                $app[$k] = $fd[$k];
            }
        }

        $directors = $fd['directors'] ?? $fd['directors_rows'] ?? [];
        $managers = $fd['managers'] ?? $fd['managers_rows'] ?? [];

        $documents = $application->documents->map(function ($d) {
            return [
                'document_type' => $d->doc_type ?? $d->document_type ?? 'document',
                'original_name' => $d->original_name ?? null,
                'file_name'     => $d->file_path ?? null,
                'url'           => !empty($d->file_path) ? Storage::disk('public')->url($d->file_path) : null,
                'status'        => $d->status ?? null,
            ];
        })->values();

        return response()->json([
            'ok' => true,
            'application' => $app,
            'form_data' => $fd,
            'labels' => $labels,
            'directors' => $directors,
            'managers' => $managers,
            'documents' => $documents,
        ]);
    }
}

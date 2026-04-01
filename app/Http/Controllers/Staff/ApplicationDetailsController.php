<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApplicationDetailsController extends Controller
{
    /**
     * JSON details for dashboard “View” modal.
     */
    public function show(Request $request, Application $application)
    {
        $application->loadMissing(['applicant','documents']);

        $fd = $application->form_data;
        if (is_string($fd)) {
            $decoded = json_decode($fd, true);
            $fd = is_array($decoded) ? $decoded : [];
        }
        if (!is_array($fd)) $fd = [];

        // Base application payload
        $app = $application->toArray();
        $app['applicant_name'] = $application->applicant?->name;
        $app['form_code'] = $this->formCode($application);

        // Flatten common AP3 fields for convenience (if present)
        foreach ([
            'title','first_name','surname','other_names','dob','sex','birth_place','origin','nationality',
            'id_passport_number','employer_name','medium_type','designation','assignment_brief','arrival_date',
            'departure_date','port_entry','zim_local_address','zim_address'
        ] as $k) {
            if (array_key_exists($k, $fd) && !array_key_exists($k, $app)) {
                $app[$k] = $fd[$k];
            }
        }

        // AP1 convenience blocks (if stored under ap1 / directors / managers)
        $ap1 = [];
        if (isset($fd['ap1']) && is_array($fd['ap1'])) {
            $ap1 = $fd['ap1'];
        } else {
            // Some builds store AP1 keys at top-level
            $possible = ['category','service_name','operating_model','org_name','reg_no','website','head_office','postal_address',
                'contact_person','contact_email','contact_phone'];
            $hasAny = false;
            foreach ($possible as $p) {
                if (array_key_exists($p, $fd)) { $hasAny = true; break; }
            }
            if ($hasAny) {
                foreach ($possible as $p) {
                    if (array_key_exists($p, $fd)) $ap1[$p] = $fd[$p];
                }
            }
        }

        $directors = [];
        if (isset($fd['directors']) && is_array($fd['directors'])) $directors = $fd['directors'];
        if (isset($fd['directors_rows']) && is_array($fd['directors_rows'])) $directors = $fd['directors_rows'];

        $managers = [];
        if (isset($fd['managers']) && is_array($fd['managers'])) $managers = $fd['managers'];
        if (isset($fd['managers_rows']) && is_array($fd['managers_rows'])) $managers = $fd['managers_rows'];

        $documents = $application->documents->map(function ($d) {
            return [
                'document_type' => $d->doc_type ?? $d->document_type ?? 'document',
                'original_name' => $d->original_name ?? null,
                'file_name'     => $d->file_path ?? null,
                'url'           => !empty($d->file_path) ? $d->url : null,
                'status'        => $d->status ?? null,
            ];
        })->values();

        // Previous applications
        $previousApplications = Application::where('applicant_id', $application->applicant_id)
            ->where('id', '!=', $application->id)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'reference', 'application_type', 'status', 'created_at'])
            ->map(function($pa) {
                return [
                    'id' => $pa->id,
                    'reference' => $pa->reference,
                    'type' => $pa->application_type,
                    'status' => $pa->status,
                    'date' => $pa->created_at?->format('d M Y'),
                ];
            });

        // Previous payments
        $previousPayments = Payment::where('payer_user_id', $application->user_id)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'reference', 'amount', 'currency', 'method', 'status', 'created_at'])
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'reference' => $p->reference,
                    'amount' => $p->amount,
                    'currency' => $p->currency,
                    'method' => $p->method,
                    'status' => $p->status,
                    'date' => $p->created_at?->format('d M Y'),
                ];
            });

        return response()->json([
            'ok' => true,
            'application' => $app,
            'ap1' => $ap1,
            'directors' => $directors,
            'managers' => $managers,
            'documents' => $documents,
            'previous_applications' => $previousApplications,
            'previous_payments' => $previousPayments,
        ]);
    }

    /**
     * Global search for staff: redirects to the specific application if found by ref, 
     * otherwise falls back to the officer's filtered list.
     */
    public function globalSearch(Request $request)
    {
        $q = $request->query('q');
        if (!$q) return back();

        // 1. Check for exact reference
        $app = Application::where('reference', trim($q))->first();
        if ($app) {
            $role = session('active_staff_role');
            // Redirect based on role if possible
            $route = match($role) {
                'accreditation_officer' => 'staff.officer.applications.show',
                'registrar' => 'staff.registrar.applications.show',
                'accounts_payments' => 'staff.accounts.applications.show',
                'production' => 'staff.production.applications.show',
                default => 'staff.officer.dashboard'
            };
            
            // If the route doesn't exist for some reason, fallback to officer dashboard
            try {
                return redirect()->route($route, $app->id);
            } catch (\Exception $e) {
                return redirect()->route('staff.officer.dashboard');
            }
        }

        // 2. Fallback to officer application list (which has a robust search)
        return redirect()->route('staff.officer.applications.index', ['q' => $q]);
    }

    private function formCode(Application $application): string
    {
        return match ($application->application_type) {
            'accreditation' => 'AP3',
            'registration' => 'AP1',
            default => strtoupper((string)$application->application_type),
        };
    }
}

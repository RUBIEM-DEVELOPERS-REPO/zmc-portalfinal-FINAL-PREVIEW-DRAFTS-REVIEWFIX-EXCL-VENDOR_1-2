<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Services\ApplicationWorkflow;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\AccreditationRecord;
use App\Models\RegistrationRecord;

class ProductionController extends Controller
{
    /**
     * Build a production query with region scoping (collection_region).
     */
    private function baseQuery(array $statuses)
    {
        $user = Auth::user();
        $q = Application::query()
            ->with(['applicant'])
            ->withCount('documents')
            ->whereIn('status', $statuses)
            ->latest();

        // Concurrency visibility logic
        $q->where(function($qq) use ($user) {
            $qq->whereNull('assigned_officer_id')
              ->orWhere('assigned_officer_id', $user->id);
        });

        $q->where(function($qq) use ($user) {
            $qq->whereNull('locked_at')
              ->orWhere('locked_at', '<=', now()->subHours(2))
              ->orWhere('locked_by', $user->id);
        });

        $userRegion = $user?->region;

        // Only filter if staff user actually has region set
        if (!empty($userRegion)) {
            $q->where(function ($qq) use ($userRegion) {
                $qq->whereNull('collection_region')
                   ->orWhere('collection_region', $userRegion);
            });
        }

        return $q;
    }

    public function dashboard(Request $request)
    {
        $applications = $this->baseQuery([
            Application::PRODUCTION_QUEUE,
            Application::CARD_GENERATED,
            Application::CERT_GENERATED,
            Application::PRINTED,
            Application::ISSUED,
        ])->paginate(20)->withQueryString();

        // KPI counters (region scoped)
        $kpiQueue       = (clone $this->baseQuery([Application::PRODUCTION_QUEUE]))->count();
        $kpiToPrint     = (clone $this->baseQuery([Application::CARD_GENERATED, Application::CERT_GENERATED]))->count();
        $kpiPrinted     = (clone $this->baseQuery([Application::PRINTED]))->count();
        $kpiIssued      = (clone $this->baseQuery([Application::ISSUED]))->count();

        // "Today" counts (created_at as proxy for production events)
        $today = now()->startOfDay();
        $kpiGeneratedToday = (clone $this->baseQuery([Application::CARD_GENERATED, Application::CERT_GENERATED]))
            ->where('updated_at', '>=', $today)->count();
        $kpiIssuedToday = (clone $this->baseQuery([Application::ISSUED]))
            ->where('updated_at', '>=', $today)->count();

        return view('staff.production.dashboard', compact(
            'applications',
            'kpiQueue',
            'kpiToPrint',
            'kpiPrinted',
            'kpiIssued',
            'kpiGeneratedToday',
            'kpiIssuedToday'
        ));
    }

    /**
     * Production Queue (Registrar approved → Production).
     */
    public function queue()
    {
        $applications = $this->baseQuery([Application::PRODUCTION_QUEUE])->paginate(20);
        return view('staff.production.list', [
            'pageTitle' => 'Production Queue',
            'pageNote'  => 'Items approved by the Registrar and awaiting production actions.',
            'applications' => $applications,
            'mode' => 'queue',
        ]);
    }

    /**
     * Card production workspace (Accreditation only).
     */
    public function cards()
    {
        $applications = $this->baseQuery([
                Application::PRODUCTION_QUEUE,
                Application::CARD_GENERATED,
                Application::PRINTED,
                Application::ISSUED,
            ])
            ->where('application_type', 'accreditation')
            ->paginate(20);

        return view('staff.production.list', [
            'pageTitle' => 'Card Production',
            'pageNote'  => 'Generate / print accreditation cards (AP3/AP5).',
            'applications' => $applications,
            'mode' => 'cards',
        ]);
    }

    /**
     * Certificate production workspace (Registration only).
     */
    public function certificates()
    {
        $applications = $this->baseQuery([
                Application::PRODUCTION_QUEUE,
                Application::CERT_GENERATED,
                Application::PRINTED,
                Application::ISSUED,
            ])
            ->where('application_type', 'registration')
            ->paginate(20);

        return view('staff.production.list', [
            'pageTitle' => 'Certificate Production',
            'pageNote'  => 'Generate / print registration certificates (AP1).',
            'applications' => $applications,
            'mode' => 'certificates',
        ]);
    }

    /**
     * Printing queue (generated but not yet printed).
     */
    public function printing()
    {
        $applications = $this->baseQuery([
                Application::CARD_GENERATED,
                Application::CERT_GENERATED,
            ])
            ->paginate(20);

        return view('staff.production.list', [
            'pageTitle' => 'Printing Queue',
            'pageNote'  => 'Items generated and ready to be marked as printed (single or batch).',
            'applications' => $applications,
            'mode' => 'printing',
        ]);
    }

    /**
     * Issuance queue (printed but not yet issued).
     */
    public function issuance()
    {
        $applications = $this->baseQuery([Application::PRINTED])->paginate(20);

        return view('staff.production.list', [
            'pageTitle' => 'Issuance & Collection',
            'pageNote'  => 'Printed items ready for collection/dispatch. Mark items as issued upon handover.',
            'applications' => $applications,
            'mode' => 'issuance',
        ]);
    }

    /**
     * Issued register (logbook).
     */
    public function issuedRegister()
    {
        $applications = $this->baseQuery([Application::ISSUED])->paginate(20);

        return view('staff.production.list', [
            'pageTitle' => 'Issued Register',
            'pageNote'  => 'Official register of issued cards/certificates (region scoped).',
            'applications' => $applications,
            'mode' => 'register',
        ]);
    }

    /**
     * Reports (placeholder page).
     */
    public function reports()
    {
        $kpis = [
            'in_queue'   => (clone $this->baseQuery([Application::PRODUCTION_QUEUE]))->count(),
            'to_print'   => (clone $this->baseQuery([Application::CARD_GENERATED, Application::CERT_GENERATED]))->count(),
            'printed'    => (clone $this->baseQuery([Application::PRINTED]))->count(),
            'issued'     => (clone $this->baseQuery([Application::ISSUED]))->count(),
        ];

        return view('staff.production.reports', compact('kpis'));
    }

    public function show(Application $application)
    {
        // Try to claim
        if (!$application->claim(auth()->user())) {
            $lockerName = $application->lockedBy ? $application->lockedBy->name : 'another officer';
            return redirect()->back()->with('error', "This application is currently being worked on by {$lockerName}.");
        }

        $application->load(['applicant', 'documents', 'messages', 'workflowLogs','lockedBy']);
        return view('staff.production.show', compact('application'));
    }

    public function unlock(Application $application)
    {
        if ($application->locked_by === auth()->id()) {
            $application->unlock();
            return redirect()->route('staff.production.dashboard')->with('success', 'Application released.');
        }
        return back();
    }

    public function generateCard(Request $request, Application $application)
    {
        $from = $application->status;

        $this->ensureRecordCreated($application);

        $application->update([
            'status' => Application::CARD_GENERATED,
            'printed_by' => auth()->id(),
            'printed_at' => now(),
        ]);
        $application->refresh();
        $this->audit('production_generate_card', $application, $from, $application->status);

        return back()->with('success', 'Card generated and record created.');
    }

    public function cardPreview(Application $application)
    {
        $application->load(['applicant', 'documents']);

        // Pull any previous edits
        $edits = (array)($application->form_data['card_edits'] ?? []);

        return view('staff.production.card_preview', compact('application', 'edits'));
    }

    public function cardPrint(Request $request, Application $application)
    {
        $data = $request->validate([
            'card_payload' => ['required', 'array'],
            'template' => ['nullable', 'string'],
        ]);

        // Persist edits into form_data
        $form = (array)($application->form_data ?? []);
        $form['card_edits'] = $data['card_payload'];
        $form['card_template'] = $data['template'] ?? 'default';
        $application->form_data = $form;
        $application->save();

        // Mark generated (workflow state) when user prints
        $from = $application->status;
        if ($application->status !== Application::CARD_GENERATED) {
            ApplicationWorkflow::transition($application, Application::CARD_GENERATED, 'production_generate_card');
            $application->refresh();
            $this->audit('production_generate_card', $application, $from, $application->status);
        }

        $application->load(['applicant', 'documents', 'accreditationRecord']);
        $payload = $data['card_payload'];
        $payload['qr_value'] = route('public.verify', $application->accreditationRecord->qr_token ?? 'invalid');
        $payload['reg_no'] = $application->accreditationRecord->certificate_no ?? ($payload['ref'] ?? '—');

        // Add passport photo URL to payload
        $passportPhoto = $application->documents->where('doc_type', 'passport_photo')->first();
        if ($passportPhoto) {
            $payload['photo_url'] = $passportPhoto->url;
        }

        $template = $data['template'] ?? 'default';
        $template_data = $this->getCardTemplateData($template);

        // Printable HTML (browser print)
        return view('staff.production.card_print', compact('application', 'payload', 'template', 'template_data'));
    }


    public function cardPrintBack(Request $request, Application $application)
    {
        $data = $request->validate([
            'card_payload' => ['required', 'array'],
            'template' => ['nullable', 'string'],
        ]);

        // Persist edits into form_data (same payload)
        $form = (array)($application->form_data ?? []);
        $form['card_edits'] = $data['card_payload'];
        $form['card_template'] = $data['template'] ?? 'default';
        $application->form_data = $form;
        $application->save();

        // Mark generated (workflow state) when user prints
        $from = $application->status;
        if ($application->status !== Application::CARD_GENERATED) {
            ApplicationWorkflow::transition($application, Application::CARD_GENERATED, 'production_generate_card');
            $application->refresh();
            $this->audit('production_generate_card', $application, $from, $application->status);
        }

        $application->load(['applicant', 'accreditationRecord']);
        $payload = $data['card_payload'];
        $payload['qr_value'] = route('public.verify', $application->accreditationRecord->qr_token ?? 'invalid');
        $payload['reg_no'] = $application->accreditationRecord->certificate_no ?? ($payload['ref'] ?? '—');

        $template = $data['template'] ?? 'default';
        $template_data = $this->getCardTemplateData($template);

        return view('staff.production.card_print_back', compact('application', 'payload', 'template', 'template_data'));
    }

    private function getCardTemplateData(string $template): array
    {
        $variations = [
            'default' => [
                'primary_color' => '#1a237e',
                'secondary_color' => '#2e7d32',
                'bg_style' => 'gradient',
                'layout' => 'standard'
            ],
            'modern_dark' => [
                'primary_color' => '#212121',
                'secondary_color' => '#fbbf24',
                'bg_style' => 'solid',
                'layout' => 'standard'
            ],
            'eco_green' => [
                'primary_color' => '#1b5e20',
                'secondary_color' => '#81c784',
                'bg_style' => 'gradient',
                'layout' => 'standard'
            ],
            'royal_gold' => [
                'primary_color' => '#4a148c',
                'secondary_color' => '#ffd54f',
                'bg_style' => 'pattern',
                'layout' => 'compact'
            ],
            'ocean_blue' => [
                'primary_color' => '#01579b',
                'secondary_color' => '#4fc3f7',
                'bg_style' => 'gradient',
                'layout' => 'standard'
            ],
            'crimson_pro' => [
                'primary_color' => '#b71c1c',
                'secondary_color' => '#ef9a9a',
                'bg_style' => 'solid',
                'layout' => 'standard'
            ],
        ];

        return $variations[$template] ?? $variations['default'];
    }

    public function generateCertificate(Request $request, Application $application)
    {
        $from = $application->status;

        $this->ensureRecordCreated($application);

        // ✅ Use constant to avoid mismatched status string
        ApplicationWorkflow::transition($application, Application::CERT_GENERATED, 'production_generate_certificate');

        $application->refresh();
        $this->audit('production_generate_certificate', $application, $from, $application->status);

        return back()->with('success', 'Certificate generated and record created.');
    }

    public function certificatePreview(Application $application)
    {
        // Also load registrationRecord so preview QR uses the real verification link
        $application->load(['applicant', 'registrationRecord']);

        $edits = (array)($application->form_data['certificate_edits'] ?? []);

        return view('staff.production.certificate_preview', compact('application', 'edits'));
    }

    public function certificatePrint(Request $request, Application $application)
    {
        $data = $request->validate([
            'certificate_payload' => ['required', 'array'],
            'template' => ['nullable', 'string'],
        ]);

        $form = (array)($application->form_data ?? []);
        $form['certificate_edits'] = $data['certificate_payload'];
        $application->form_data = $form;
        $application->save();

        // Mark generated (workflow state) when user prints
        $from = $application->status;
        if ($application->status !== Application::CERT_GENERATED) {
            $this->ensureRecordCreated($application);
            ApplicationWorkflow::transition($application, Application::CERT_GENERATED, 'production_generate_certificate');
            $application->refresh();
            $this->audit('production_generate_certificate', $application, $from, $application->status);
        }

        $application->load(['applicant', 'registrationRecord']);
        $payload = $data['certificate_payload'];
        $payload['qr_value'] = route('public.verify', $application->registrationRecord->qr_token ?? 'invalid');
        $payload['reg_no'] = $payload['reg_no'] ?? ($application->registrationRecord->registration_no ?? '—');

        $template = $data['template'] ?? 'modern';
        $view = 'staff.production.certificate_print';
        $template_data = [];
        $orientation = 'portrait';

        if ($template === 'classic') {
            $view = 'staff.production.certificate_classic';
        } elseif (str_starts_with($template, 'var_')) {
            $view = 'staff.production.certificate_variations';
            $template_data = $this->getTemplateVariationData($template);
            $orientation = $template_data['orientation'] ?? 'portrait';
        }

        return view($view, compact('application', 'payload', 'template_data', 'orientation'));
    }

    private function getTemplateVariationData(string $template): array
    {
        $variations = [
            'var_1' => ['orientation' => 'portrait', 'primary_color' => '#1a237e', 'secondary_color' => '#d4a574', 'bg_type' => 'solid', 'border_style' => 'solid'],
            'var_2' => ['orientation' => 'portrait', 'primary_color' => '#1b5e20', 'secondary_color' => '#a5d6a7', 'bg_type' => 'solid', 'border_style' => 'double'],
            'var_3' => ['orientation' => 'portrait', 'primary_color' => '#b71c1c', 'secondary_color' => '#ef9a9a', 'bg_type' => 'gradient', 'border_style' => 'solid'],
            'var_4' => ['orientation' => 'portrait', 'primary_color' => '#4a148c', 'secondary_color' => '#ce93d8', 'bg_type' => 'pattern', 'border_style' => 'ornate'],
            'var_5' => ['orientation' => 'landscape', 'primary_color' => '#1a237e', 'secondary_color' => '#d4a574', 'bg_type' => 'solid', 'border_style' => 'solid'],
            'var_6' => ['orientation' => 'landscape', 'primary_color' => '#311b92', 'secondary_color' => '#b39ddb', 'bg_type' => 'gradient', 'border_style' => 'double'],
            'var_7' => ['orientation' => 'landscape', 'primary_color' => '#004d40', 'secondary_color' => '#80cbc4', 'bg_type' => 'pattern', 'border_style' => 'ornate'],
            'var_8' => ['orientation' => 'portrait', 'primary_color' => '#263238', 'secondary_color' => '#cfd8dc', 'bg_type' => 'solid', 'border_style' => 'solid', 'font_family' => 'Arial, sans-serif'],
            'var_9' => ['orientation' => 'portrait', 'primary_color' => '#bf360c', 'secondary_color' => '#ffab91', 'bg_type' => 'solid', 'border_style' => 'double', 'title_font' => 'Georgia, serif', 'title_style' => 'italic'],
            'var_10' => ['orientation' => 'landscape', 'primary_color' => '#01579b', 'secondary_color' => '#81d4fa', 'bg_type' => 'gradient', 'border_style' => 'solid'],
            'var_11' => ['orientation' => 'portrait', 'primary_color' => '#33691e', 'secondary_color' => '#dcedc8', 'bg_type' => 'solid', 'border_style' => 'ornate'],
            'var_12' => ['orientation' => 'landscape', 'primary_color' => '#e65100', 'secondary_color' => '#ffcc80', 'bg_type' => 'solid', 'border_style' => 'double'],
            'var_13' => ['orientation' => 'portrait', 'primary_color' => '#000000', 'secondary_color' => '#999999', 'bg_type' => 'pattern', 'border_style' => 'solid'],
            'var_14' => ['orientation' => 'landscape', 'primary_color' => '#1a237e', 'secondary_color' => '#ffd54f', 'bg_type' => 'gradient', 'border_style' => 'ornate'],
            'var_15' => ['orientation' => 'portrait', 'primary_color' => '#880e4f', 'secondary_color' => '#f48fb1', 'bg_type' => 'solid', 'border_style' => 'double'],
            'var_16' => ['orientation' => 'landscape', 'primary_color' => '#3e2723', 'secondary_color' => '#bcaaa4', 'bg_type' => 'solid', 'border_style' => 'solid'],
            'var_17' => ['orientation' => 'portrait', 'primary_color' => '#212121', 'secondary_color' => '#d4a574', 'bg_type' => 'gradient', 'border_style' => 'ornate'],
            'var_18' => ['orientation' => 'landscape', 'primary_color' => '#0d47a1', 'secondary_color' => '#bbdefb', 'bg_type' => 'pattern', 'border_style' => 'double'],
            'var_19' => ['orientation' => 'portrait', 'primary_color' => '#ff6f00', 'secondary_color' => '#ffe082', 'bg_type' => 'solid', 'border_style' => 'solid'],
            'var_20' => ['orientation' => 'landscape', 'primary_color' => '#1b5e20', 'secondary_color' => '#c8e6c9', 'bg_type' => 'gradient', 'border_style' => 'ornate'],
            'var_21' => ['orientation' => 'portrait', 'primary_color' => '#006064', 'secondary_color' => '#b2ebf2', 'bg_type' => 'pattern', 'border_style' => 'double'],
            'var_22' => ['orientation' => 'landscape', 'primary_color' => '#4a148c', 'secondary_color' => '#e1bee7', 'bg_type' => 'solid', 'border_style' => 'solid'],
            'var_23' => ['orientation' => 'portrait', 'primary_color' => '#b71c1c', 'secondary_color' => '#ffccbc', 'bg_type' => 'gradient', 'border_style' => 'double'],
            'var_24' => ['orientation' => 'landscape', 'primary_color' => '#263238', 'secondary_color' => '#eceff1', 'bg_type' => 'solid', 'border_style' => 'ornate'],
            'var_25' => ['orientation' => 'portrait', 'primary_color' => '#1a237e', 'secondary_color' => '#c5cae9', 'bg_type' => 'solid', 'border_style' => 'double'],
        ];

        return $variations[$template] ?? $variations['var_1'];
    }

    public function printSingle(Request $request, Application $application)
    {
        $from = $application->status;

        $application->update([
            'status' => Application::PRINTED,
            'printed_by' => auth()->id(),
            'printed_at' => now(),
        ]);
        $application->refresh();
        $this->audit('production_print_single', $application, $from, $application->status);

        return back()->with('success', 'Marked as printed.');
    }

    public function printBatch(Request $request)
    {
        $data = $request->validate([
            'application_ids' => ['required', 'array', 'min:1'],
            'application_ids.*' => ['integer'],
        ]);

        $apps = Application::whereIn('id', $data['application_ids'])->get();

        $processed = 0;

        foreach ($apps as $app) {
            if (in_array($app->status, [
                Application::PRODUCTION_QUEUE,
                Application::CARD_GENERATED,
                Application::CERT_GENERATED,
            ], true)) {

                $from = $app->status;

                $app->update([
                    'status' => Application::PRINTED,
                    'printed_by' => auth()->id(),
                    'printed_at' => now(),
                ]);
                $app->refresh();
                $this->audit('production_print_batch', $app, $from, $app->status, [
                    'batch' => true,
                    'batch_size' => count($apps),
                ]);

                $processed++;
            }
        }

        return back()->with('success', "Batch print processed. Printed: {$processed} item(s).");
    }

    public function markIssued(Request $request, Application $application)
    {
        $from = $application->status;

        $application->update([
            'status' => Application::ISSUED,
            'issued_by' => auth()->id(),
            'issued_at' => now(),
        ]);
        $application->refresh();
        $this->audit('production_issue', $application, $from, $application->status);

        return back()->with('success', 'Marked as issued.');
    }

    /* helpers */
    private function audit(string $action, Application $application, ?string $from, ?string $to, array $meta = []): void
    {
        $payload = array_merge([
            'actor_role' => session('active_staff_role'),
            'actor_user_id' => Auth::id(),
            'from_status' => $from,
            'to_status' => $to,
        ], $meta);

        ActivityLogger::log($action, $application, $from, $to, $payload);
        \App\Support\AuditTrail::log($action, $application, $payload);
    }

    /**
     * Helper to ensure an AccreditationRecord or RegistrationRecord is created.
     */
    private function ensureRecordCreated(Application $application): void
    {
        $number = $application->generateFormattedNumber();
        $token  = Str::random(12);

        if ($application->application_type === 'registration') {
            if (!$application->registrationRecord) {
                RegistrationRecord::create([
                    'contact_user_id' => $application->applicant_user_id,
                    'application_id'  => $application->id,
                    'entity_name'      => $application->form_data['media_house_name'] ?? ($application->applicant->name ?? 'ZMC Media House'),
                    'registration_no' => $number,
                    'record_number'   => $number,
                    'status'          => 'active',
                    'issued_at'       => now(),
                    'expires_at'      => now()->addYears(3), // Default 3 years for registrations
                    'qr_token'        => $token,
                ]);
            }
        } else {
            if (!$application->accreditationRecord) {
                AccreditationRecord::create([
                    'holder_user_id' => $application->applicant_user_id,
                    'application_id' => $application->id,
                    'certificate_no' => $number,
                    'record_number'  => $number,
                    'status'         => 'active',
                    'issued_at'      => now(),
                    'expires_at'     => now()->addYear(), // Default 1 year for accreditation
                    'qr_token'       => $token,
                ]);
            }
        }
    }
}

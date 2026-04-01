<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\AccreditationRecord;
use App\Models\RegistrationRecord;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RegistrarController extends Controller
{
    /**
     * Display registrar dashboard.
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        
        // Updated KPIs according to new requirements
        $kpis = [
            'total_applications' => Application::count(),
            'awaiting_review' => Application::where('registrar_reviewed', false)
                ->where('status', Application::OFFICER_APPROVED)
                ->count(),
            'returned_for_correction' => Application::where('status', Application::CORRECTION_REQUESTED)
                ->count(),
            'forwarded_to_registrar' => Application::where('status', Application::FORWARDED_TO_REGISTRAR)
                ->count(),
            'new_this_week' => Application::where('created_at', '>=', now()->startOfWeek())->count(),
        ];

        // Production Stats - Cards Generated Today
        $productionStats = [
            'cards_today' => AccreditationRecord::whereDate('created_at', today())->count() + 
                            RegistrationRecord::whereDate('created_at', today())->count(),
            'accreditation_cards' => AccreditationRecord::whereDate('created_at', today())->count(),
            'registration_cards' => RegistrationRecord::whereDate('created_at', today())->count(),
            'pending_cards' => AccreditationRecord::whereNull('printed_at')->count() + 
                             RegistrationRecord::whereNull('printed_at')->count(),
        ];

        // Records Stats
        $recordsStats = [
            'accredited_count' => AccreditationRecord::count(),
            'registered_count' => RegistrationRecord::count(),
        ];

        // Recent Activities - Officer and Accountant activities only
        $recentActivities = Activity::with(['user'])
            ->whereIn('activity_type', [
                'application_approved',
                'application_rejected',
                'payment_confirmed',
                'payment_verified',
                'card_generated',
                'certificate_issued'
            ])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($activity) {
                return [
                    'title' => $this->getActivityTitle($activity),
                    'description' => $this->getActivityDescription($activity),
                    'time' => $activity->created_at->diffForHumans(),
                    'icon' => $this->getActivityIcon($activity),
                    'color' => $this->getActivityColor($activity),
                ];
            });

        return view('staff.registrar.dashboard', compact('kpis', 'productionStats', 'recordsStats', 'recentActivities'));
    }

    /**
     * Display applications list for registrar with new structure.
     */
    public function applications(Request $request)
    {
        $query = Application::with(['applicant', 'assignedOfficer']);

        // Apply status filter according to new requirements
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'awaiting_review':
                    $query->where('registrar_reviewed', false)
                          ->where('status', Application::OFFICER_APPROVED);
                    break;
                case 'forwarded':
                    $query->where('status', Application::FORWARDED_TO_REGISTRAR);
                    break;
                case 'returned':
                    $query->where('status', Application::CORRECTION_REQUESTED);
                    break;
            }
        }

        // Apply advanced filters
        if ($request->filled('application_type')) {
            $query->where('application_type', $request->application_type);
        }

        if ($request->filled('request_type')) {
            $query->where('request_type', $request->request_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('applicant', function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        if ($request->filled('reference')) {
            $query->where('reference', 'like', "%{$request->reference}%");
        }

        if ($request->filled('date_from')) {
            $query->whereDate('submitted_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('submitted_at', '<=', $request->date_to);
        }

        if ($request->filled('payment_status')) {
            if ($request->payment_status === 'paid') {
                $query->where('payment_status', 'paid');
            } else {
                $query->where('payment_status', '!=', 'paid');
            }
        }

        $applications = $query->orderBy('submitted_at', 'desc')->paginate(20);

        return view('staff.registrar.applications', compact('applications'));
    }

    /**
     * Mark application as reviewed by registrar.
     */
    public function markAsReviewed(Request $request, $id)
    {
        $application = Application::findOrFail($id);
        
        $application->update([
            'registrar_reviewed' => true,
            'registrar_reviewed_at' => now(),
            'registrar_reviewed_by' => Auth::id(),
        ]);

        \App\Services\ActivityLogger::log('registrar_reviewed_application', $application, null, null, [
            'registrar_id' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Application marked as reviewed']);
    }

    /**
     * Flag application anomaly.
     */
    public function flagAnomaly(Request $request, $id)
    {
        $request->validate([
            'anomaly_description' => ['required', 'string', 'max:1000'],
        ]);

        $application = Application::findOrFail($id);
        
        $application->update([
            'has_anomaly' => true,
            'anomaly_description' => $request->anomaly_description,
            'anomaly_flagged_by' => Auth::id(),
            'anomaly_flagged_at' => now(),
        ]);

        \App\Services\ActivityLogger::log('application_anomaly_flagged', $application, null, null, [
            'registrar_id' => Auth::id(),
            'anomaly_description' => $request->anomaly_description,
        ]);

        return response()->json(['success' => true, 'message' => 'Anomaly flagged successfully']);
    }

    /**
     * Send guidance to accreditation officer for complex applications.
     */
    public function sendGuidance(Request $request, $id)
    {
        $request->validate([
            'guidance_note' => ['required', 'string', 'max:1000'],
        ]);

        $application = Application::findOrFail($id);
        
        // Create guidance record
        $guidance = \App\Models\GuidanceNote::create([
            'application_id' => $application->id,
            'registrar_id' => Auth::id(),
            'officer_id' => $application->assigned_officer_id,
            'note' => $request->guidance_note,
            'status' => 'sent',
        ]);

        // Update application status
        $application->update([
            'status' => Application::OFFICER_REVIEW,
            'registrar_guidance_sent' => true,
            'registrar_guidance_sent_at' => now(),
        ]);

        \App\Services\ActivityLogger::log('guidance_sent_to_officer', $application, null, null, [
            'registrar_id' => Auth::id(),
            'officer_id' => $application->assigned_officer_id,
            'guidance_id' => $guidance->id,
        ]);

        return response()->json(['success' => true, 'message' => 'Guidance sent successfully']);
    }

    /**
     * Send message to accreditation officer.
     */
    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $application = Application::findOrFail($id);
        
        // Create message record
        $message = \App\Models\OfficerMessage::create([
            'application_id' => $application->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $application->assigned_officer_id,
            'message' => $request->message,
            'sender_type' => 'registrar',
        ]);

        \App\Services\ActivityLogger::log('message_sent_to_officer', $application, null, null, [
            'registrar_id' => Auth::id(),
            'officer_id' => $application->assigned_officer_id,
            'message_id' => $message->id,
        ]);

        return response()->json(['success' => true, 'message' => 'Message sent successfully']);
    }

    /**
     * Display operational reports - Officer activities only.
     */
    public function reports(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->subMonth()->format('Y-m-d');
        $dateTo = $request->date_to ?? now()->format('Y-m-d');

        // Officer activities statistics only
        $officerStats = DB::table('activities')
            ->join('users', 'activities.user_id', '=', 'users.id')
            ->where('activities.created_at', '>=', $dateFrom)
            ->where('activities.created_at', '<=', $dateTo)
            ->whereIn('activities.activity_type', [
                'application_approved',
                'application_rejected',
                'correction_requested',
                'forwarded_to_registrar'
            ])
            ->selectRaw('
                users.name as officer_name,
                COUNT(*) as total_activities,
                SUM(CASE WHEN activities.activity_type = "application_approved" THEN 1 ELSE 0 END) as approved_count,
                SUM(CASE WHEN activities.activity_type = "application_rejected" THEN 1 ELSE 0 END) as rejected_count,
                SUM(CASE WHEN activities.activity_type = "correction_requested" THEN 1 ELSE 0 END) as correction_count,
                SUM(CASE WHEN activities.activity_type = "forwarded_to_registrar" THEN 1 ELSE 0 END) as guidance_count
            ')
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_activities', 'desc')
            ->get();

        // Application processing times
        $processingTimes = Application::where('submitted_at', '>=', $dateFrom)
            ->where('submitted_at', '<=', $dateTo)
            ->whereNotNull('approved_at')
            ->get()
            ->map(function ($app) {
                return [
                    'reference' => $app->reference,
                    'type' => $app->application_type,
                    'submitted_at' => $app->submitted_at,
                    'approved_at' => $app->approved_at,
                    'processing_days' => $app->submitted_at->diffInDays($app->approved_at),
                ];
            });

        // Monthly trends for accreditation and registration only
        $monthlyTrends = Application::selectRaw('
            strftime("%Y-%m", submitted_at) as month,
            COUNT(*) as total,
            SUM(CASE WHEN application_type = "accreditation" THEN 1 ELSE 0 END) as accreditation_count,
            SUM(CASE WHEN application_type = "registration" THEN 1 ELSE 0 END) as registration_count,
            SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_count
        ')
        ->where('submitted_at', '>=', $dateFrom)
        ->where('submitted_at', '<=', $dateTo)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        // Prepare data for the trend chart
        $months = [];
        $approvedTrend = [];
        $returnedTrend = [];
        foreach ($monthlyTrends as $t) {
            $months[] = date('M Y', strtotime($t->month . '-01'));
            $approvedTrend[] = (int) $t->approved_count;
            // For 'returned', we'll sum correction_requested and returned_to_applicant
            $returnedTrend[] = Application::whereIn('status', [Application::CORRECTION_REQUESTED, Application::RETURNED_TO_APPLICANT])
                ->whereRaw('strftime("%Y-%m", submitted_at) = ?', [$t->month])
                ->count();
        }

        return view('staff.registrar.reports', compact(
            'officerStats', 
            'processingTimes', 
            'monthlyTrends', 
            'dateFrom', 
            'dateTo',
            'months',
            'approvedTrend',
            'returnedTrend'
        ));
    }

    /**
     * Display downloads page - Accreditation and Registration only.
     */
    public function downloads(Request $request)
    {
        // Get accreditation and registration related downloads only
        $downloads = \App\Models\Download::whereIn('category', ['accreditation', 'registration'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('staff.registrar.downloads', compact('downloads'));
    }

    /**
     * Export applications data.
     */
    public function exportApplications(Request $request)
    {
        $query = Application::with(['applicant', 'assignedOfficer']);

        // Apply same filters as applications method
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'awaiting_review':
                    $query->where('registrar_reviewed', false)
                          ->where('status', Application::OFFICER_APPROVED);
                    break;
                case 'forwarded':
                    $query->where('status', Application::FORWARDED_TO_REGISTRAR);
                    break;
                case 'returned':
                    $query->where('status', Application::CORRECTION_REQUESTED);
                    break;
            }
        }

        // Apply other filters
        if ($request->filled('application_type')) {
            $query->where('application_type', $request->application_type);
        }
        if ($request->filled('request_type')) {
            $query->where('request_type', $request->request_type);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('applicant', function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        $applications = $query->get();

        $filename = 'registrar_applications_export_' . now()->format('Ymd_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($applications) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'Reference',
                'Applicant Name',
                'Email',
                'Application Type',
                'Request Type',
                'Status',
                'Payment Status',
                'Submitted Date',
                'Approved Date',
                'Registrar Reviewed',
                'Assigned Officer',
                'Has Anomaly',
                'Processing Days'
            ]);

            foreach ($applications as $app) {
                fputcsv($file, [
                    $app->reference,
                    $app->applicant?->name ?? '',
                    $app->applicant?->email ?? '',
                    $app->application_type,
                    $app->request_type,
                    $app->statusLabel(),
                    $app->payment_status,
                    optional($app->submitted_at)->format('Y-m-d H:i'),
                    optional($app->approved_at)->format('Y-m-d H:i'),
                    $app->registrar_reviewed ? 'Yes' : 'No',
                    $app->assignedOfficer?->name ?? '',
                    $app->has_anomaly ? 'Yes' : 'No',
                    $app->approved_at ? $app->submitted_at->diffInDays($app->approved_at) : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export dashboard report.
     */
    public function exportDashboardReport()
    {
        $data = [
            'export_date' => now()->format('Y-m-d H:i:s'),
            'total_applications' => Application::count(),
            'awaiting_review' => Application::where('registrar_reviewed', false)
                ->where('status', Application::OFFICER_APPROVED)
                ->count(),
            'returned_for_correction' => Application::where('status', Application::CORRECTION_REQUESTED)
                ->count(),
            'forwarded_to_registrar' => Application::where('status', Application::FORWARDED_TO_REGISTRAR)
                ->count(),
            'cards_today' => AccreditationRecord::whereDate('created_at', today())->count() + 
                            RegistrationRecord::whereDate('created_at', today())->count(),
        ];

        $filename = 'registrar_dashboard_report_' . now()->format('Ymd_His') . '.json';
        
        return response()->json($data, 200, [
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // Helper methods for activity display
    private function getActivityTitle($activity)
    {
        return match($activity->activity_type) {
            'application_approved' => 'Application Approved',
            'application_rejected' => 'Application Rejected',
            'payment_confirmed' => 'Payment Confirmed',
            'payment_verified' => 'Payment Verified',
            'card_generated' => 'Card Generated',
            'certificate_issued' => 'Certificate Issued',
            default => 'Activity',
        };
    }

    private function getActivityDescription($activity)
    {
        $user = $activity->user?->name ?? 'Unknown';
        
        return match($activity->activity_type) {
            'application_approved' => "{$user} approved application",
            'application_rejected' => "{$user} rejected application",
            'payment_confirmed' => "{$user} confirmed payment",
            'payment_verified' => "{$user} verified payment",
            'card_generated' => "{$user} generated card",
            'certificate_issued' => "{$user} issued certificate",
            default => "Activity by {$user}",
        };
    }

    private function getActivityIcon($activity)
    {
        return match($activity->activity_type) {
            'application_approved' => 'check-line',
            'application_rejected' => 'close-line',
            'payment_confirmed' => 'money-dollar-circle-line',
            'payment_verified' => 'shield-check-line',
            'card_generated' => 'bank-card-line',
            'certificate_issued' => 'award-line',
            default => 'file-list-3-line',
        };
    }

    private function getActivityColor($activity)
    {
        return match($activity->activity_type) {
            'application_approved' => 'success',
            'application_rejected' => 'danger',
            'payment_confirmed' => 'primary',
            'payment_verified' => 'info',
            'card_generated' => 'warning',
            'certificate_issued' => 'success',
            default => 'primary',
        };
    }
}

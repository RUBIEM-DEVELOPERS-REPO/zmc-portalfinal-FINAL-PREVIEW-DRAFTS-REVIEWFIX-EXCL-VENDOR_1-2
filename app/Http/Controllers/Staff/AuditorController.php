<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\AuditFlag;
use App\Models\AuditLog;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuditorController extends Controller
{
    /**
     * Auditor dashboard (KPIs)
     */
    public function dashboard(Request $request)
    {
        $base = Application::query();

        $totalApplications = (clone $base)->count();
        $approvedCount = (clone $base)->whereIn('status', [
            Application::OFFICER_APPROVED,
            Application::REGISTRAR_APPROVED,
            Application::PAID_CONFIRMED,
            Application::PRODUCTION_QUEUE,
            Application::CARD_GENERATED,
            Application::CERT_GENERATED,
            Application::PRINTED,
            Application::ISSUED,
        ])->count();

        $rejectedCount = (clone $base)->whereIn('status', [
            Application::OFFICER_REJECTED,
            Application::REGISTRAR_REJECTED,
        ])->count();

        $waiversApproved = (clone $base)->where('waiver_status', 'approved')->count();
        $proofsApproved  = (clone $base)->where('proof_status', 'approved')->count();
        $paynowConfirmed = (clone $base)->whereNotNull('paynow_confirmed_at')->count();

        $irregularApprovedWithoutPayment = (clone $base)
            ->whereIn('status', [
                Application::PAID_CONFIRMED,
                Application::PRODUCTION_QUEUE,
                Application::CARD_GENERATED,
                Application::CERT_GENERATED,
                Application::PRINTED,
                Application::ISSUED,
            ])
            ->where(function ($w) {
                $w->whereNull('paynow_confirmed_at')
                  ->where(function ($x) {
                      $x->whereNull('proof_status')->orWhere('proof_status', '!=', 'approved');
                  })
                  ->where(function ($x) {
                      $x->whereNull('waiver_status')->orWhere('waiver_status', '!=', 'approved');
                  });
            })
            ->count();

        $recentFlags = AuditFlag::query()
            ->latest()->limit(10)->get();

        // System-wide activity feed (all roles)
        $activity = ActivityLog::query()
            ->with(['entity','user'])
            ->latest('created_at')
            ->limit(12)
            ->get();

        return view('staff.auditor.dashboard', compact(
            'totalApplications','approvedCount','rejectedCount',
            'waiversApproved','proofsApproved','paynowConfirmed',
            'irregularApprovedWithoutPayment','recentFlags','activity'
        ));
    }

    /**
     * Analytics page (charts)
     */
    public function analytics(Request $request)
    {
        $days = (int)($request->get('days') ?? 30);
        $days = max(7, min(365, $days));
        $to = Carbon::now()->endOfDay();
        $from = (clone $to)->subDays($days - 1)->startOfDay();

        // Line chart: applications per day
        $daily = Application::query()
            ->selectRaw("date(created_at) as d, count(*) as c")
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('c', 'd')
            ->toArray();

        $approvedDaily = Application::query()
            ->selectRaw("date(created_at) as d, count(*) as c")
            ->whereBetween('created_at', [$from, $to])
            ->whereIn('status', [
                Application::OFFICER_APPROVED,
                Application::REGISTRAR_APPROVED,
                Application::PAID_CONFIRMED,
                Application::PRODUCTION_QUEUE,
                Application::CARD_GENERATED,
                Application::CERT_GENERATED,
                Application::PRINTED,
                Application::ISSUED,
            ])
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('c', 'd')
            ->toArray();

        $rejectedDaily = Application::query()
            ->selectRaw("date(created_at) as d, count(*) as c")
            ->whereBetween('created_at', [$from, $to])
            ->whereIn('status', [
                Application::OFFICER_REJECTED,
                Application::REGISTRAR_REJECTED,
            ])
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('c', 'd')
            ->toArray();

        $labels = [];
        $seriesApplications = [];
        $seriesApproved     = [];
        $seriesRejected     = [];

        for ($i = 0; $i < $days; $i++) {
            $date = (clone $from)->addDays($i)->toDateString();
            $labels[] = $date;
            $seriesApplications[] = (int)($daily[$date] ?? 0);
            $seriesApproved[]     = (int)($approvedDaily[$date] ?? 0);
            $seriesRejected[]     = (int)($rejectedDaily[$date] ?? 0);
        }

        // Anomaly Severity Breakdown
        $severityCounts = AuditFlag::query()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('severity, count(*) as c')
            ->groupBy('severity')
            ->pluck('c', 'severity')
            ->toArray();

        $anomalyLabels = ['High', 'Medium', 'Low'];
        $anomalySeries = [
            (int)($severityCounts['high'] ?? 0),
            (int)($severityCounts['medium'] ?? 0),
            (int)($severityCounts['low'] ?? 0),
        ];

        // Payment Verification Distribution
        $paynowCount = Application::whereBetween('created_at', [$from, $to])->whereNotNull('paynow_confirmed_at')->count();
        $proofCount  = Application::whereBetween('created_at', [$from, $to])->where('proof_status', 'approved')->count();
        $waiverCount = Application::whereBetween('created_at', [$from, $to])->where('waiver_status', 'approved')->count();

        $paymentLabels = ['Paynow Confirmed', 'Proof Approved', 'Waiver Approved'];
        $paymentSeries = [$paynowCount, $proofCount, $waiverCount];

        // Pie chart: application buckets (Existing logic refined)
        $bucketCounts = Application::query()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw("request_type, application_type, journalist_scope, count(*) as c")
            ->groupBy('request_type', 'application_type', 'journalist_scope')
            ->get()
            ->reduce(function ($carry, $row) {
                $bucket = Application::bucketKey(
                    (string)$row->request_type,
                    (string)$row->application_type,
                    (string)$row->journalist_scope
                );
                if ($bucket) {
                    $carry[$bucket] = ($carry[$bucket] ?? 0) + (int)$row->c;
                }
                return $carry;
            }, []);

        $bucketLabels = Application::bucketLabels();
        $pieLabels = [];
        $pieValues = [];
        foreach ($bucketLabels as $key => $label) {
            $pieLabels[] = $label;
            $pieValues[] = (int)($bucketCounts[$key] ?? 0);
        }

        return view('staff.auditor.analytics', compact(
            'days','from','to',
            'labels','seriesApplications','seriesApproved','seriesRejected',
            'anomalyLabels','anomalySeries',
            'paymentLabels','paymentSeries',
            'pieLabels','pieValues'
        ));
    }

    /**
     * User login events + per-user activity trails
     */
    public function logins(Request $request)
    {
        $q = trim((string)$request->get('q'));
        $from = $request->date('date_from') ?? $request->date('from');
        $to = $request->date('date_to') ?? $request->date('to');

        $logs = AuditLog::query()
            ->with(['actor'])
            ->where('action', 'like', '%login%')
            ->when($from, fn($x) => $x->whereDate('created_at', '>=', $from))
            ->when($to, fn($x) => $x->whereDate('created_at', '<=', $to))
            ->when($q, function ($query) use ($q) {
                $query->where('ip', 'like', "%{$q}%")
                    ->orWhere('action', 'like', "%{$q}%")
                    ->orWhereHas('actor', function ($u) use ($q) {
                        $u->where('name', 'like', "%{$q}%")
                          ->orWhere('email', 'like', "%{$q}%");
                    });
            })
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        // For each login event, fetch a compact slice of recent actions by that user
        $actorIds = $logs->pluck('actor_user_id')->filter()->unique()->values()->all();
        $recentByUser = [];
        if (!empty($actorIds)) {
            $recent = AuditLog::query()
                ->with(['actor'])
                ->whereIn('actor_user_id', $actorIds)
                ->orderByDesc('created_at')
                ->limit(200)
                ->get();

            foreach ($recent as $r) {
                $recentByUser[$r->actor_user_id] = $recentByUser[$r->actor_user_id] ?? [];
                if (count($recentByUser[$r->actor_user_id]) < 8) {
                    $recentByUser[$r->actor_user_id][] = $r;
                }
            }
        }

        return view('staff.auditor.logins', compact('logs','q','from','to','recentByUser'));
    }

    /** Application & accreditation audits */
    public function applications(Request $request)
    {
        $q = Application::query()->with('applicant');

        // Filter: only show applications approved for payment or further
        $q->whereNotIn('status', [Application::DRAFT, Application::SUBMITTED, Application::OFFICER_REVIEW, Application::CORRECTION_REQUESTED]);

        // Unified application bucket filter (8 types)
        if ($request->filled('application_type')) {
            $q->applyBucket((string)$request->get('application_type'));
        } else {
            // Backward-compatible filters (if provided)
            if ($request->filled('request_type')) $q->where('request_type', $request->string('request_type'));
            if ($request->filled('scope')) $q->where('journalist_scope', $request->string('scope'));
            if ($request->filled('legacy_application_type')) $q->where('application_type', $request->string('legacy_application_type'));
        }
        if ($request->filled('status')) $q->where('status', $request->string('status'));
        if ($request->filled('date_from')) $q->whereDate('created_at', '>=', $request->date('date_from'));
        else if ($request->filled('from')) $q->whereDate('created_at', '>=', $request->date('from'));

        if ($request->filled('date_to')) $q->whereDate('created_at', '<=', $request->date('date_to'));
        else if ($request->filled('to')) $q->whereDate('created_at', '<=', $request->date('to'));
        if ($request->filled('search')) {
            $s = trim((string)$request->get('search'));
            $q->where(function($w) use ($s) {
                $w->where('reference', 'like', "%{$s}%")
                  ->orWhere('paynow_reference', 'like', "%{$s}%")
                  ->orWhereHas('applicant', function($u) use ($s){
                      $u->where('name', 'like', "%{$s}%")
                        ->orWhere('email', 'like', "%{$s}%");
                  });
            });
        }

        $applications = $q->latest()->paginate(25)->withQueryString();

        return view('staff.auditor.applications', compact('applications'));
    }

    /** PayNow & fees audit (read-only; derived from Application payment fields) */
    public function paynow(Request $request)
    {
        $q = Application::query()->with('applicant')
            ->where(function ($w) {
                $w->whereNotNull('paynow_reference')
                  ->orWhereNotNull('paynow_poll_url')
                  ->orWhereNotNull('payment_status')
                  ->orWhereNotNull('paynow_confirmed_at');
            });

        if ($request->filled('status')) $q->where('payment_status', $request->string('status'));
        if ($request->filled('from')) $q->whereDate('created_at', '>=', $request->date('from'));
        if ($request->filled('to')) $q->whereDate('created_at', '<=', $request->date('to'));

        $applications = $q->latest()->paginate(25)->withQueryString();

        // Duplicate PayNow references (simple detection)
        $duplicateRefs = Application::query()
            ->selectRaw('paynow_reference, COUNT(*) as c')
            ->whereNotNull('paynow_reference')
            ->groupBy('paynow_reference')
            ->having('c', '>', 1)
            ->pluck('paynow_reference')
            ->toArray();

        return view('staff.auditor.paynow', compact('applications','duplicateRefs'));
    }

    public function proofs(Request $request)
    {
        $q = Application::query()->with('applicant')
            ->whereNotNull('payment_proof_path');

        if ($request->filled('status')) $q->where('proof_status', $request->string('status'));
        if ($request->filled('date_from')) $q->whereDate('payment_proof_uploaded_at', '>=', $request->date('date_from'));
        elseif ($request->filled('from')) $q->whereDate('payment_proof_uploaded_at', '>=', $request->date('from'));

        if ($request->filled('date_to')) $q->whereDate('payment_proof_uploaded_at', '<=', $request->date('date_to'));
        elseif ($request->filled('to')) $q->whereDate('payment_proof_uploaded_at', '<=', $request->date('to'));

        $applications = $q->latest('payment_proof_uploaded_at')->paginate(25)->withQueryString();
        return view('staff.auditor.proofs', compact('applications'));
    }

    public function waivers(Request $request)
    {
        $q = Application::query()->with('applicant')
            ->whereNotNull('waiver_path');

        if ($request->filled('status')) $q->where('waiver_status', $request->string('status'));
        if ($request->filled('date_from')) $q->whereDate('updated_at', '>=', $request->date('date_from'));
        elseif ($request->filled('from')) $q->whereDate('updated_at', '>=', $request->date('from'));

        if ($request->filled('date_to')) $q->whereDate('updated_at', '<=', $request->date('date_to'));
        elseif ($request->filled('to')) $q->whereDate('updated_at', '<=', $request->date('to'));

        $applications = $q->latest('updated_at')->paginate(25)->withQueryString();
        return view('staff.auditor.waivers', compact('applications'));
    }

    /** Immutable logs (full audit trail) */
    public function logs(Request $request)
    {
        $q = trim((string)$request->get('q'));

        $logs = AuditLog::query()
            ->with(['actor'])
            ->when(($request->filled('date_from') || $request->filled('from')), fn($x) => $x->whereDate('created_at', '>=', ($request->date('date_from') ?? $request->date('from'))))
            ->when(($request->filled('date_to') || $request->filled('to')), fn($x) => $x->whereDate('created_at', '<=', ($request->date('date_to') ?? $request->date('to'))))
            ->when($q, function($query) use ($q) {
                $query->where('action', 'like', "%{$q}%")
                      ->orWhere('model_type', 'like', "%{$q}%")
                      ->orWhere('model_id', 'like', "%{$q}%")
                      ->orWhere('ip', 'like', "%{$q}%");
                $query->orWhereHas('actor', function ($u) use ($q) {
                    $u->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('staff.auditor.logs', compact('logs','q'));
    }

    /** Security oversight: focused view of security-related logs */
    public function security(Request $request)
    {
        $q = trim((string)$request->get('q'));
        $logs = AuditLog::query()
            ->where(function ($w) {
                $w->where('action', 'like', '%login%')
                  ->orWhere('action', 'like', '%otp%')
                  ->orWhere('action', 'like', '%2fa%')
                  ->orWhere('action', 'like', '%password%')
                  ->orWhere('action', 'like', '%role%')
                  ->orWhere('action', 'like', '%permission%');
            })
            ->when($q, fn($x) => $x->where('action', 'like', "%{$q}%")
                                  ->orWhere('ip', 'like', "%{$q}%")
                                  ->orWhere('user_agent', 'like', "%{$q}%"))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('staff.auditor.security', compact('logs','q'));
    }

    /** Reports (CSV export) */
    public function reports(Request $request)
    {
        $from = ($request->date('date_from') ?? $request->date('from'))?->startOfDay();
        $to   = ($request->date('date_to') ?? $request->date('to'))?->endOfDay();

        $base = Application::query()->when($from, fn($q) => $q->where('created_at', '>=', $from))
                                     ->when($to, fn($q) => $q->where('created_at', '<=', $to));

        $stats = [
            'total' => (clone $base)->count(),
            'approved' => (clone $base)->whereIn('status', [
                Application::OFFICER_APPROVED,
                Application::REGISTRAR_APPROVED,
                Application::PAID_CONFIRMED,
                Application::PRODUCTION_QUEUE,
                Application::CARD_GENERATED,
                Application::CERT_GENERATED,
                Application::PRINTED,
                Application::ISSUED,
            ])->count(),
            'rejected' => (clone $base)->whereIn('status', [
                Application::OFFICER_REJECTED,
                Application::REGISTRAR_REJECTED,
            ])->count(),
            'waivers_approved' => (clone $base)->where('waiver_status','approved')->count(),
            'proofs_approved'  => (clone $base)->where('proof_status','approved')->count(),
            'paynow_confirmed' => (clone $base)->whereNotNull('paynow_confirmed_at')->count(),
        ];

        return view('staff.auditor.reports', compact('from','to','stats'));
    }

    public function reportsCsv(Request $request): StreamedResponse
    {
        $from = ($request->date('date_from') ?? $request->date('from'))?->startOfDay();
        $to   = ($request->date('date_to') ?? $request->date('to'))?->endOfDay();

        $base = Application::query()->with('applicant')
            ->when($from, fn($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn($q) => $q->where('created_at', '<=', $to))
            ->orderByDesc('created_at');

        $filename = 'audit_report_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($base) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'Reference','Applicant','Email','Application Type','Request Type','Scope','Status','Created At',
                'PayNow Ref','Payment Status','PayNow Confirmed At','Proof Status','Waiver Status'
            ]);
            $base->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $a) {
                    fputcsv($out, [
                        $a->reference,
                        optional($a->applicant)->name,
                        optional($a->applicant)->email,
                        $a->application_type,
                        $a->request_type,
                        $a->journalist_scope,
                        $a->status,
                        optional($a->created_at)->format('Y-m-d H:i:s'),
                        $a->paynow_reference,
                        $a->payment_status,
                        optional($a->paynow_confirmed_at)->format('Y-m-d H:i:s'),
                        $a->proof_status,
                        $a->waiver_status,
                    ]);
                }
            });
            fclose($out);
        }, $filename);
    }

    /** Export User Logins as CSV */
    public function loginsCsv(Request $request): StreamedResponse
    {
        $q = $request->input('q');
        $from = $request->date('date_from') ?? $request->date('from');
        $to = $request->date('date_to') ?? $request->date('to');

        // Fetch all login-related audit logs (using AuditLog model, not ActivityLog)
        $logs = AuditLog::with('actor:id,name,email')
            ->where('action', 'like', '%login%')
            ->when($from, fn($x) => $x->whereDate('created_at', '>=', $from))
            ->when($to, fn($x) => $x->whereDate('created_at', '<=', $to))
            ->when($q, function ($query) use ($q) {
                $query->where('ip', 'like', "%{$q}%")
                    ->orWhere('action', 'like', "%{$q}%")
                    ->orWhereHas('actor', function ($u) use ($q) {
                        $u->where('name', 'like', "%{$q}%")
                          ->orWhere('email', 'like', "%{$q}%");
                    });
            })
            ->orderByDesc('created_at')
            ->limit(5000)
            ->get();

        $filename = 'user_logins_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($logs) {
            $out = fopen('php://output', 'w');
            
            // Write headers
            fputcsv($out, ['Time', 'User', 'Email', 'Action', 'IP Address']);
            
            // Write data rows
            if ($logs->count() > 0) {
                foreach ($logs as $log) {
                    fputcsv($out, [
                        $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : '',
                        $log->actor ? $log->actor->name : 'User #' . ($log->actor_user_id ?? 'Unknown'),
                        $log->actor ? $log->actor->email : '',
                        str_replace('_', ' ', $log->action ?? ''),
                        $log->ip ?? '',
                    ]);
                }
            } else {
                // Add a row indicating no data
                fputcsv($out, ['No login records found for the selected criteria', '', '', '', '']);
            }
            
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /** Export Applications Audit as CSV */
    public function applicationsCsv(Request $request): StreamedResponse
    {
        $type = $request->input('type');
        $status = $request->input('status');
        $from = $request->date('from');
        $to = $request->date('to');

        $applications = Application::with('applicant:id,name,email')
            ->when($type, fn($q) => $q->where('application_type', $type))
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($from, fn($q) => $q->where('created_at', '>=', $from->startOfDay()))
            ->when($to, fn($q) => $q->where('created_at', '<=', $to->endOfDay()))
            ->orderByDesc('created_at')
            ->limit(5000)
            ->get();

        $filename = 'applications_audit_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($applications) {
            $out = fopen('php://output', 'w');
            
            fputcsv($out, ['Reference', 'Applicant', 'Email', 'Type', 'Status', 'Created', 'Updated']);
            
            if ($applications->count() > 0) {
                foreach ($applications as $app) {
                    fputcsv($out, [
                        $app->reference ?? '',
                        $app->applicant ? $app->applicant->name : '',
                        $app->applicant ? $app->applicant->email : '',
                        $app->application_type ?? '',
                        $app->status ?? '',
                        $app->created_at ? $app->created_at->format('Y-m-d H:i:s') : '',
                        $app->updated_at ? $app->updated_at->format('Y-m-d H:i:s') : '',
                    ]);
                }
            } else {
                fputcsv($out, ['No applications found for the selected criteria', '', '', '', '', '', '']);
            }
            
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /** Export PayNow Payments as CSV */
    public function paynowCsv(Request $request): StreamedResponse
    {
        $q = $request->input('q');
        $from = $request->date('from');
        $to = $request->date('to');

        $payments = Application::with('applicant:id,name,email')
            ->whereNotNull('paynow_reference')
            ->when($q, fn($qb) => $qb->where(function($sub) use ($q) {
                $sub->where('paynow_reference', 'like', "%{$q}%")
                    ->orWhere('reference', 'like', "%{$q}%")
                    ->orWhereHas('applicant', fn($u) => $u->where('name', 'like', "%{$q}%"));
            }))
            ->when($from, fn($qb) => $qb->where('created_at', '>=', $from->startOfDay()))
            ->when($to, fn($qb) => $qb->where('created_at', '<=', $to->endOfDay()))
            ->orderByDesc('created_at')
            ->limit(5000)
            ->get();

        $filename = 'paynow_payments_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($payments) {
            $out = fopen('php://output', 'w');
            
            fputcsv($out, ['Reference', 'Applicant', 'PayNow Ref', 'Amount', 'Status', 'Confirmed At', 'Created']);
            
            if ($payments->count() > 0) {
                foreach ($payments as $app) {
                    fputcsv($out, [
                        $app->reference ?? '',
                        $app->applicant ? $app->applicant->name : '',
                        $app->paynow_reference ?? '',
                        $app->paynow_amount ?? '',
                        $app->payment_status ?? '',
                        $app->paynow_confirmed_at ? $app->paynow_confirmed_at->format('Y-m-d H:i:s') : '',
                        $app->created_at ? $app->created_at->format('Y-m-d H:i:s') : '',
                    ]);
                }
            } else {
                fputcsv($out, ['No PayNow payments found for the selected criteria', '', '', '', '', '', '']);
            }
            
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /** Export Audit Logs as CSV */
    public function logsCsv(Request $request): StreamedResponse
    {
        $q = $request->input('q');

        // Use AuditLog to match the logs() method
        $logs = AuditLog::with('actor:id,name,email')
            ->when(($request->filled('date_from') || $request->filled('from')), fn($x) => $x->whereDate('created_at', '>=', ($request->date('date_from') ?? $request->date('from'))))
            ->when(($request->filled('date_to') || $request->filled('to')), fn($x) => $x->whereDate('created_at', '<=', ($request->date('date_to') ?? $request->date('to'))))
            ->when($q, function($query) use ($q) {
                $query->where('action', 'like', "%{$q}%")
                      ->orWhere('model_type', 'like', "%{$q}%")
                      ->orWhere('model_id', 'like', "%{$q}%")
                      ->orWhere('ip', 'like', "%{$q}%");
                $query->orWhereHas('actor', function ($u) use ($q) {
                    $u->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('created_at')
            ->limit(5000)
            ->get();

        $filename = 'audit_logs_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($logs) {
            $out = fopen('php://output', 'w');
            
            fputcsv($out, ['Time', 'Actor', 'Action', 'Model Type', 'Model ID', 'IP Address']);
            
            if ($logs->count() > 0) {
                foreach ($logs as $log) {
                    fputcsv($out, [
                        $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : '',
                        $log->actor ? $log->actor->name : 'User #' . ($log->actor_user_id ?? 'Unknown'),
                        str_replace('_', ' ', $log->action ?? ''),
                        $log->model_type ? class_basename($log->model_type) : '',
                        $log->model_id ?? '',
                        $log->ip ?? '',
                    ]);
                }
            } else {
                fputcsv($out, ['No audit logs found for the selected criteria', '', '', '', '', '']);
            }
            
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /** Export Security Logs as CSV */
    public function securityCsv(Request $request): StreamedResponse
    {
        $q = $request->input('q');

        // Use AuditLog to match the security() method
        $logs = AuditLog::with('actor:id,name,email')
            ->where(function ($w) {
                $w->where('action', 'like', '%login%')
                  ->orWhere('action', 'like', '%otp%')
                  ->orWhere('action', 'like', '%2fa%')
                  ->orWhere('action', 'like', '%password%')
                  ->orWhere('action', 'like', '%role%')
                  ->orWhere('action', 'like', '%permission%');
            })
            ->when($q, fn($x) => $x->where('action', 'like', "%{$q}%")
                                  ->orWhere('ip', 'like', "%{$q}%")
                                  ->orWhere('user_agent', 'like', "%{$q}%"))
            ->orderByDesc('created_at')
            ->limit(5000)
            ->get();

        $filename = 'security_logs_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($logs) {
            $out = fopen('php://output', 'w');
            
            fputcsv($out, ['Time', 'User', 'Action', 'IP Address', 'User Agent']);
            
            if ($logs->count() > 0) {
                foreach ($logs as $log) {
                    fputcsv($out, [
                        $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : '',
                        $log->actor ? $log->actor->name : 'User #' . ($log->actor_user_id ?? 'Unknown'),
                        str_replace('_', ' ', $log->action ?? ''),
                        $log->ip ?? '',
                        $log->user_agent ?? '',
                    ]);
                }
            } else {
                fputcsv($out, ['No security logs found for the selected criteria', '', '', '', '']);
            }
            
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /** Export Activity Feed as CSV */
    public function activityCsv(Request $request): StreamedResponse
    {
        // Use ActivityLog with correct 'user' relationship (not 'actor')
        $logs = ActivityLog::with('user:id,name,email')
            ->orderByDesc('created_at')
            ->limit(1000)
            ->get();

        $filename = 'activity_feed_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($logs) {
            $out = fopen('php://output', 'w');
            
            fputcsv($out, ['Time', 'User', 'Role', 'Action', 'Reference', 'From Status', 'To Status']);
            
            if ($logs->count() > 0) {
                foreach ($logs as $log) {
                    $ref = '';
                    try {
                        if ($log->entity) {
                            $ref = $log->entity->reference ?? '';
                        }
                    } catch (\Throwable $e) {}
                    
                    if (!$ref && $log->entity_type && $log->entity_id) {
                        $ref = class_basename($log->entity_type) . '-' . $log->entity_id;
                    }

                    fputcsv($out, [
                        $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : '',
                        $log->user ? $log->user->name : 'User #' . ($log->user_id ?? 'Unknown'),
                        $log->user_role ?? '',
                        str_replace('_', ' ', $log->action ?? ''),
                        $ref,
                        $log->from_status ?? '',
                        $log->to_status ?? '',
                    ]);
                }
            } else {
                fputcsv($out, ['No activity found', '', '', '', '', '', '']);
            }
            
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /** Flag an anomaly (Auditor-only) */
    public function flag(Request $request)
    {
        $data = $request->validate([
            'entity_type' => ['required','string','max:80'],
            'entity_id'   => ['required','integer'],
            'severity'    => ['nullable','in:low,medium,high'],
            'reason'      => ['required','string','max:5000'],
        ]);

        $flag = AuditFlag::create([
            'auditor_user_id' => Auth::id(),
            'entity_type' => $data['entity_type'],
            'entity_id' => $data['entity_id'],
            'severity' => $data['severity'] ?? 'medium',
            'reason' => $data['reason'],
        ]);

        AuditLog::create([
            'actor_user_id' => Auth::id(),
            'action' => 'auditor_flagged_anomaly',
            'model_type' => $data['entity_type'],
            'model_id' => $data['entity_id'],
            'meta' => [
                'severity' => $flag->severity,
                'reason' => $flag->reason,
                'flag_id' => $flag->id,
            ],
            'ip' => $request->ip(),
            'user_agent' => substr((string)$request->userAgent(), 0, 500),
        ]);

        return back()->with('success', 'Anomaly flagged.');
    }

    public function proofsCsv(Request $request): StreamedResponse
    {
        $status = $request->input('status');
        $from = $request->date('date_from') ?? $request->date('from');
        $to = $request->date('date_to') ?? $request->date('to');

        $applications = Application::with('applicant:id,name,email')
            ->whereNotNull('payment_proof_path')
            ->when($status, fn($q) => $q->where('proof_status', $status))
            ->when($from, fn($q) => $q->whereDate('payment_proof_uploaded_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('payment_proof_uploaded_at', '<=', $to))
            ->orderByDesc('payment_proof_uploaded_at')
            ->limit(5000)
            ->get();

        $filename = 'payment_proofs_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($applications) {
            $out = fopen('php://output', 'w');
            
            fputcsv($out, ['Reference', 'Applicant', 'Email', 'Proof Status', 'Uploaded At', 'Payment Status']);
            
            if ($applications->count() > 0) {
                foreach ($applications as $app) {
                    fputcsv($out, [
                        $app->reference ?? '',
                        $app->applicant ? $app->applicant->name : '',
                        $app->applicant ? $app->applicant->email : '',
                        $app->proof_status ?? '',
                        $app->payment_proof_uploaded_at ? $app->payment_proof_uploaded_at->format('Y-m-d H:i:s') : '',
                        $app->payment_status ?? '',
                    ]);
                }
            } else {
                fputcsv($out, ['No payment proofs found for the selected criteria', '', '', '', '', '']);
            }
            
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function waiversCsv(Request $request): StreamedResponse
    {
        $status = $request->input('status');
        $from = $request->date('date_from') ?? $request->date('from');
        $to = $request->date('date_to') ?? $request->date('to');

        $applications = Application::with('applicant:id,name,email')
            ->whereNotNull('waiver_path')
            ->when($status, fn($q) => $q->where('waiver_status', $status))
            ->when($from, fn($q) => $q->whereDate('updated_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('updated_at', '<=', $to))
            ->orderByDesc('updated_at')
            ->limit(5000)
            ->get();

        $filename = 'waivers_exemptions_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($applications) {
            $out = fopen('php://output', 'w');
            
            fputcsv($out, ['Reference', 'Applicant', 'Email', 'Waiver Status', 'Updated At', 'Payment Status']);
            
            if ($applications->count() > 0) {
                foreach ($applications as $app) {
                    fputcsv($out, [
                        $app->reference ?? '',
                        $app->applicant ? $app->applicant->name : '',
                        $app->applicant ? $app->applicant->email : '',
                        $app->waiver_status ?? '',
                        $app->updated_at ? $app->updated_at->format('Y-m-d H:i:s') : '',
                        $app->payment_status ?? '',
                    ]);
                }
            } else {
                fputcsv($out, ['No waivers found for the selected criteria', '', '', '', '', '']);
            }
            
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}

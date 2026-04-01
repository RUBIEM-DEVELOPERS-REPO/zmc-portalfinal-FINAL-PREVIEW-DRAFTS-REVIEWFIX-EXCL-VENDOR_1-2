<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Complaint;
use App\Models\Event;
use App\Models\Notice;
use App\Models\News;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadsController extends Controller
{
    public function index()
    {
        return view('admin.downloads.index');
    }

    /**
     * Quick CSV downloads for admin/staff.
     */
    public function csv(Request $request, string $type): StreamedResponse
    {
        if (session('active_staff_role') === 'it_admin') {
            abort(403, 'IT Personnel are restricted to View Only access. CSV exports are disabled.');
        }

        $filename = $type . '-' . now()->format('Y-m-d_His') . '.csv';

        $query = match ($type) {
            'users_staff' => User::query()->whereHas('roles'),
            'users_public' => User::query()->whereDoesntHave('roles'),
            'applications' => Application::query(),
            'notices' => Notice::query(),
            'events' => Event::query(),
            'news' => News::query(),
            'complaints' => Complaint::query(),
            default => abort(404),
        };

        // Apply Common Filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Apply Specific Filters
        if ($type === 'applications') {
            if ($request->filled('application_type')) {
                $query->where('application_type', $request->application_type);
            }
            if ($request->filled('request_type')) {
                $query->where('request_type', $request->request_type);
            }
            if ($request->filled('residency')) {
                $query->where('residency_type', $request->residency);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('is_renewal')) {
                $query->where('request_type', $request->is_renewal === 'yes' ? 'renewal' : 'new');
            }
        }

        if ($type === 'complaints' && $request->filled('status')) {
            $query->where('status', $request->status);
        }

        $cols = match ($type) {
            'users_staff', 'users_public' => ['id', 'name', 'email', 'created_at'],
            'applications' => ['id', 'reference', 'application_type', 'request_type', 'status', 'created_at'],
            'notices' => ['id', 'title', 'target_portal', 'is_published', 'published_at', 'created_at'],
            'events' => ['id', 'title', 'target_portal', 'is_published', 'starts_at', 'ends_at', 'created_at'],
            'news' => ['id', 'title', 'is_published', 'published_at', 'created_at'],
            'complaints' => ['id', 'name', 'email', 'category', 'status', 'created_at'],
            default => ['id', 'created_at'],
        };

        $query->orderByDesc('created_at');

        return response()->streamDownload(function () use ($query, $cols) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $cols);

            $query->chunk(500, function ($rows) use ($out, $cols) {
                foreach ($rows as $r) {
                    $line = [];
                    foreach ($cols as $c) {
                        $line[] = data_get($r, $c);
                    }
                    fputcsv($out, $line);
                }
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}

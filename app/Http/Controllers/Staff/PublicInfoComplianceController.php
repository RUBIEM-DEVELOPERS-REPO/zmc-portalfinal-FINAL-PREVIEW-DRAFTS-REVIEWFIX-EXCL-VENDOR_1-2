<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicInfoComplianceController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:public_info_compliance|super_admin|director|auditor']);
    }

    /**
     * Display the Public Information Compliance Manager dashboard.
     */
    public function dashboard()
    {
        $complaints = Complaint::orderByDesc('created_at')->paginate(15);
        $stats = $this->getComplaintsStats();

        return view('staff.public_info_compliance.dashboard', compact('complaints', 'stats'));
    }

    /**
     * Get complaints statistics for dashboard.
     */
    private function getComplaintsStats(): array
    {
        $total = Complaint::count();
        $pending = Complaint::where('status', 'pending')->count();
        $resolved = Complaint::where('status', 'resolved')->count();
        $appeals = Complaint::where('type', 'appeal')->count();
        $complaintCount = Complaint::where('type', 'complaint')->count();

        return [
            'total' => $total,
            'pending' => $pending,
            'resolved' => $resolved,
            'appeals' => $appeals,
            'complaints' => $complaintCount,
        ];
    }

    /**
     * Store a new complaint/appeal.
     */
    public function store(Request $request)
    {
        abort_unless(auth()->user()?->hasAnyRole(['public_info_compliance', 'super_admin', 'director']), 403);

        $data = $request->validate([
            'type' => ['required', 'in:complaint,appeal'],
            'name' => ['required', 'string', 'max:200'],
            'email' => ['required', 'email', 'max:200'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string'],
            'status' => ['nullable', 'in:pending,resolved'],
        ]);

        $data['status'] = $data['status'] ?? 'pending';
        $data['created_by'] = Auth::id();

        Complaint::create($data);

        \App\Support\AuditTrail::log('complaint_create', null, ['subject' => $data['subject']]);

        return back()->with('success', 'Complaint/Appeal recorded successfully.');
    }

    /**
     * Update a complaint/appeal.
     */
    public function update(Request $request, Complaint $complaint)
    {
        abort_unless(auth()->user()?->hasAnyRole(['public_info_compliance', 'super_admin', 'director']), 403);

        $data = $request->validate([
            'type' => ['required', 'in:complaint,appeal'],
            'name' => ['required', 'string', 'max:200'],
            'email' => ['required', 'email', 'max:200'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string'],
            'status' => ['required', 'in:pending,resolved'],
            'resolution_notes' => ['nullable', 'string'],
        ]);

        $data['resolved_at'] = $data['status'] === 'resolved' ? ($complaint->resolved_at ?? now()) : null;
        $data['resolved_by'] = $data['status'] === 'resolved' ? ($complaint->resolved_by ?? Auth::id()) : null;

        $complaint->update($data);

        \App\Support\AuditTrail::log('complaint_update', null, ['complaint_id' => $complaint->id, 'status' => $data['status']]);

        return back()->with('success', 'Complaint/Appeal updated successfully.');
    }

    /**
     * Delete a complaint/appeal.
     */
    public function destroy(Complaint $complaint)
    {
        abort_unless(auth()->user()?->hasAnyRole(['public_info_compliance', 'super_admin', 'director']), 403);

        $id = $complaint->id;
        $complaint->delete();

        \App\Support\AuditTrail::log('complaint_delete', null, ['complaint_id' => $id]);

        return back()->with('success', 'Complaint/Appeal deleted successfully.');
    }
}

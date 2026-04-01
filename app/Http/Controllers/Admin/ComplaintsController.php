<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $isSuper = $user->hasRole('super_admin');
        $isCompliance = $user->hasRole('public_info_compliance');
        $isResearch = $user->hasRole('research_training_standards');
        $isDirector = $user->hasRole('director');
        $isChiefAccountant = $user->hasRole('chief_accountant');

        // General entry check
        abort_unless($isSuper || $isCompliance || $isResearch || $isDirector || $isChiefAccountant || $user->can('receive_complaints_appeals'), 403);

        $type = $request->get('type');
        $status = $request->get('status');

        // Segregation:
        // Research/Director see ONLY complaints (unless type=appeal is explicitly denied or filtered)
        // Compliance/ChiefAccountant see ONLY appeals
        if (!$isSuper) {
            if ($isResearch || $isDirector) {
                if ($type === 'appeal') abort(403, 'Unauthorized to view appeals.');
                $type = 'complaint';
            } elseif ($isCompliance || $isChiefAccountant) {
                if ($type === 'complaint') abort(403, 'Unauthorized to view complaints.');
                $type = 'appeal';
            }
        }

        $q = Complaint::query()->orderByDesc('id');
        if (in_array($type, ['complaint','appeal'], true)) $q->where('type',$type);
        if (in_array($status, ['open','in_progress','resolved','closed'], true)) $q->where('status',$status);

        $items = $q->paginate(20);
        return view('admin.complaints.index', compact('items','type','status'));
    }

    public function update(Request $request, Complaint $complaint)
    {
        $user = auth()->user();
        $isSuper = $user->hasRole('super_admin');
        
        // Check update permission specifically for the type
        if (!$isSuper) {
            if ($complaint->type === 'complaint') {
                abort_unless($user->hasAnyRole(['research_training_standards','director']), 403);
            } else {
                abort_unless($user->hasAnyRole(['public_info_compliance','chief_accountant']), 403);
            }
        }

        $data = $request->validate([
            'status' => ['required','in:open,in_progress,resolved,closed'],
        ]);

        $complaint->update([
            'status' => $data['status'],
            'handled_by' => Auth::id(),
            'handled_at' => now(),
        ]);

        \App\Support\AuditTrail::log('complaint_update', null, ['complaint_id'=>$complaint->id,'status'=>$data['status']]);

        return back()->with('success','Updated.');
    }
}

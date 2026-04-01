<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\RenewalApplication;
use App\Models\Application;
use App\Models\RenewalChangeRequest;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RenewalController extends Controller
{
    /**
     * Show renewal dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        $renewals = RenewalApplication::where('applicant_user_id', $user->id)
            ->with('originalApplication')
            ->latest()
            ->paginate(10);
        
        return view('portal.renewals.index', compact('renewals'));
    }

    /**
     * Step 1: Select renewal type
     */
    public function selectType()
    {
        return view('portal.renewals.select_type');
    }

    /**
     * Store renewal type selection
     */
    public function storeType(Request $request)
    {
        $data = $request->validate([
            'renewal_type' => ['required', 'in:renewal,replacement'],
        ]);

        $renewal = RenewalApplication::create([
            'applicant_user_id' => Auth::id(),
            'renewal_type' => $data['renewal_type'],
            'status' => RenewalApplication::RENEWAL_TYPE_SELECTED,
            'current_stage' => 'type_selection',
            'last_action_at' => now(),
            'last_action_by' => Auth::id(),
        ]);

        ActivityLogger::log('renewal_type_selected', $renewal, null, RenewalApplication::RENEWAL_TYPE_SELECTED, [
            'renewal_type' => $data['renewal_type'],
        ]);

        return redirect()->route('accreditation.renewals.lookup', $renewal);
    }

    /**
     * Step 2: Number lookup
     */
    public function lookup(RenewalApplication $renewal)
    {
        // Verify ownership
        if ($renewal->applicant_user_id !== Auth::id()) {
            abort(403);
        }

        return view('portal.renewals.lookup', compact('renewal'));
    }

    /**
     * Perform number lookup
     */
    public function performLookup(Request $request, RenewalApplication $renewal)
    {
        // Verify ownership
        if ($renewal->applicant_user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'number' => ['required', 'string', 'max:50'],
        ]);

        $number = strtoupper(trim($data['number']));

        // Rate limiting check (simple implementation)
        $recentAttempts = RenewalApplication::where('applicant_user_id', Auth::id())
            ->where('looked_up_at', '>=', now()->subHour())
            ->count();

        if ($recentAttempts >= 5) {
            return back()->with('error', 'Too many lookup attempts. Please try again later.');
        }

        // Lookup original application (media practitioner accreditation only)
        $originalApp = null;
        
        // Both renewal and replacement types look up accreditation applications
        $originalApp = Application::where('application_type', 'accreditation')
            ->where(function($q) use ($number) {
                $q->where('reference', $number)
                  ->orWhereHas('accreditationRecord', function($q2) use ($number) {
                      $q2->where('accreditation_number', $number);
                  });
            })
            ->where('applicant_user_id', Auth::id())
            ->whereIn('status', [
                Application::ISSUED,
                Application::PRINTED,
                Application::REGISTRAR_APPROVED,
            ])
            ->first();

        // Update renewal with lookup result
        $renewal->update([
            'original_number' => $number,
            'original_application_id' => $originalApp?->id,
            'lookup_status' => $originalApp ? 'found' : 'not_found',
            'looked_up_at' => now(),
            'status' => $originalApp 
                ? RenewalApplication::RENEWAL_RECORD_FOUND 
                : RenewalApplication::RENEWAL_RECORD_NOT_FOUND,
            'last_action_at' => now(),
        ]);

        // Log attempt
        ActivityLogger::log('renewal_number_lookup', $renewal, $renewal->status, $renewal->status, [
            'renewal_type' => $renewal->renewal_type,
            'lookup_result' => $originalApp ? 'found' : 'not_found',
            // Don't log the actual number for security
        ]);

        if (!$originalApp) {
            return back()->with('error', 'Number not found. Please verify your number and try again.');
        }

        return redirect()->route('accreditation.renewals.confirm', $renewal);
    }

    /**
     * Step 3: Confirm changes
     */
    public function confirm(RenewalApplication $renewal)
    {
        // Verify ownership
        if ($renewal->applicant_user_id !== Auth::id()) {
            abort(403);
        }

        // Must have found record
        if ($renewal->lookup_status !== 'found') {
            return redirect()->route('accreditation.renewals.lookup', $renewal)
                ->with('error', 'Please complete the number lookup first.');
        }

        $renewal->load('originalApplication.applicant');

        return view('portal.renewals.confirm', compact('renewal'));
    }

    /**
     * Submit no changes confirmation
     */
    public function confirmNoChanges(RenewalApplication $renewal)
    {
        // Verify ownership
        if ($renewal->applicant_user_id !== Auth::id()) {
            abort(403);
        }

        $renewal->update([
            'has_changes' => false,
            'confirmation_type' => 'no_changes',
            'confirmed_at' => now(),
            'status' => RenewalApplication::RENEWAL_CONFIRMED_NO_CHANGES,
            'current_stage' => 'payment',
            'last_action_at' => now(),
        ]);

        ActivityLogger::log('renewal_confirmed_no_changes', $renewal, null, RenewalApplication::RENEWAL_CONFIRMED_NO_CHANGES, []);

        return redirect()->route('accreditation.renewals.payment', $renewal);
    }

    /**
     * Submit changes
     */
    public function submitChanges(Request $request, RenewalApplication $renewal)
    {
        // Verify ownership
        if ($renewal->applicant_user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'changes' => ['required', 'array', 'min:1'],
            'changes.*.field_name' => ['required', 'string'],
            'changes.*.old_value' => ['required', 'string'],
            'changes.*.new_value' => ['required', 'string'],
            'changes.*.supporting_document' => ['nullable', 'file', 'max:5120'],
        ]);

        DB::transaction(function() use ($renewal, $data) {
            // Store changes
            foreach ($data['changes'] as $index => $change) {
                $documentPath = null;
                
                if (isset($change['supporting_document'])) {
                    $file = $change['supporting_document'];
                    $documentPath = $file->store('renewal_change_documents', 'public');
                }

                RenewalChangeRequest::create([
                    'renewal_application_id' => $renewal->id,
                    'field_name' => $change['field_name'],
                    'old_value' => $change['old_value'],
                    'new_value' => $change['new_value'],
                    'supporting_document_path' => $documentPath,
                    'status' => 'pending',
                ]);
            }

            // Update renewal
            $renewal->update([
                'has_changes' => true,
                'confirmation_type' => 'with_changes',
                'confirmed_at' => now(),
                'change_requests' => $data['changes'], // Store summary
                'status' => RenewalApplication::RENEWAL_CONFIRMED_WITH_CHANGES,
                'current_stage' => 'payment',
                'last_action_at' => now(),
            ]);
        });

        ActivityLogger::log('renewal_changes_submitted', $renewal, null, RenewalApplication::RENEWAL_CONFIRMED_WITH_CHANGES, [
            'change_count' => count($data['changes']),
        ]);

        return redirect()->route('accreditation.renewals.payment', $renewal);
    }

    /**
     * Step 4: Payment
     */
    public function payment(RenewalApplication $renewal)
    {
        // Verify ownership
        if ($renewal->applicant_user_id !== Auth::id()) {
            abort(403);
        }

        // Must be confirmed
        if (!in_array($renewal->status, [
            RenewalApplication::RENEWAL_CONFIRMED_NO_CHANGES,
            RenewalApplication::RENEWAL_CONFIRMED_WITH_CHANGES,
        ])) {
            return redirect()->route('accreditation.renewals.confirm', $renewal)
                ->with('error', 'Please confirm your information first.');
        }

        return view('portal.renewals.payment', compact('renewal'));
    }

    /**
     * Submit PayNow payment
     */
    public function submitPaynow(Request $request, RenewalApplication $renewal)
    {
        // Verify ownership
        if ($renewal->applicant_user_id !== Auth::id()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'reference' => ['required', 'string', 'max:255'],
        ]);

        $renewal->update([
            'payment_method' => 'PAYNOW',
            'payment_reference' => $data['reference'],
            'payment_submitted_at' => now(),
            'status' => RenewalApplication::RENEWAL_SUBMITTED_AWAITING_ACCOUNTS_VERIFICATION,
            'current_stage' => 'accounts_verification',
            'last_action_at' => now(),
        ]);

        ActivityLogger::log('renewal_payment_paynow_submitted', $renewal, null, RenewalApplication::RENEWAL_SUBMITTED_AWAITING_ACCOUNTS_VERIFICATION, [
            'reference' => $data['reference'],
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Payment reference submitted successfully. Accounts will verify it shortly.',
        ]);
    }

    /**
     * Submit proof of payment
     */
    public function submitProof(Request $request, RenewalApplication $renewal)
    {
        // Verify ownership
        if ($renewal->applicant_user_id !== Auth::id()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payer_name' => ['nullable', 'string', 'max:200'],
            'reference' => ['nullable', 'string', 'max:255'],
            'proof_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $file = $request->file('proof_file');
        $path = $file->store('renewal_payment_proofs', 'public');
        $hash = hash_file('sha256', Storage::disk('public')->path($path));

        $renewal->update([
            'payment_method' => 'PROOF_UPLOAD',
            'payment_reference' => $data['reference'] ?? null,
            'payment_amount' => $data['amount'],
            'payment_date' => $data['payment_date'],
            'payment_proof_path' => $path,
            'payment_metadata' => [
                'payer_name' => $data['payer_name'] ?? null,
                'file_name' => $file->getClientOriginalName(),
                'file_hash' => $hash,
            ],
            'payment_submitted_at' => now(),
            'status' => RenewalApplication::RENEWAL_SUBMITTED_AWAITING_ACCOUNTS_VERIFICATION,
            'current_stage' => 'accounts_verification',
            'last_action_at' => now(),
        ]);

        ActivityLogger::log('renewal_payment_proof_submitted', $renewal, null, RenewalApplication::RENEWAL_SUBMITTED_AWAITING_ACCOUNTS_VERIFICATION, [
            'amount' => $data['amount'],
            'file_hash' => $hash,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Payment proof uploaded successfully. Accounts will verify it shortly.',
        ]);
    }

    /**
     * Show renewal details
     */
    public function show(RenewalApplication $renewal)
    {
        // Verify ownership
        if ($renewal->applicant_user_id !== Auth::id()) {
            abort(403);
        }

        $renewal->load(['originalApplication', 'changeRequests', 'paymentVerifier']);

        return view('portal.renewals.show', compact('renewal'));
    }
}

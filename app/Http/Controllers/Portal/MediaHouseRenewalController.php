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

class MediaHouseRenewalController extends Controller
{
    /**
     * Show renewal dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        $renewals = RenewalApplication::where('applicant_user_id', $user->id)
            ->where('renewal_type', 'registration') // Media house renewals
            ->with('originalApplication')
            ->latest()
            ->paginate(10);
        
        return view('portal.mediahouse.renewals.index', compact('renewals'));
    }

    /**
     * Start renewal flow directly with a preset type (renewal or replacement)
     */
    public function start(string $type)
    {
        abort_unless(in_array($type, ['renewal', 'replacement'], true), 404);

        $renewal = RenewalApplication::create([
            'applicant_user_id' => Auth::id(),
            'renewal_type' => 'registration', // Media house type
            'request_type' => $type, // renewal or replacement
            'status' => 'renewal_started',
            'current_stage' => 'type_selection',
            'last_action_at' => now(),
            'last_action_by' => Auth::id(),
        ]);

        ActivityLogger::log('mediahouse_renewal_started', $renewal, null, 'renewal_started', [
            'request_type' => $type,
        ]);

        return redirect()->route('mediahouse.renewals.lookup', $renewal);
    }

    /**
     * Step 1: Select renewal type (renewal or replacement)
     */
    public function selectType()
    {
        return view('portal.mediahouse.renewals.select_type');
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
            'renewal_type' => 'registration', // Media house type
            'request_type' => $data['renewal_type'], // renewal or replacement
            'status' => 'renewal_started',
            'current_stage' => 'type_selection',
            'last_action_at' => now(),
            'last_action_by' => Auth::id(),
        ]);

        ActivityLogger::log('mediahouse_renewal_started', $renewal, null, 'renewal_started', [
            'request_type' => $data['renewal_type'],
        ]);

        return redirect()->route('mediahouse.renewals.lookup', $renewal);
    }

    /**
     * Step 2: Registration number lookup
     */
    public function lookup(RenewalApplication $renewal)
    {
        // Verify ownership
        if ($renewal->applicant_user_id !== Auth::id()) {
            abort(403);
        }

        return view('portal.mediahouse.renewals.lookup', compact('renewal'));
    }

    /**
     * Perform registration number lookup
     */
    public function performLookup(Request $request, RenewalApplication $renewal)
    {
        // Verify ownership
        if ($renewal->applicant_user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'registration_number' => ['required', 'string', 'max:50'],
        ]);

        $number = strtoupper(trim($data['registration_number']));

        // Rate limiting check
        $recentAttempts = RenewalApplication::where('applicant_user_id', Auth::id())
            ->where('looked_up_at', '>=', now()->subHour())
            ->count();

        if ($recentAttempts >= 10) {
            return back()->with('error', 'Too many lookup attempts. Please try again later.');
        }

        // Check for duplicate active renewals
        $existingRenewal = RenewalApplication::where('applicant_user_id', Auth::id())
            ->where('original_number', $number)
            ->whereNotIn('status', ['renewal_cancelled', 'renewal_collected'])
            ->where('id', '!=', $renewal->id)
            ->first();

        if ($existingRenewal) {
            return back()->with('error', 'You already have a pending renewal for this registration number.');
        }

        // Lookup original registration application
        $originalApp = Application::where('application_type', 'registration')
            ->where(function($q) use ($number) {
                $q->where('reference', $number)
                  ->orWhere('registration_number', $number);
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
            'status' => $originalApp ? 'record_found_displayed' : 'record_not_found',
            'current_stage' => $originalApp ? 'confirmation' : 'lookup',
            'last_action_at' => now(),
        ]);

        // Log attempt
        ActivityLogger::log('mediahouse_registration_lookup', $renewal, $renewal->status, $renewal->status, [
            'lookup_result' => $originalApp ? 'found' : 'not_found',
        ]);

        if (!$originalApp) {
            return back()->with('error', 'Registration number not found. Please verify your number and try again.');
        }

        return redirect()->route('mediahouse.renewals.confirm', $renewal);
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
            return redirect()->route('mediahouse.renewals.lookup', $renewal)
                ->with('error', 'Please complete the registration number lookup first.');
        }

        $renewal->load('originalApplication.applicant');

        return view('portal.mediahouse.renewals.confirm', compact('renewal'));
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

        // Must have found record
        if ($renewal->lookup_status !== 'found') {
            return response()->json([
                'ok' => false,
                'message' => 'Please complete the registration number lookup first.'
            ], 400);
        }

        $renewal->update([
            'has_changes' => false,
            'confirmation_type' => 'no_changes',
            'confirmed_at' => now(),
            'status' => 'renewal_confirmed_no_changes',
            'current_stage' => 'payment',
            'last_action_at' => now(),
        ]);

        ActivityLogger::log('mediahouse_renewal_confirmed_no_changes', $renewal, null, 'renewal_confirmed_no_changes', []);

        return redirect()->route('mediahouse.renewals.payment', $renewal);
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

        // Must have found record
        if ($renewal->lookup_status !== 'found') {
            return back()->with('error', 'Please complete the registration number lookup first.');
        }

        $data = $request->validate([
            'changes' => ['required', 'array', 'min:1'],
            'changes.*.field_name' => ['required', 'string'],
            'changes.*.old_value' => ['required', 'string'],
            'changes.*.new_value' => ['required', 'string'],
            'changes.*.supporting_document' => ['nullable', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png'],
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
                'status' => 'renewal_confirmed_with_changes',
                'current_stage' => 'payment',
                'last_action_at' => now(),
            ]);
        });

        ActivityLogger::log('mediahouse_renewal_changes_submitted', $renewal, null, 'renewal_confirmed_with_changes', [
            'change_count' => count($data['changes']),
        ]);

        return redirect()->route('mediahouse.renewals.payment', $renewal);
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
            'renewal_confirmed_no_changes',
            'renewal_confirmed_with_changes',
        ])) {
            return redirect()->route('mediahouse.renewals.confirm', $renewal)
                ->with('error', 'Please confirm your information first.');
        }

        return view('portal.mediahouse.renewals.payment', compact('renewal'));
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

        // Must be confirmed
        if (!in_array($renewal->status, [
            'renewal_confirmed_no_changes',
            'renewal_confirmed_with_changes',
        ])) {
            return response()->json([
                'ok' => false,
                'message' => 'Please confirm your information first.'
            ], 400);
        }

        $data = $request->validate([
            'reference' => ['required', 'string', 'max:255'],
        ]);

        $renewal->update([
            'payment_method' => 'PAYNOW',
            'payment_reference' => $data['reference'],
            'payment_submitted_at' => now(),
            'status' => 'renewal_submitted_awaiting_accounts_verification',
            'current_stage' => 'accounts_verification',
            'last_action_at' => now(),
        ]);

        ActivityLogger::log('mediahouse_renewal_payment_paynow_submitted', $renewal, null, 'renewal_submitted_awaiting_accounts_verification', [
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

        // Must be confirmed
        if (!in_array($renewal->status, [
            'renewal_confirmed_no_changes',
            'renewal_confirmed_with_changes',
        ])) {
            return response()->json([
                'ok' => false,
                'message' => 'Please confirm your information first.'
            ], 400);
        }

        $data = $request->validate([
            'payment_date' => ['required', 'date'],
            'amount' => ['nullable', 'numeric', 'min:0'],
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
            'payment_amount' => $data['amount'] ?? null,
            'payment_date' => $data['payment_date'],
            'payment_proof_path' => $path,
            'payment_metadata' => [
                'payer_name' => $data['payer_name'] ?? null,
                'file_name' => $file->getClientOriginalName(),
                'file_hash' => $hash,
            ],
            'payment_submitted_at' => now(),
            'status' => 'renewal_submitted_awaiting_accounts_verification',
            'current_stage' => 'accounts_verification',
            'last_action_at' => now(),
        ]);

        ActivityLogger::log('mediahouse_renewal_payment_proof_submitted', $renewal, null, 'renewal_submitted_awaiting_accounts_verification', [
            'amount' => $data['amount'] ?? null,
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

        return view('portal.mediahouse.renewals.show', compact('renewal'));
    }
}

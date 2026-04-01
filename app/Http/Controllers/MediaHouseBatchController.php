<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\User;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MediaHouseBatchController extends Controller
{
    /**
     * Preview selected practitioners and initiate a batch.
     */
    public function initiate(Request $request)
    {
        $journalistIds = $request->input('journalist_ids', []);
        if (empty($journalistIds)) {
            return back()->with('error', 'Please select at least one media practitioner.');
        }

        $journalists = User::whereIn('id', $journalistIds)->get();
        
        // Calculate total amount (Placeholder: 20 USD per practitioner for now if not set)
        $rate = 20; 
        $total = count($journalistIds) * $rate;

        return view('portal.mediahouse.batches.preview', compact('journalists', 'total', 'journalistIds'));
    }

    /**
     * Create the batch and redirect to payment.
     */
    public function store(Request $request)
    {
        $journalistIds = $request->input('journalist_ids', []);
        $total = $request->input('total');
        $user = Auth::user();

        $reference = 'BATCH-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));

        $batch = DB::transaction(function () use ($reference, $journalistIds, $user, $total) {
            $batch = Batch::create([
                'reference' => $reference,
                'media_house_user_id' => $user->id,
                'amount' => $total,
                'status' => 'pending',
                'metadata' => [
                    'journalist_ids' => $journalistIds,
                ],
            ]);

            foreach ($journalistIds as $journalistId) {
                $journalist = User::find($journalistId);
                if (!$journalist) continue;

                Application::create([
                    'applicant_user_id' => $journalistId,
                    'media_house_user_id' => $user->id,
                    'batch_id' => $batch->id,
                    'type' => 'renewal',
                    'category' => 'Local Journalist',
                    'status' => Application::AWAITING_BATCH_PAYMENT,
                    'form_data' => [
                        'name' => $journalist->name,
                        'email' => $journalist->email,
                        'employer_name' => $user->name,
                    ],
                ]);
            }

            return $batch;
        });

        return redirect()->route('mediahouse.batch.show', $batch);

    }

    /**
     * Show batch details and payment options.
     */
    public function show(Batch $batch)
    {
        abort_unless($batch->media_house_user_id === Auth::id(), 403);
        
        $journalists = User::whereIn('id', $batch->metadata['journalist_ids'] ?? [])->get();

        return view('portal.mediahouse.batches.show', compact('batch', 'journalists'));
    }

    /**
     * Handle POP upload for the batch.
     */
    public function submitPayment(Request $request, Batch $batch)
    {
        abort_unless($batch->media_house_user_id === Auth::id(), 403);

        $request->validate([
            'payment_method' => 'required|in:proof,paynow',
            'proof_file' => 'required_if:payment_method,proof|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->input('payment_method') === 'proof') {
            $path = $request->file('proof_file')->store('payment_proofs/batches', 'public');
            $batch->update([
                'payment_method' => 'proof',
                'proof_path' => $path,
                'status' => 'pending_verification',
            ]);
            
            return redirect()->route('mediahouse.portal')->with('success', 'Batch payment proof uploaded and awaiting verification.');
        }

        // PayNow logic would go here
        return back()->with('info', 'PayNow integration for batches is coming soon. Please use Proof of Payment for now.');
    }

    /**
     * List all batches for the media house.
     */
    public function index()
    {
        $batches = Batch::where('media_house_user_id', Auth::id())
            ->latest()
            ->paginate(20);

        return view('portal.mediahouse.batches.index', compact('batches'));
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration maps existing status values to new workflow enforcement statuses.
     * It's designed to be idempotent and can be run multiple times safely.
     */
    public function up(): void
    {
        // Status mapping from old to new
        $statusMappings = [
            // Old status => New status
            'submitted' => 'submitted_to_accreditation_officer',
            'officer_review' => 'submitted_to_accreditation_officer',
            'officer_approved' => 'approved_by_accreditation_officer_awaiting_payment',
            'registrar_review' => 'approved_by_accreditation_officer_awaiting_payment',
            'accounts_review' => 'awaiting_accounts_verification',
            'returned_to_accounts' => 'awaiting_accounts_verification',
            'paid_confirmed' => 'payment_verified',
            'returned_to_officer' => 'registrar_raised_fix_request',
            'forwarded_to_registrar_no_approval' => 'forwarded_to_registrar_no_approval', // Keep same
            'pending_accounts_review_from_registrar' => 'pending_accounts_review_from_registrar_special',
            'registrar_approved_pending_reg_fee' => 'registrar_approved_pending_registration_fee_payment',
            'reg_fee_submitted_awaiting_verification' => 'reg_fee_submitted_awaiting_verification', // Keep same
            'payment_verified' => 'payment_verified', // Keep same
            'payment_rejected' => 'payment_rejected', // Keep same
            'production_queue' => 'production_queue', // Keep same
            'card_generated' => 'card_generated', // Keep same
            'certificate_generated' => 'certificate_generated', // Keep same
            'printed' => 'printed', // Keep same
            'issued' => 'issued', // Keep same
            'correction_requested' => 'correction_requested', // Keep same
            'officer_rejected' => 'officer_rejected', // Keep same
            'registrar_rejected' => 'registrar_rejected', // Keep same
            'withdrawn' => 'withdrawn', // Keep same
            'draft' => 'draft', // Keep same
        ];

        // Update statuses in batches
        foreach ($statusMappings as $oldStatus => $newStatus) {
            if ($oldStatus !== $newStatus) {
                DB::table('applications')
                    ->where('status', $oldStatus)
                    ->update([
                        'status' => $newStatus,
                        'updated_at' => now(),
                    ]);
                
                $count = DB::table('applications')->where('status', $newStatus)->count();
                echo "Migrated '{$oldStatus}' to '{$newStatus}': {$count} records\n";
            }
        }

        // Log the migration
        if (Schema::hasTable('activity_logs')) {
            try {
                DB::table('activity_logs')->insert([
                    'action' => 'system_status_migration',
                    'user_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Silently fail if activity_logs structure is different
                echo "Note: Could not log to activity_logs table\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     * 
     * This reverses the status migration back to old values.
     */
    public function down(): void
    {
        // Reverse mapping
        $reverseMappings = [
            'submitted_to_accreditation_officer' => 'submitted',
            'approved_by_accreditation_officer_awaiting_payment' => 'officer_approved',
            'awaiting_accounts_verification' => 'accounts_review',
            'registrar_raised_fix_request' => 'returned_to_officer',
            'pending_accounts_review_from_registrar_special' => 'pending_accounts_review_from_registrar',
            'registrar_approved_pending_registration_fee_payment' => 'registrar_approved_pending_reg_fee',
        ];

        foreach ($reverseMappings as $newStatus => $oldStatus) {
            DB::table('applications')
                ->where('status', $newStatus)
                ->update([
                    'status' => $oldStatus,
                    'updated_at' => now(),
                ]);
        }
    }
};

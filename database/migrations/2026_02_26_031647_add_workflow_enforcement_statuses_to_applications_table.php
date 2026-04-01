<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This migration adds support for new workflow enforcement status constants
        // The status column already exists, we just need to ensure it can hold the new values
        
        // Add any missing columns for workflow enforcement
        Schema::table('applications', function (Blueprint $table) {
            // Add forward_no_approval_reason if not exists
            if (!Schema::hasColumn('applications', 'forward_no_approval_reason')) {
                $table->text('forward_no_approval_reason')->nullable()->after('decision_notes');
            }
            
            // Add official_letter_id if not exists
            if (!Schema::hasColumn('applications', 'official_letter_id')) {
                $table->unsignedBigInteger('official_letter_id')->nullable()->after('decision_notes');
                $table->foreign('official_letter_id')->references('id')->on('official_letters')->onDelete('set null');
            }
        });

        // Note: The status column already exists and can hold any string value
        // The new status constants are:
        // - submitted_to_accreditation_officer
        // - approved_by_accreditation_officer_awaiting_payment
        // - awaiting_accounts_verification
        // - registrar_raised_fix_request
        // - pending_accounts_review_from_registrar_special
        // - registrar_approved_pending_registration_fee_payment
        // - produced_ready_for_collection
        
        // These are enforced at the application level via StatusTransitionValidator
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'forward_no_approval_reason')) {
                $table->dropColumn('forward_no_approval_reason');
            }
            
            if (Schema::hasColumn('applications', 'official_letter_id')) {
                $table->dropForeign(['official_letter_id']);
                $table->dropColumn('official_letter_id');
            }
        });
    }
};

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
        Schema::table('applications', function (Blueprint $table) {
            if (!Schema::hasColumn('applications', 'payment_submission_method')) {
                $table->enum('payment_submission_method', ['paynow_reference', 'proof_upload', 'waiver'])
                    ->nullable()
                    ->after('payment_status')
                    ->comment('How applicant submitted payment: paynow_reference, proof_upload, or waiver');
            }

            if (!Schema::hasColumn('applications', 'payment_submitted_at')) {
                $table->timestamp('payment_submitted_at')
                    ->nullable()
                    ->after('payment_submission_method')
                    ->comment('When applicant submitted payment info');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'payment_submission_method')) {
                $table->dropColumn('payment_submission_method');
            }
            if (Schema::hasColumn('applications', 'payment_submitted_at')) {
                $table->dropColumn('payment_submitted_at');
            }
        });
    }
};

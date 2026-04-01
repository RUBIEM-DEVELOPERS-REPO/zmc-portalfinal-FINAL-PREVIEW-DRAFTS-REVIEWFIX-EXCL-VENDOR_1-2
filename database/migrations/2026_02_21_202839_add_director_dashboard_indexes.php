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
        // Add indexes on applications table for director dashboard queries
        Schema::table('applications', function (Blueprint $table) {
            $table->index('status', 'idx_applications_status');
            $table->index('application_type', 'idx_applications_type');
            $table->index('accreditation_category_code', 'idx_applications_category');
            $table->index('residency_type', 'idx_applications_residency');
            // Removed: media_house_id index (column doesn't exist)
            $table->index(['status', 'issued_at'], 'idx_applications_status_issued');
            $table->index(['status', 'submitted_at'], 'idx_applications_status_submitted');
        });

        // Add indexes on payments table for financial analytics
        Schema::table('payments', function (Blueprint $table) {
            $table->index('status', 'idx_payments_status');
            $table->index('service_type', 'idx_payments_service_type');
            $table->index('applicant_category', 'idx_payments_applicant_category');
            $table->index('payment_method', 'idx_payments_method');
            $table->index(['status', 'confirmed_at'], 'idx_payments_status_confirmed');
        });

        // Add indexes on activity_logs table for compliance monitoring
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index('action', 'idx_activity_logs_action');
            $table->index('user_id', 'idx_activity_logs_user');
            $table->index('created_at', 'idx_activity_logs_created');
            $table->index(['action', 'created_at'], 'idx_activity_logs_action_created');
        });

        // Add indexes on print_logs table for issuance oversight
        Schema::table('print_logs', function (Blueprint $table) {
            $table->index('application_id', 'idx_print_logs_application');
            $table->index('print_type', 'idx_print_logs_type');
            $table->index('printed_at', 'idx_print_logs_printed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex('idx_applications_status');
            $table->dropIndex('idx_applications_type');
            $table->dropIndex('idx_applications_category');
            $table->dropIndex('idx_applications_residency');
            // Removed: media_house_id index (column doesn't exist)
            $table->dropIndex('idx_applications_status_issued');
            $table->dropIndex('idx_applications_status_submitted');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('idx_payments_status');
            $table->dropIndex('idx_payments_service_type');
            $table->dropIndex('idx_payments_applicant_category');
            $table->dropIndex('idx_payments_method');
            $table->dropIndex('idx_payments_status_confirmed');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex('idx_activity_logs_action');
            $table->dropIndex('idx_activity_logs_user');
            $table->dropIndex('idx_activity_logs_created');
            $table->dropIndex('idx_activity_logs_action_created');
        });

        Schema::table('print_logs', function (Blueprint $table) {
            $table->dropIndex('idx_print_logs_application');
            $table->dropIndex('idx_print_logs_type');
            $table->dropIndex('idx_print_logs_printed');
        });
    }
};

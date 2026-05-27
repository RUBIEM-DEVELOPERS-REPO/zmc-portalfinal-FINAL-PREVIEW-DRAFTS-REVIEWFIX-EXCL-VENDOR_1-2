<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (!Schema::hasColumn('applications', 'payment_stage')) {
                $table->string('payment_stage')->nullable();
            }
            if (!Schema::hasColumn('applications', 'forward_reason')) {
                $table->text('forward_reason')->nullable();
            }
            if (!Schema::hasColumn('applications', 'registrar_letter_path')) {
                $table->string('registrar_letter_path')->nullable();
            }
            if (!Schema::hasColumn('applications', 'receipt_number')) {
                $table->string('receipt_number')->nullable();
            }
            if (!Schema::hasColumn('applications', 'paynow_ref_submitted')) {
                $table->string('paynow_ref_submitted')->nullable();
            }
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE applications DROP CONSTRAINT IF EXISTS applications_status_check');
            DB::statement("ALTER TABLE applications ADD CONSTRAINT applications_status_check CHECK (status::text = ANY(ARRAY[
                'draft', 'submitted', 'withdrawn',
                'officer_review', 'officer_approved', 'officer_rejected',
                'correction_requested', 'returned_to_applicant',
                'approved_awaiting_payment', 'forwarded_to_registrar', 'registrar_fix_request',
                'registrar_review', 'registrar_approved', 'registrar_rejected',
                'returned_to_officer', 'pending_accounts_from_registrar', 'registrar_approved_pending_reg_fee',
                'accounts_review', 'awaiting_accounts_verification',
                'payment_verified', 'payment_rejected',
                'paid_confirmed', 'returned_to_accounts',
                'submitted_with_app_fee', 'verified_by_officer',
                'approved_pending_payment', 'paid', 'returned_from_payments', 'returned_from_registrar', 'rejected',
                'needs_correction',
                'production_queue', 'produced_ready', 'card_generated', 'certificate_generated', 'printed', 'issued'
            ]::text[]))");
        }

        if (!Schema::hasTable('reminders')) {
            Schema::create('reminders', function (Blueprint $table) {
                $table->id();
                $table->string('target_type');
                $table->unsignedBigInteger('target_id');
                $table->string('reminder_type')->default('general');
                $table->text('message');
                $table->unsignedBigInteger('created_by');
                $table->timestamp('acknowledged_at')->nullable();
                $table->unsignedBigInteger('acknowledged_by')->nullable();
                $table->timestamps();
                $table->index(['target_type', 'target_id']);
            });
        }

        if (!Schema::hasTable('card_templates')) {
            Schema::create('card_templates', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('type');
                $table->string('year', 4);
                $table->string('background_path')->nullable();
                $table->json('layout_config')->nullable();
                $table->boolean('is_active')->default(false);
                $table->unsignedBigInteger('created_by');
                $table->timestamps();
            });
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'id_number')) {
                $table->string('id_number')->nullable();
            }
            if (!Schema::hasColumn('users', 'passport_number')) {
                $table->string('passport_number')->nullable();
            }
            if (!Schema::hasColumn('users', 'phone2')) {
                $table->string('phone2')->nullable();
            }
            if (!Schema::hasColumn('users', 'social_media')) {
                $table->json('social_media')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
        Schema::dropIfExists('card_templates');
    }
};

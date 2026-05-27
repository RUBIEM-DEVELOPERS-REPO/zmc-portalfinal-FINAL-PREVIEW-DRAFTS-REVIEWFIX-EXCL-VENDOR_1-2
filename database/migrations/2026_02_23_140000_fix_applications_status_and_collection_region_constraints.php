<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE applications DROP CONSTRAINT IF EXISTS applications_status_check');
            DB::statement("ALTER TABLE applications ADD CONSTRAINT applications_status_check CHECK (status::text = ANY(ARRAY[
                'draft', 'submitted', 'withdrawn', 'needs_correction',
                'officer_review', 'officer_approved', 'officer_rejected', 'correction_requested',
                'registrar_review', 'registrar_approved', 'registrar_rejected', 'returned_to_officer',
                'accounts_review', 'paid_confirmed', 'returned_to_accounts',
                'approved_pending_payment', 'paid', 'returned_from_payments', 'returned_from_registrar', 'rejected',
                'production_queue', 'card_generated', 'certificate_generated', 'printed', 'issued'
            ]::text[]))");

            DB::statement('ALTER TABLE applications DROP CONSTRAINT IF EXISTS applications_collection_region_check');
            DB::statement("ALTER TABLE applications ADD CONSTRAINT applications_collection_region_check CHECK (collection_region IS NULL OR collection_region::text = ANY(ARRAY[
                'harare', 'bulawayo', 'mutare', 'masvingo', 'gweru', 'chinhoyi'
            ]::text[]))");

            DB::statement("ALTER TABLE applications ALTER COLUMN status SET DEFAULT 'draft'");
            DB::statement("ALTER TABLE applications ALTER COLUMN collection_region DROP NOT NULL");

            DB::statement('ALTER TABLE application_documents DROP CONSTRAINT IF EXISTS application_documents_status_check');
            DB::statement("ALTER TABLE application_documents ADD CONSTRAINT application_documents_status_check CHECK (status::text = ANY(ARRAY[
                'pending', 'accepted', 'rejected', 'draft', 'uploaded'
            ]::text[]))");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE applications DROP CONSTRAINT IF EXISTS applications_status_check');
            DB::statement("ALTER TABLE applications ADD CONSTRAINT applications_status_check CHECK (status::text = ANY(ARRAY[
                'submitted', 'needs_correction', 'rejected',
                'approved_pending_payment', 'paid', 'returned_from_payments', 'returned_from_registrar'
            ]::text[]))");
        }
    }
};

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DeployMigrate extends Command
{
    protected $signature = 'db:deploy-migrate';
    protected $description = 'Idempotent deployment migration - works regardless of migration table state';

    public function handle(): int
    {
        $this->info('==> Deploy migration starting...');

        try {
            $this->ensureMigrationsTable();
            $this->applyIdempotentSchema();
            $this->markAllMigrationsRun();
            $this->info('==> Deploy migration complete!');
            return 0;
        } catch (\Throwable $e) {
            $this->error('Deploy migration failed: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }

    private function ensureMigrationsTable(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement("CREATE TABLE IF NOT EXISTS migrations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                migration VARCHAR(255) NOT NULL,
                batch INTEGER NOT NULL
            )");
        } else {
            DB::statement("CREATE TABLE IF NOT EXISTS migrations (
                id SERIAL PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                batch INTEGER NOT NULL
            )");
        }
        $this->info('  - migrations table ensured');
    }

    private function markAllMigrationsRun(): void
    {
        $migrationFiles = collect(scandir(database_path('migrations')))
            ->filter(fn($f) => str_ends_with($f, '.php'))
            ->map(fn($f) => str_replace('.php', '', $f))
            ->sort()
            ->values();

        $existing = DB::table('migrations')->pluck('migration')->toArray();
        $added = 0;

        foreach ($migrationFiles as $migration) {
            if (!in_array($migration, $existing)) {
                DB::table('migrations')->insert([
                    'migration' => $migration,
                    'batch' => 1,
                ]);
                $added++;
            }
        }

        $this->info("  - Marked {$added} migrations as run (" . count($existing) . " already recorded)");
    }

    private function applyIdempotentSchema(): void
    {
        $this->info('  - Applying idempotent schema...');

        $isSqlite = DB::getDriverName() === 'sqlite';

        $idType = $isSqlite ? 'INTEGER PRIMARY KEY AUTOINCREMENT' : 'BIGSERIAL PRIMARY KEY';
        $jsonType = $isSqlite ? 'TEXT' : 'JSONB';

        DB::statement("CREATE TABLE IF NOT EXISTS users (
            id {$idType},
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            email_verified_at TIMESTAMP NULL,
            password VARCHAR(255) NOT NULL,
            remember_token VARCHAR(100) NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS password_reset_tokens (
            email VARCHAR(255) PRIMARY KEY,
            token VARCHAR(255) NOT NULL,
            created_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS sessions (
            id VARCHAR(255) PRIMARY KEY,
            user_id BIGINT NULL,
            ip_address VARCHAR(45) NULL,
            user_agent TEXT NULL,
            payload TEXT NOT NULL,
            last_activity INTEGER NOT NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS cache (
            key VARCHAR(255) PRIMARY KEY,
            value TEXT NOT NULL,
            expiration INTEGER NOT NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS cache_locks (
            key VARCHAR(255) PRIMARY KEY,
            owner VARCHAR(255) NOT NULL,
            expiration INTEGER NOT NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS jobs (
            id {$idType},
            queue VARCHAR(255) NOT NULL,
            payload TEXT NOT NULL,
            attempts SMALLINT NOT NULL DEFAULT 0,
            reserved_at INTEGER NULL,
            available_at INTEGER NOT NULL,
            created_at INTEGER NOT NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS job_batches (
            id VARCHAR(255) PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            total_jobs INTEGER NOT NULL DEFAULT 0,
            pending_jobs INTEGER NOT NULL DEFAULT 0,
            failed_jobs INTEGER NOT NULL DEFAULT 0,
            failed_job_ids TEXT NOT NULL DEFAULT '',
            options TEXT NULL,
            cancelled_at INTEGER NULL,
            created_at INTEGER NOT NULL,
            finished_at INTEGER NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS failed_jobs (
            id {$idType},
            uuid VARCHAR(255) NOT NULL UNIQUE,
            connection TEXT NOT NULL,
            queue TEXT NOT NULL,
            payload TEXT NOT NULL,
            exception TEXT NOT NULL,
            failed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS permissions (
            id {$idType},
            name VARCHAR(255) NOT NULL,
            guard_name VARCHAR(255) NOT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS roles (
            id {$idType},
            name VARCHAR(255) NOT NULL,
            guard_name VARCHAR(255) NOT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS model_has_permissions (
            permission_id BIGINT NOT NULL,
            model_type VARCHAR(255) NOT NULL,
            model_id BIGINT NOT NULL,
            PRIMARY KEY (permission_id, model_id, model_type)
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS model_has_roles (
            role_id BIGINT NOT NULL,
            model_type VARCHAR(255) NOT NULL,
            model_id BIGINT NOT NULL,
            PRIMARY KEY (role_id, model_id, model_type)
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS role_has_permissions (
            permission_id BIGINT NOT NULL,
            role_id BIGINT NOT NULL,
            PRIMARY KEY (permission_id, role_id)
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS applications (
            id {$idType},
            reference VARCHAR(255) NULL UNIQUE,
            applicant_user_id BIGINT NULL,
            application_type VARCHAR(50) NULL,
            request_type VARCHAR(50) NULL DEFAULT 'new',
            journalist_scope VARCHAR(20) NULL,
            collection_region VARCHAR(100) NULL,
            form_data {$jsonType} NULL,
            is_draft BOOLEAN NOT NULL DEFAULT true,
            submitted_at TIMESTAMP NULL,
            status VARCHAR(50) NOT NULL DEFAULT 'draft',
            current_stage VARCHAR(50) NULL,
            last_action_at TIMESTAMP NULL,
            last_action_by BIGINT NULL,
            correction_notes TEXT NULL,
            rejection_reason TEXT NULL,
            id_verification_status VARCHAR(50) NULL,
            assigned_officer_id BIGINT NULL,
            assigned_at TIMESTAMP NULL,
            approved_at TIMESTAMP NULL,
            rejected_at TIMESTAMP NULL,
            decision_notes TEXT NULL,
            paynow_reference VARCHAR(255) NULL,
            waiver_path VARCHAR(500) NULL,
            payment_status VARCHAR(50) NULL,
            payment_proof_path VARCHAR(500) NULL,
            payment_proof_uploaded_at TIMESTAMP NULL,
            waiver_status VARCHAR(50) NULL,
            waiver_reviewed_by BIGINT NULL,
            waiver_reviewed_at TIMESTAMP NULL,
            waiver_review_notes TEXT NULL,
            proof_status VARCHAR(50) NULL,
            proof_reviewed_by BIGINT NULL,
            proof_reviewed_at TIMESTAMP NULL,
            proof_review_notes TEXT NULL,
            paynow_poll_url VARCHAR(500) NULL,
            paynow_confirmed_at TIMESTAMP NULL,
            paynow_webhook_last_hash VARCHAR(255) NULL,
            proof_payer_first_name VARCHAR(100) NULL,
            proof_payer_last_name VARCHAR(100) NULL,
            proof_payment_date DATE NULL,
            proof_amount_paid DECIMAL(12,2) NULL,
            proof_bank_name VARCHAR(120) NULL,
            proof_original_name VARCHAR(255) NULL,
            proof_mime VARCHAR(100) NULL,
            proof_file_hash VARCHAR(255) NULL,
            waiver_beneficiary_first_name VARCHAR(100) NULL,
            waiver_beneficiary_last_name VARCHAR(100) NULL,
            waiver_offered_date DATE NULL,
            waiver_offered_by_name VARCHAR(150) NULL,
            waiver_original_name VARCHAR(255) NULL,
            waiver_mime VARCHAR(100) NULL,
            waiver_file_hash VARCHAR(255) NULL,
            registrar_reviewed_at TIMESTAMP NULL,
            registrar_reviewed_by BIGINT NULL,
            residency_type VARCHAR(50) NULL,
            accreditation_category_code VARCHAR(10) NULL,
            media_house_category_code VARCHAR(10) NULL,
            locked_by BIGINT NULL,
            locked_at TIMESTAMP NULL,
            printed_by BIGINT NULL,
            printed_at TIMESTAMP NULL,
            issued_by BIGINT NULL,
            issued_at TIMESTAMP NULL,
            payment_stage VARCHAR(50) NULL,
            forward_reason TEXT NULL,
            registrar_letter_path VARCHAR(500) NULL,
            receipt_number VARCHAR(100) NULL,
            paynow_ref_submitted VARCHAR(100) NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS application_documents (
            id {$idType},
            application_id BIGINT NULL,
            doc_type VARCHAR(50) NULL,
            document_type VARCHAR(100) NULL,
            file_path VARCHAR(500) NULL,
            original_name VARCHAR(255) NULL,
            status VARCHAR(50) NULL DEFAULT 'pending',
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS application_messages (
            id {$idType},
            application_id BIGINT NULL,
            sender_id BIGINT NULL,
            message TEXT NULL,
            read_at TIMESTAMP NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS officer_regions (
            id {$idType},
            user_id BIGINT NULL,
            region VARCHAR(100) NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS activity_logs (
            id {$idType},
            application_id BIGINT NULL,
            user_id BIGINT NULL,
            action VARCHAR(100) NULL,
            from_status VARCHAR(50) NULL,
            to_status VARCHAR(50) NULL,
            notes TEXT NULL,
            metadata {$jsonType} NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS payments (
            id {$idType},
            application_id BIGINT NULL,
            user_id BIGINT NULL,
            amount DECIMAL(12,2) NULL,
            currency VARCHAR(10) NULL DEFAULT 'USD',
            method VARCHAR(50) NULL,
            status VARCHAR(50) NULL DEFAULT 'pending',
            reference VARCHAR(255) NULL,
            paynow_reference VARCHAR(255) NULL,
            poll_url VARCHAR(500) NULL,
            metadata {$jsonType} NULL,
            paid_at TIMESTAMP NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            payment_type VARCHAR(50) NULL,
            receipt_number VARCHAR(100) NULL,
            recorded_by BIGINT NULL,
            void_reason TEXT NULL,
            voided_by BIGINT NULL,
            voided_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS audit_trails (
            id {$idType},
            user_id BIGINT NULL,
            action VARCHAR(255) NULL,
            model_type VARCHAR(255) NULL,
            model_id BIGINT NULL,
            old_values {$jsonType} NULL,
            new_values {$jsonType} NULL,
            ip_address VARCHAR(45) NULL,
            user_agent TEXT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS audit_logs (
            id {$idType},
            user_id BIGINT NULL,
            action VARCHAR(100) NULL,
            entity_type VARCHAR(100) NULL,
            entity_id BIGINT NULL,
            details {$jsonType} NULL,
            ip_address VARCHAR(45) NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS notices (
            id {$idType},
            title VARCHAR(255) NOT NULL,
            body TEXT NULL,
            image_path VARCHAR(500) NULL,
            target_portal VARCHAR(50) NULL DEFAULT 'both',
            is_published BOOLEAN NOT NULL DEFAULT false,
            published_at TIMESTAMP NULL,
            created_by BIGINT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS events (
            id {$idType},
            title VARCHAR(255) NOT NULL,
            description TEXT NULL,
            image_path VARCHAR(500) NULL,
            starts_at TIMESTAMP NULL,
            ends_at TIMESTAMP NULL,
            location VARCHAR(255) NULL,
            target_portal VARCHAR(50) NULL DEFAULT 'both',
            is_published BOOLEAN NOT NULL DEFAULT false,
            published_at TIMESTAMP NULL,
            created_by BIGINT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS notifications (
            id UUID PRIMARY KEY,
            type VARCHAR(255) NOT NULL,
            notifiable_type VARCHAR(255) NOT NULL,
            notifiable_id BIGINT NOT NULL,
            data TEXT NOT NULL,
            read_at TIMESTAMP NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS news (
            id {$idType},
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            body TEXT NULL,
            is_published BOOLEAN NOT NULL DEFAULT false,
            published_at TIMESTAMP NULL,
            created_by BIGINT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS complaints (
            id {$idType},
            reference VARCHAR(255) NULL UNIQUE,
            complainant_user_id BIGINT NULL,
            respondent_media_house VARCHAR(255) NULL,
            category VARCHAR(100) NULL,
            description TEXT NULL,
            status VARCHAR(50) NOT NULL DEFAULT 'submitted',
            assigned_officer_id BIGINT NULL,
            resolution TEXT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS accreditation_records (
            id {$idType},
            user_id BIGINT NULL,
            application_id BIGINT NULL,
            accreditation_number VARCHAR(255) NULL UNIQUE,
            category VARCHAR(100) NULL,
            scope VARCHAR(50) NULL,
            status VARCHAR(50) NOT NULL DEFAULT 'active',
            issued_at TIMESTAMP NULL,
            expires_at TIMESTAMP NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS system_configs (
            id {$idType},
            key VARCHAR(255) NOT NULL UNIQUE,
            value TEXT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS registration_records (
            id {$idType},
            user_id BIGINT NULL,
            application_id BIGINT NULL,
            registration_number VARCHAR(255) NULL UNIQUE,
            media_type VARCHAR(100) NULL,
            status VARCHAR(50) NOT NULL DEFAULT 'active',
            issued_at TIMESTAMP NULL,
            expires_at TIMESTAMP NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS officer_followups (
            id {$idType},
            application_id BIGINT NULL,
            officer_id BIGINT NULL,
            message TEXT NULL,
            response TEXT NULL,
            responded_at TIMESTAMP NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS compliance_cases (
            id {$idType},
            reference VARCHAR(255) NULL UNIQUE,
            media_house_id BIGINT NULL,
            officer_id BIGINT NULL,
            category VARCHAR(100) NULL,
            description TEXT NULL,
            status VARCHAR(50) NOT NULL DEFAULT 'open',
            resolution TEXT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS compliance_violations (
            id {$idType},
            case_id BIGINT NULL,
            violation_type VARCHAR(100) NULL,
            description TEXT NULL,
            severity VARCHAR(50) NULL DEFAULT 'minor',
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS compliance_evidence_files (
            id {$idType},
            case_id BIGINT NULL,
            file_path VARCHAR(500) NULL,
            original_name VARCHAR(255) NULL,
            uploaded_by BIGINT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS audit_flags (
            id {$idType},
            flagged_by BIGINT NULL,
            entity_type VARCHAR(100) NULL,
            entity_id BIGINT NULL,
            flag_type VARCHAR(50) NULL,
            reason TEXT NULL,
            status VARCHAR(50) NOT NULL DEFAULT 'open',
            resolved_by BIGINT NULL,
            resolved_at TIMESTAMP NULL,
            resolution_notes TEXT NULL,
            priority VARCHAR(20) NULL DEFAULT 'medium',
            metadata {$jsonType} NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS media_house_staff (
            id {$idType},
            media_house_user_id BIGINT NULL,
            journalist_user_id BIGINT NULL,
            role VARCHAR(100) NULL DEFAULT 'staff',
            status VARCHAR(50) NULL DEFAULT 'active',
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS regions (
            id {$idType},
            name VARCHAR(255) NOT NULL,
            code VARCHAR(10) NULL UNIQUE,
            is_active BOOLEAN NOT NULL DEFAULT true,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS vacancies (
            id {$idType},
            title VARCHAR(255) NOT NULL,
            description TEXT NULL,
            department VARCHAR(100) NULL,
            location VARCHAR(100) NULL,
            closing_date DATE NULL,
            is_published BOOLEAN NOT NULL DEFAULT false,
            created_by BIGINT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS tenders (
            id {$idType},
            title VARCHAR(255) NOT NULL,
            reference_number VARCHAR(100) NULL,
            description TEXT NULL,
            closing_date DATE NULL,
            is_published BOOLEAN NOT NULL DEFAULT false,
            created_by BIGINT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS document_versions (
            id {$idType},
            application_id BIGINT NULL,
            document_id BIGINT NULL,
            version_number INTEGER NOT NULL DEFAULT 1,
            file_path VARCHAR(500) NULL,
            original_name VARCHAR(255) NULL,
            uploaded_by BIGINT NULL,
            notes TEXT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS print_logs (
            id {$idType},
            application_id BIGINT NULL,
            type VARCHAR(50) NULL,
            template_version VARCHAR(50) NULL,
            generated_by BIGINT NULL,
            printed_by BIGINT NULL,
            print_count INTEGER NOT NULL DEFAULT 0,
            generated_at TIMESTAMP NULL,
            printed_at TIMESTAMP NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS payment_audit_logs (
            id {$idType},
            payment_id BIGINT NULL,
            action VARCHAR(100) NULL,
            performed_by BIGINT NULL,
            details {$jsonType} NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS receipt_sequences (
            id {$idType},
            prefix VARCHAR(20) NOT NULL,
            year INTEGER NOT NULL,
            last_number INTEGER NOT NULL DEFAULT 0,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS card_templates (
            id {$idType},
            name VARCHAR(255) NOT NULL,
            type VARCHAR(50) NOT NULL DEFAULT 'card',
            year INTEGER NULL,
            background_path VARCHAR(500) NULL,
            layout_config {$jsonType} NULL,
            is_active BOOLEAN NOT NULL DEFAULT false,
            created_by BIGINT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS reminders (
            id {$idType},
            target_type VARCHAR(50) NOT NULL,
            target_id BIGINT NOT NULL,
            message TEXT NOT NULL,
            type VARCHAR(50) NOT NULL DEFAULT 'general',
            acknowledged_at TIMESTAMP NULL,
            created_by BIGINT NOT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS files (
            id {$idType},
            name VARCHAR(255) NULL,
            path VARCHAR(500) NULL,
            mime_type VARCHAR(100) NULL,
            size BIGINT NULL,
            uploaded_by BIGINT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS refunds (
            id {$idType},
            payment_id BIGINT NULL,
            amount DECIMAL(12,2) NULL,
            reason TEXT NULL,
            status VARCHAR(50) NULL DEFAULT 'pending',
            processed_by BIGINT NULL,
            processed_at TIMESTAMP NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS unaccredited_reports (
            id {$idType},
            reported_by BIGINT NULL,
            name VARCHAR(255) NULL,
            description TEXT NULL,
            status VARCHAR(50) NULL DEFAULT 'submitted',
            reviewed_by BIGINT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS fix_requests (
            id BIGSERIAL PRIMARY KEY,
            application_id BIGINT NOT NULL REFERENCES applications(id) ON DELETE CASCADE,
            requested_by BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            request_type VARCHAR(50) NOT NULL DEFAULT 'data_correction',
            description TEXT NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'pending',
            resolved_by BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
            resolved_at TIMESTAMP NULL,
            resolution_notes TEXT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS payment_submissions (
            id BIGSERIAL PRIMARY KEY,
            application_id BIGINT NOT NULL REFERENCES applications(id) ON DELETE CASCADE,
            payment_stage VARCHAR(50) NOT NULL,
            method VARCHAR(50) NOT NULL,
            reference VARCHAR(255) NULL,
            amount DECIMAL(10,2) NULL,
            currency VARCHAR(3) NOT NULL DEFAULT 'USD',
            status VARCHAR(20) NOT NULL DEFAULT 'submitted',
            submitted_at TIMESTAMP NULL,
            verified_at TIMESTAMP NULL,
            verified_by BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
            rejection_reason TEXT NULL,
            proof_path VARCHAR(255) NULL,
            proof_metadata JSONB NULL,
            waiver_path VARCHAR(255) NULL,
            waiver_metadata JSONB NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS official_letters (
            id BIGSERIAL PRIMARY KEY,
            application_id BIGINT NOT NULL REFERENCES applications(id) ON DELETE CASCADE,
            uploaded_by BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            file_path VARCHAR(255) NOT NULL,
            file_name VARCHAR(255) NOT NULL,
            file_size BIGINT NOT NULL,
            file_hash VARCHAR(64) NOT NULL,
            uploaded_at TIMESTAMP NOT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS renewal_applications (
            id BIGSERIAL PRIMARY KEY,
            applicant_user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            renewal_type VARCHAR(255) NOT NULL,
            original_application_id BIGINT NULL REFERENCES applications(id) ON DELETE SET NULL,
            original_number VARCHAR(255) NULL,
            lookup_status VARCHAR(20) NOT NULL DEFAULT 'pending',
            looked_up_at TIMESTAMP NULL,
            has_changes BOOLEAN NOT NULL DEFAULT false,
            change_requests JSONB NULL,
            confirmation_type VARCHAR(20) NULL,
            confirmed_at TIMESTAMP NULL,
            payment_method VARCHAR(50) NULL,
            payment_reference VARCHAR(255) NULL,
            payment_amount DECIMAL(10,2) NULL,
            payment_date DATE NULL,
            payment_proof_path VARCHAR(500) NULL,
            payment_metadata JSONB NULL,
            payment_submitted_at TIMESTAMP NULL,
            payment_verified_at TIMESTAMP NULL,
            payment_verified_by BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
            payment_rejection_reason TEXT NULL,
            status VARCHAR(50) NOT NULL,
            current_stage VARCHAR(50) NULL,
            last_action_at TIMESTAMP NULL,
            last_action_by BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
            produced_at TIMESTAMP NULL,
            produced_by BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
            print_count INTEGER NOT NULL DEFAULT 0,
            collection_location VARCHAR(255) NULL,
            collected_at TIMESTAMP NULL,
            request_type VARCHAR(50) NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS renewal_change_requests (
            id BIGSERIAL PRIMARY KEY,
            renewal_application_id BIGINT NOT NULL REFERENCES renewal_applications(id) ON DELETE CASCADE,
            field_name VARCHAR(100) NOT NULL,
            old_value TEXT NULL,
            new_value TEXT NULL,
            reason TEXT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'pending',
            reviewed_by BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
            reviewed_at TIMESTAMP NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS portal_requirements (
            id BIGSERIAL PRIMARY KEY,
            portal_type VARCHAR(50) NOT NULL,
            category VARCHAR(100) NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT NULL,
            sort_order INTEGER NOT NULL DEFAULT 0,
            is_active BOOLEAN NOT NULL DEFAULT true,
            created_by BIGINT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS portal_requirement_items (
            id BIGSERIAL PRIMARY KEY,
            requirement_id BIGINT NOT NULL REFERENCES portal_requirements(id) ON DELETE CASCADE,
            label VARCHAR(255) NOT NULL,
            description TEXT NULL,
            is_mandatory BOOLEAN NOT NULL DEFAULT true,
            sort_order INTEGER NOT NULL DEFAULT 0,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS application_categories (
            id BIGSERIAL PRIMARY KEY,
            portal_type VARCHAR(50) NOT NULL,
            code VARCHAR(10) NOT NULL,
            name VARCHAR(255) NOT NULL,
            description TEXT NULL,
            fee_local DECIMAL(10,2) NULL,
            fee_foreign DECIMAL(10,2) NULL,
            fee_renewal_local DECIMAL(10,2) NULL,
            fee_renewal_foreign DECIMAL(10,2) NULL,
            is_active BOOLEAN NOT NULL DEFAULT true,
            sort_order INTEGER NOT NULL DEFAULT 0,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS portal_requirements_audit (
            id BIGSERIAL PRIMARY KEY,
            requirement_id BIGINT NULL,
            action VARCHAR(50) NOT NULL,
            performed_by BIGINT NULL,
            old_values JSONB NULL,
            new_values JSONB NULL,
            created_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS login_activities (
            id BIGSERIAL PRIMARY KEY,
            user_id BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
            email VARCHAR(255) NOT NULL,
            ip_address VARCHAR(45) NULL,
            user_agent TEXT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'success',
            failure_reason TEXT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS cash_payments (
            id BIGSERIAL PRIMARY KEY,
            application_id BIGINT NOT NULL REFERENCES applications(id) ON DELETE CASCADE,
            receipt_number VARCHAR(100) NOT NULL,
            amount DECIMAL(12,2) NOT NULL,
            currency VARCHAR(10) NOT NULL DEFAULT 'USD',
            payment_date DATE NOT NULL,
            payer_name VARCHAR(255) NULL,
            recorded_by BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            notes TEXT NULL,
            is_voided BOOLEAN NOT NULL DEFAULT false,
            void_reason TEXT NULL,
            voided_by BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
            voided_at TIMESTAMP NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS physical_intakes (
            id BIGSERIAL PRIMARY KEY,
            application_id BIGINT NOT NULL REFERENCES applications(id) ON DELETE CASCADE,
            officer_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            receipt_number VARCHAR(100) NOT NULL,
            intake_type VARCHAR(50) NOT NULL DEFAULT 'walk_in',
            applicant_name VARCHAR(255) NULL,
            applicant_id_number VARCHAR(50) NULL,
            notes TEXT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS design_templates (
            id BIGSERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            type VARCHAR(50) NOT NULL DEFAULT 'card',
            year INTEGER NULL,
            background_path VARCHAR(500) NULL,
            layout_config JSONB NULL,
            is_active BOOLEAN NOT NULL DEFAULT false,
            created_by BIGINT NULL,
            notes TEXT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");

        DB::statement("CREATE TABLE IF NOT EXISTS system_logs (
            id BIGSERIAL PRIMARY KEY,
            level VARCHAR(20) NOT NULL DEFAULT 'info',
            channel VARCHAR(100) NULL,
            message TEXT NOT NULL,
            context JSONB NULL,
            created_at TIMESTAMP NULL
        )");

        $this->addColumnSafe('users', 'account_type', "VARCHAR(50) NULL DEFAULT 'public'");
        $this->addColumnSafe('users', 'account_status', "VARCHAR(50) NULL DEFAULT 'active'");
        $this->addColumnSafe('users', 'designation', 'VARCHAR(255) NULL');
        $this->addColumnSafe('users', 'phone', 'VARCHAR(50) NULL');
        $this->addColumnSafe('users', 'region', 'VARCHAR(100) NULL');
        $this->addColumnSafe('users', 'approved_at', 'TIMESTAMP NULL');
        $this->addColumnSafe('users', 'approved_by', 'BIGINT NULL');
        $this->addColumnSafe('users', 'rejection_reason', 'TEXT NULL');
        $this->addColumnSafe('users', 'last_login_at', 'TIMESTAMP NULL');
        $this->addColumnSafe('users', 'theme_preference', "VARCHAR(20) NULL DEFAULT 'light'");

        $this->addColumnSafe('payments', 'payment_type', 'VARCHAR(50) NULL');
        $this->addColumnSafe('payments', 'receipt_number', 'VARCHAR(100) NULL');
        $this->addColumnSafe('payments', 'recorded_by', 'BIGINT NULL');
        $this->addColumnSafe('payments', 'void_reason', 'TEXT NULL');
        $this->addColumnSafe('payments', 'voided_by', 'BIGINT NULL');
        $this->addColumnSafe('payments', 'voided_at', 'TIMESTAMP NULL');

        $this->addColumnSafe('notices', 'image_path', 'VARCHAR(500) NULL');
        $this->addColumnSafe('events', 'image_path', 'VARCHAR(500) NULL');

        $this->addColumnSafe('users', 'national_id_number', 'VARCHAR(50) NULL');
        $this->addColumnSafe('users', 'passport_number', 'VARCHAR(50) NULL');
        $this->addColumnSafe('users', 'phone_number', 'VARCHAR(20) NULL');
        $this->addColumnSafe('users', 'phone_number_2', 'VARCHAR(20) NULL');

        $this->addColumnSafe('applications', 'payment_submission_method', 'VARCHAR(50) NULL');
        $this->addColumnSafe('applications', 'receipt_number', 'VARCHAR(100) NULL');
        $this->addColumnSafe('applications', 'forward_no_approval_reason', 'TEXT NULL');
        $this->addColumnSafe('applications', 'official_letter_id', 'BIGINT NULL');
        $this->addColumnSafe('applications', 'verification_notes', 'TEXT NULL');
        $this->addColumnSafe('applications', 'verified_by', 'BIGINT NULL');
        $this->addColumnSafe('applications', 'verified_at', 'TIMESTAMP NULL');

        $this->addColumnSafe('payments', 'cash_receipt_number', 'VARCHAR(100) NULL');
        $this->addColumnSafe('payments', 'cash_amount', 'DECIMAL(12,2) NULL');
        $this->addColumnSafe('payments', 'cash_date', 'DATE NULL');
        $this->addColumnSafe('payments', 'cash_recorded_by', 'BIGINT NULL');
        $this->addColumnSafe('payments', 'cash_notes', 'TEXT NULL');

        $this->fixConstraints();

        $this->info('  - All tables and columns ensured');
    }

    private function fixConstraints(): void
    {
        $this->info('  - Fixing check constraints...');

        $this->replaceCheckConstraint('users', 'users_account_type_check',
            "account_type IN ('staff','public','journalist','mediahouse')");

        $this->replaceCheckConstraint('applications', 'applications_application_type_check',
            "application_type IN ('accreditation','registration','media_house')");

        $this->replaceCheckConstraint('applications', 'applications_collection_region_check',
            "collection_region IS NULL OR collection_region IN ('harare','bulawayo','mutare','masvingo','gweru','chinhoyi','bindura','marondera','hwange','kadoma','Harare','Bulawayo','Mutare','Masvingo','Gweru','Chinhoyi','Bindura','Marondera','Hwange','Kadoma')");

        $this->replaceCheckConstraint('applications', 'applications_payment_status_check',
            "payment_status IN ('none','requested','uploaded_waiver','paid','rejected','pending','verified')");

        DB::statement("ALTER TABLE applications DROP CONSTRAINT IF EXISTS applications_status_check");
    }

    private function replaceCheckConstraint(string $table, string $constraintName, string $check): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        try {
            DB::statement("ALTER TABLE {$table} DROP CONSTRAINT IF EXISTS {$constraintName}");
            DB::statement("ALTER TABLE {$table} ADD CONSTRAINT {$constraintName} CHECK ({$check})");
        } catch (\Throwable $e) {
            $this->warn("    Constraint {$constraintName}: " . $e->getMessage());
        }
    }

    private function addColumnSafe(string $table, string $column, string $type): void
    {
        if (Schema::hasColumn($table, $column)) {
            return;
        }

        DB::statement("ALTER TABLE {$table} ADD COLUMN {$column} {$type}");
        $this->info("    + Added {$table}.{$column}");
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SafeMigrate extends Command
{
    protected $signature = 'db:safe-migrate';
    protected $description = 'Run migrations safely, marking existing tables as already migrated';

    private array $tableMap = [
        'create_users_table' => ['users', 'password_reset_tokens', 'sessions'],
        'create_cache_table' => ['cache', 'cache_locks'],
        'create_jobs_table' => ['jobs', 'job_batches', 'failed_jobs'],
        'create_permission_tables' => ['permissions', 'roles', 'model_has_permissions', 'model_has_roles', 'role_has_permissions'],
        'create_applications_table' => ['applications'],
        'create_application_documents_table' => ['application_documents'],
        'create_application_messages_table' => ['application_messages'],
        'create_officer_regions_table' => ['officer_regions'],
        'create_activity_logs_table' => ['activity_logs'],
        'create_payments_table' => ['payments'],
        'create_audit_trails_table' => ['audit_trails'],
        'create_audit_logs_table' => ['audit_logs'],
        'create_notices_table' => ['notices'],
        'create_events_table' => ['events'],
        'create_notifications_table' => ['notifications'],
        'create_news_table' => ['news'],
        'create_complaints_table' => ['complaints'],
        'create_accreditation_records_table' => ['accreditation_records'],
        'create_system_configs_table' => ['system_configs'],
        'create_registration_records_table' => ['registration_records'],
        'create_officer_followups_table' => ['officer_followups'],
        'create_compliance_tables' => ['compliance_cases', 'compliance_violations', 'compliance_evidence_files'],
        'create_audit_flags_table' => ['audit_flags'],
        'create_media_house_staff_table' => ['media_house_staff'],
        'create_regions_table' => ['regions'],
        'create_vacancies_table' => ['vacancies'],
        'create_tenders_table' => ['tenders'],
        'create_registrar_oversight_tables' => ['document_versions', 'print_logs', 'payment_audit_logs', 'receipt_sequences'],
    ];

    public function handle(): int
    {
        $this->info('Running safe migration...');

        try {
            if (!Schema::hasTable('migrations')) {
                $this->info('  - No migrations table, running fresh migrate...');
                $this->call('migrate', ['--force' => true]);
                return 0;
            }

            $migrationCount = DB::table('migrations')->count();

            if ($migrationCount > 0) {
                $this->info("  - Migrations table has {$migrationCount} records, running pending migrations...");
                $this->call('migrate', ['--force' => true]);
                return 0;
            }

            $existingTables = [];
            if (DB::getDriverName() === 'sqlite') {
                $existingTables = collect(DB::select("SELECT name FROM sqlite_master WHERE type='table'"))
                    ->pluck('name')
                    ->toArray();
            } else {
                $existingTables = collect(DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'"))
                    ->pluck('table_name')
                    ->toArray();
            }

            if (count($existingTables) <= 1) {
                $this->info('  - Empty database, running fresh migrate...');
                $this->call('migrate', ['--force' => true]);
                return 0;
            }

            $this->info('  - Database has ' . count($existingTables) . ' tables but empty migrations tracker.');
            $this->info('  - Checking each migration against existing schema...');

            $migrationFiles = collect(scandir(database_path('migrations')))
                ->filter(fn($f) => str_ends_with($f, '.php'))
                ->map(fn($f) => str_replace('.php', '', $f))
                ->sort()
                ->values();

            $marked = 0;
            $skipped = 0;

            foreach ($migrationFiles as $migration) {
                $shouldMark = $this->shouldMarkAsRun($migration, $existingTables);

                if ($shouldMark) {
                    DB::table('migrations')->insert([
                        'migration' => $migration,
                        'batch' => 1,
                    ]);
                    $marked++;
                } else {
                    $skipped++;
                    $this->warn("  - Leaving pending: {$migration}");
                }
            }

            $this->info("  - Marked {$marked} migrations as already run, {$skipped} left pending");

            $this->call('migrate', ['--force' => true]);

            $this->info('Safe migration complete!');
            return 0;

        } catch (\Throwable $e) {
            $this->error('Migration failed: ' . $e->getMessage());
            return 1;
        }
    }

    private function shouldMarkAsRun(string $migration, array $existingTables): bool
    {
        foreach ($this->tableMap as $pattern => $tables) {
            if (str_contains($migration, $pattern)) {
                foreach ($tables as $table) {
                    if (in_array($table, $existingTables)) {
                        return true;
                    }
                }
                return false;
            }
        }

        if (str_contains($migration, 'create_') && str_contains($migration, '_table')) {
            preg_match('/create_(.+?)_table/', $migration, $matches);
            if (!empty($matches[1]) && in_array($matches[1], $existingTables)) {
                return true;
            }
        }

        if (str_contains($migration, 'add_') || str_contains($migration, 'backfill_') ||
            str_contains($migration, 'migrate_') || str_contains($migration, 'fix_') ||
            str_contains($migration, 'enhance_')) {
            preg_match('/(?:add|backfill|migrate|fix|enhance)_.+?_to_(.+?)_table/', $migration, $matches);
            if (!empty($matches[1]) && in_array($matches[1], $existingTables)) {
                return true;
            }

            preg_match('/(?:add|enhance)_.+?_(.+?)$/', $migration, $matches);
            if (!empty($matches[1])) {
                $tableName = $matches[1];
                if (in_array($tableName, $existingTables)) {
                    return true;
                }
            }

            return true;
        }

        if (str_contains($migration, 'add_director_dashboard_indexes')) {
            return in_array('applications', $existingTables);
        }

        return true;
    }
}

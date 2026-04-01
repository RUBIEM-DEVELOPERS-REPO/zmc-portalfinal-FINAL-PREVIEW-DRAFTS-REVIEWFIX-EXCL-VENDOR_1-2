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
            // Add indexes if they don't exist (Laravel will skip if they exist)
            try {
                $table->index(['applicant_id', 'created_at']);
            } catch (\Exception $e) {
                // Index already exists, skip
            }
            
            if (Schema::hasColumn('applications', 'request_type')) {
                try {
                    $table->index('request_type');
                } catch (\Exception $e) {
                    // Index already exists, skip
                }
            }
        });

        if (Schema::hasTable('payment_submissions')) {
            Schema::table('payment_submissions', function (Blueprint $table) {
                try {
                    $table->index(['application_id', 'created_at']);
                } catch (\Exception $e) {
                    // Index already exists, skip
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex(['applicant_id', 'created_at']);
            if (Schema::hasColumn('applications', 'request_type')) {
                $table->dropIndex(['request_type']);
            }
        });

        if (Schema::hasTable('payment_submissions')) {
            Schema::table('payment_submissions', function (Blueprint $table) {
                $table->dropIndex(['application_id', 'created_at']);
            });
        }
    }
};

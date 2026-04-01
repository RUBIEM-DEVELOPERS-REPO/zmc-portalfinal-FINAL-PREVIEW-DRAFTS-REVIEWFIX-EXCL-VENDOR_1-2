<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Forward without approval reason
            if (!Schema::hasColumn('applications', 'forward_no_approval_reason')) {
                $table->text('forward_no_approval_reason')->nullable()->after('decision_notes');
            }
            
            // Official letter reference (for quick access)
            if (!Schema::hasColumn('applications', 'official_letter_id')) {
                $table->foreignId('official_letter_id')->nullable()
                    ->after('forward_no_approval_reason')
                    ->constrained('official_letters')
                    ->nullOnDelete();
            }
            
            // Payment stage tracking
            if (!Schema::hasColumn('applications', 'current_payment_stage')) {
                $table->enum('current_payment_stage', ['none', 'application_fee', 'registration_fee'])
                    ->default('none')
                    ->after('official_letter_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropForeign(['official_letter_id']);
            $table->dropColumn(['forward_no_approval_reason', 'official_letter_id', 'current_payment_stage']);
        });
    }
};

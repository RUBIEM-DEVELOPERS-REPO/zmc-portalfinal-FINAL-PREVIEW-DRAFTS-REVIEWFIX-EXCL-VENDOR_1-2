<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite, we need to recreate the table to change column types
        // This is a safe operation since we're in development
        
        // Get existing data
        $existingData = DB::table('renewal_applications')->get();
        
        // Drop and recreate table
        Schema::dropIfExists('renewal_applications');
        
        Schema::create('renewal_applications', function (Blueprint $table) {
            $table->id();
            
            // Applicant
            $table->foreignId('applicant_user_id')->constrained('users')->cascadeOnDelete();
            
            // Renewal Type - Changed to string to accept any value
            $table->string('renewal_type');
            
            // Original Record Reference
            $table->foreignId('original_application_id')->nullable()->constrained('applications')->nullOnDelete();
            $table->string('original_number')->nullable()->index(); // Made nullable
            
            // Lookup
            $table->enum('lookup_status', ['pending', 'found', 'not_found'])->default('pending');
            $table->timestamp('looked_up_at')->nullable();
            
            // Changes
            $table->boolean('has_changes')->default(false);
            $table->json('change_requests')->nullable();
            $table->enum('confirmation_type', ['no_changes', 'with_changes'])->nullable();
            $table->timestamp('confirmed_at')->nullable();
            
            // Payment
            $table->enum('payment_method', ['PAYNOW', 'PROOF_UPLOAD', 'WAIVER'])->nullable();
            $table->string('payment_reference')->nullable();
            $table->decimal('payment_amount', 10, 2)->nullable();
            $table->date('payment_date')->nullable();
            $table->string('payment_proof_path')->nullable();
            $table->json('payment_metadata')->nullable();
            $table->timestamp('payment_submitted_at')->nullable();
            $table->timestamp('payment_verified_at')->nullable();
            $table->foreignId('payment_verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('payment_rejection_reason')->nullable();
            
            // Status & Workflow
            $table->string('status')->index();
            $table->string('current_stage')->nullable();
            $table->timestamp('last_action_at')->nullable();
            $table->foreignId('last_action_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Production
            $table->timestamp('produced_at')->nullable();
            $table->foreignId('produced_by')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('print_count')->default(0);
            $table->string('collection_location')->nullable();
            $table->timestamp('collected_at')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index(['applicant_user_id', 'status']);
            $table->index(['status', 'current_stage']);
            $table->index('payment_submitted_at');
        });
        
        // Restore existing data if any
        foreach ($existingData as $row) {
            DB::table('renewal_applications')->insert((array) $row);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get existing data
        $existingData = DB::table('renewal_applications')->get();
        
        // Drop and recreate with original schema
        Schema::dropIfExists('renewal_applications');
        
        Schema::create('renewal_applications', function (Blueprint $table) {
            $table->id();
            
            // Applicant
            $table->foreignId('applicant_user_id')->constrained('users')->cascadeOnDelete();
            
            // Renewal Type
            $table->enum('renewal_type', ['accreditation', 'registration', 'permission']);
            
            // Original Record Reference
            $table->foreignId('original_application_id')->nullable()->constrained('applications')->nullOnDelete();
            $table->string('original_number')->index(); // NOT NULL
            
            // Lookup
            $table->enum('lookup_status', ['pending', 'found', 'not_found'])->default('pending');
            $table->timestamp('looked_up_at')->nullable();
            
            // Changes
            $table->boolean('has_changes')->default(false);
            $table->json('change_requests')->nullable();
            $table->enum('confirmation_type', ['no_changes', 'with_changes'])->nullable();
            $table->timestamp('confirmed_at')->nullable();
            
            // Payment
            $table->enum('payment_method', ['PAYNOW', 'PROOF_UPLOAD', 'WAIVER'])->nullable();
            $table->string('payment_reference')->nullable();
            $table->decimal('payment_amount', 10, 2)->nullable();
            $table->date('payment_date')->nullable();
            $table->string('payment_proof_path')->nullable();
            $table->json('payment_metadata')->nullable();
            $table->timestamp('payment_submitted_at')->nullable();
            $table->timestamp('payment_verified_at')->nullable();
            $table->foreignId('payment_verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('payment_rejection_reason')->nullable();
            
            // Status & Workflow
            $table->string('status')->index();
            $table->string('current_stage')->nullable();
            $table->timestamp('last_action_at')->nullable();
            $table->foreignId('last_action_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Production
            $table->timestamp('produced_at')->nullable();
            $table->foreignId('produced_by')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('print_count')->default(0);
            $table->string('collection_location')->nullable();
            $table->timestamp('collected_at')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index(['applicant_user_id', 'status']);
            $table->index(['status', 'current_stage']);
            $table->index('payment_submitted_at');
        });
        
        // Restore existing data if any
        foreach ($existingData as $row) {
            DB::table('renewal_applications')->insert((array) $row);
        }
    }
};

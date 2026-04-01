<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            
            // Payment stage: 'application_fee' | 'registration_fee'
            $table->enum('payment_stage', ['application_fee', 'registration_fee']);
            
            // Method: 'PAYNOW' | 'PROOF_UPLOAD' | 'WAIVER'
            $table->enum('method', ['PAYNOW', 'PROOF_UPLOAD', 'WAIVER']);
            
            // Reference/tracking
            $table->string('reference')->nullable(); // PayNow reference or receipt number
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            
            // Status: 'submitted' | 'verified' | 'rejected'
            $table->enum('status', ['submitted', 'verified', 'rejected'])->default('submitted');
            
            // Timestamps
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Rejection
            $table->text('rejection_reason')->nullable();
            
            // File paths
            $table->string('proof_path')->nullable(); // For proof uploads
            $table->json('proof_metadata')->nullable(); // payer_name, date_paid, etc.
            $table->string('waiver_path')->nullable(); // For waivers
            $table->json('waiver_metadata')->nullable(); // beneficiary, offered_by, etc.
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['application_id', 'payment_stage']);
            $table->index(['status', 'payment_stage']);
            $table->index('submitted_at');
            $table->index('verified_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_submissions');
    }
};

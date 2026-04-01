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
        Schema::create('renewal_change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('renewal_application_id')->constrained()->cascadeOnDelete();
            
            // Change Details
            $table->string('field_name');
            $table->text('old_value')->nullable();
            $table->text('new_value');
            $table->string('supporting_document_path')->nullable();
            
            // Review
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['renewal_application_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('renewal_change_requests');
    }
};

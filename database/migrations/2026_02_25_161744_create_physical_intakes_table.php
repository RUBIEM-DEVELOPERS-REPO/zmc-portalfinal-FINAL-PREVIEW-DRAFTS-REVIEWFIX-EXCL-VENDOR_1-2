<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('physical_intakes', function (Blueprint $table) {
            $table->id();
            $table->string('accreditation_number', 50)->nullable();
            $table->string('registration_number', 50)->nullable();
            $table->enum('intake_type', ['accreditation', 'registration']);
            $table->string('applicant_name');
            $table->string('receipt_number', 100);
            $table->foreignId('processed_by')->constrained('users');
            $table->timestamp('confirmed_at');
            $table->foreignId('application_id')->nullable()->constrained('applications');
            $table->unsignedBigInteger('production_record_id')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'in_production', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['accreditation_number', 'registration_number']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('physical_intakes');
    }
};

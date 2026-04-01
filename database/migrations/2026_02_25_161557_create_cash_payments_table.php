<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications');
            $table->string('receipt_number', 100)->unique();
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->foreignId('recorded_by')->constrained('users');
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            $table->enum('status', ['pending', 'verified', 'voided'])->default('pending');
            $table->text('void_reason')->nullable();
            $table->foreignId('voided_by')->nullable()->constrained('users');
            $table->timestamp('voided_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('application_id');
            $table->index('receipt_number');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_payments');
    }
};

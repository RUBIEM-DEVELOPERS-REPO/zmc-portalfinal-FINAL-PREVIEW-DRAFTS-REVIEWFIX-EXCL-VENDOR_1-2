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
        Schema::create('portal_requirements_audit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requirement_id')->nullable()->constrained('portal_requirements')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('action', ['created', 'updated', 'deleted'])->comment('Action performed');
            $table->json('old_value')->nullable()->comment('Previous value');
            $table->json('new_value')->nullable()->comment('New value');
            $table->string('ip_address', 45)->nullable()->comment('User IP address');
            $table->text('user_agent')->nullable()->comment('User agent string');
            $table->timestamp('created_at')->useCurrent();
            
            // Indexes
            $table->index('requirement_id');
            $table->index('user_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portal_requirements_audit');
    }
};

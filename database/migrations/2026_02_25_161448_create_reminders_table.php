<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users');
            $table->enum('target_type', ['media_practitioner', 'media_house', 'bulk']);
            $table->unsignedBigInteger('target_id')->nullable();
            $table->json('bulk_criteria')->nullable();
            $table->enum('priority', ['high', 'normal'])->default('normal');
            $table->string('reminder_type', 50);
            $table->string('title');
            $table->text('message');
            $table->foreignId('related_application_id')->nullable()->constrained('applications');
            $table->string('link_url', 500)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['target_type', 'target_id']);
            $table->index('expires_at');
            $table->index('priority');
        });

        Schema::create('reminder_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reminder_id')->constrained('reminders')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamp('read_at')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();
            
            $table->unique(['reminder_id', 'user_id']);
            $table->index(['user_id', 'read_at']);
            $table->index('acknowledged_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminder_reads');
        Schema::dropIfExists('reminders');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('reminder_reads')) {
            Schema::create('reminder_reads', function (Blueprint $table) {
                $table->id();
                $table->foreignId('reminder_id')->constrained('reminders')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->timestamp('read_at')->nullable();
                $table->timestamp('acknowledged_at')->nullable();
                $table->timestamps();
                $table->unique(['reminder_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reminder_reads');
    }
};

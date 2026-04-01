<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('account_name');
            $table->string('ip_address', 45);
            $table->text('user_agent');
            $table->string('device_identifier')->nullable();
            $table->string('operating_system', 100)->nullable();
            $table->string('browser_name', 100)->nullable();
            $table->string('browser_version', 50)->nullable();
            $table->timestamp('login_at');
            $table->timestamp('logout_at')->nullable();
            $table->integer('session_duration')->nullable();
            $table->boolean('login_successful')->default(true);
            $table->string('failure_reason')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'login_at']);
            $table->index('ip_address');
            $table->index('login_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_activities');
    }
};

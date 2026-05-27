<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('temp_password')->nullable()->after('password');
            $table->timestamp('temp_password_expires_at')->nullable()->after('temp_password');
            $table->boolean('password_change_required')->default(false)->after('temp_password_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['temp_password', 'temp_password_expires_at', 'password_change_required']);
        });
    }
};

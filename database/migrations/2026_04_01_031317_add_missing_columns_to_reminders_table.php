<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            if (!Schema::hasColumn('reminders', 'title')) {
                $table->string('title', 255)->nullable()->after('reminder_type');
            }
            if (!Schema::hasColumn('reminders', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('message');
            }
            if (!Schema::hasColumn('reminders', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->dropColumn(['title', 'expires_at']);
            $table->dropSoftDeletes();
        });
    }
};

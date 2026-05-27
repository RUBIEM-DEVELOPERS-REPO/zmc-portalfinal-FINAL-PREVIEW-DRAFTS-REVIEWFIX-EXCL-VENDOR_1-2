<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'national_id_number')) {
                $table->string('national_id_number', 50)->nullable();
            }
            if (!Schema::hasColumn('users', 'passport_number')) {
                $table->string('passport_number', 50)->nullable();
            }
            if (!Schema::hasColumn('users', 'phone_number_2')) {
                $table->string('phone_number_2', 20)->nullable();
            }
            if (!Schema::hasColumn('users', 'theme_preference')) {
                $table->string('theme_preference', 10)->default('light');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['national_id_number', 'passport_number', 'phone_number_2', 'theme_preference']);
        });
    }
};

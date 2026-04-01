<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('national_id_number', 50)->nullable()->after('email');
            $table->string('passport_number', 50)->nullable()->after('national_id_number');
            $table->string('phone_number_2', 20)->nullable()->after('phone_number');
            $table->enum('theme_preference', ['light', 'dark'])->default('light')->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['national_id_number', 'passport_number', 'phone_number_2', 'theme_preference']);
        });
    }
};

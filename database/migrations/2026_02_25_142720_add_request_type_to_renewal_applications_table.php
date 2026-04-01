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
        Schema::table('renewal_applications', function (Blueprint $table) {
            $table->string('request_type')->nullable()->after('renewal_type')->comment('renewal or replacement');
            $table->string('registration_number')->nullable()->after('original_number')->comment('For media house renewals');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('renewal_applications', function (Blueprint $table) {
            $table->dropColumn(['request_type', 'registration_number']);
        });
    }
};

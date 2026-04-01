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
        Schema::table('applications', function (Blueprint $table) {
            $table->timestamp('registrar_reviewed_at')->after('registrar_approved_at')->nullable();
            $table->unsignedBigInteger('registrar_reviewed_by')->after('registrar_reviewed_at')->nullable();
            $table->boolean('is_flagged')->default(false)->after('registrar_reviewed_by');
            $table->text('flag_notes')->after('is_flagged')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['registrar_reviewed_at', 'registrar_reviewed_by', 'is_flagged', 'flag_notes']);
        });
    }
};

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
        Schema::table('application_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('application_documents', 'owner_id')) {
                $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('application_documents', 'thumbnail_path')) {
                $table->string('thumbnail_path')->nullable();
            }
            if (!Schema::hasColumn('application_documents', 'mime')) {
                $table->string('mime')->nullable();
            }
            if (!Schema::hasColumn('application_documents', 'size')) {
                $table->unsignedBigInteger('size')->nullable();
            }
            if (!Schema::hasColumn('application_documents', 'sha256')) {
                $table->string('sha256')->nullable();
            }
            if (!Schema::hasColumn('application_documents', 'file_data')) {
                $table->longText('file_data')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->dropColumn(['owner_id', 'thumbnail_path', 'mime', 'size', 'sha256', 'file_data']);
        });
    }
};

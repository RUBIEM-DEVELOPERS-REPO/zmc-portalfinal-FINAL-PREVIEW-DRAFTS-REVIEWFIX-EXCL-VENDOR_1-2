<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('notices')) {
            Schema::table('notices', function (Blueprint $table) {
                if (!Schema::hasColumn('notices', 'image_path')) {
                    $table->string('image_path', 500)->nullable();
                }
                if (!Schema::hasColumn('notices', 'thumbnail_path')) {
                    $table->string('thumbnail_path', 500)->nullable();
                }
                if (!Schema::hasColumn('notices', 'expires_at')) {
                    $table->timestamp('expires_at')->nullable();
                }
            });
        }

        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                if (!Schema::hasColumn('events', 'image_path')) {
                    $table->string('image_path', 500)->nullable();
                }
                if (!Schema::hasColumn('events', 'thumbnail_path')) {
                    $table->string('thumbnail_path', 500)->nullable();
                }
                if (!Schema::hasColumn('events', 'expires_at')) {
                    $table->timestamp('expires_at')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('notices')) {
            Schema::table('notices', function (Blueprint $table) {
                $table->dropColumn(['image_path', 'thumbnail_path', 'expires_at']);
            });
        }

        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn(['image_path', 'thumbnail_path', 'expires_at']);
            });
        }
    }
};

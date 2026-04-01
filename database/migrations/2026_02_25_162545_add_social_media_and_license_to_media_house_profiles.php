<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if table exists, if not create it
        if (!Schema::hasTable('media_house_profiles')) {
            Schema::create('media_house_profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('entity_name')->nullable();
                $table->string('registration_number')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('media_house_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('media_house_profiles', 'facebook_url')) {
                $table->string('facebook_url', 500)->nullable();
            }
            if (!Schema::hasColumn('media_house_profiles', 'twitter_url')) {
                $table->string('twitter_url', 500)->nullable();
            }
            if (!Schema::hasColumn('media_house_profiles', 'instagram_url')) {
                $table->string('instagram_url', 500)->nullable();
            }
            if (!Schema::hasColumn('media_house_profiles', 'youtube_url')) {
                $table->string('youtube_url', 500)->nullable();
            }
            if (!Schema::hasColumn('media_house_profiles', 'tiktok_url')) {
                $table->string('tiktok_url', 500)->nullable();
            }
            if (!Schema::hasColumn('media_house_profiles', 'website_url')) {
                $table->string('website_url', 500)->nullable();
            }
            if (!Schema::hasColumn('media_house_profiles', 'license_status')) {
                $table->enum('license_status', ['active', 'expired', 'suspended'])->default('active');
            }
            if (!Schema::hasColumn('media_house_profiles', 'license_expires_at')) {
                $table->date('license_expires_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('media_house_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'facebook_url',
                'twitter_url',
                'instagram_url',
                'youtube_url',
                'tiktok_url',
                'website_url',
                'license_status',
                'license_expires_at'
            ]);
        });
    }
};

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
        Schema::create('portal_requirements', function (Blueprint $table) {
            $table->id();
            $table->enum('portal_type', ['accreditation', 'registration'])->comment('Portal this requirement belongs to');
            $table->string('section_key', 100)->comment('Unique key for this section');
            $table->string('section_title')->comment('Display title for this section');
            $table->integer('section_order')->default(0)->comment('Display order');
            $table->json('content')->comment('Section content as JSON');
            $table->boolean('is_active')->default(true)->comment('Whether this section is active');
            $table->timestamps();
            
            // Indexes
            $table->index('portal_type');
            $table->index('section_key');
            $table->index('is_active');
            $table->unique(['portal_type', 'section_key'], 'unique_portal_section');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portal_requirements');
    }
};

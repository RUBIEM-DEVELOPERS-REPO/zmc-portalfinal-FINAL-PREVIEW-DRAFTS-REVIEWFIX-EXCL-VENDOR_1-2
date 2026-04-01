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
        Schema::create('application_categories', function (Blueprint $table) {
            $table->id();
            $table->enum('portal_type', ['accreditation', 'registration'])->comment('Portal this category belongs to');
            $table->string('code', 50)->comment('Category code (e.g., J01, M01)');
            $table->string('name')->comment('Category name');
            $table->text('description')->nullable()->comment('Category description');
            $table->text('requirements')->nullable()->comment('Specific requirements for this category');
            $table->boolean('is_active')->default(true)->comment('Whether this category is active');
            $table->integer('category_order')->default(0)->comment('Display order');
            $table->timestamps();
            
            // Indexes
            $table->index('portal_type');
            $table->index('code');
            $table->index('is_active');
            $table->unique(['portal_type', 'code'], 'unique_portal_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_categories');
    }
};

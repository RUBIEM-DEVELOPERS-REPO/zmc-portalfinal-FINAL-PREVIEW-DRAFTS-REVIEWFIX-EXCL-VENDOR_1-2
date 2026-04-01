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
        Schema::create('portal_requirement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requirement_id')->constrained('portal_requirements')->onDelete('cascade');
            $table->enum('item_type', ['document', 'information', 'fee', 'step'])->comment('Type of requirement item');
            $table->string('title')->comment('Item title');
            $table->text('description')->nullable()->comment('Item description');
            $table->boolean('is_required')->default(true)->comment('Whether this item is mandatory');
            $table->json('file_types')->nullable()->comment('Allowed file types for documents');
            $table->integer('max_file_size')->nullable()->comment('Max file size in KB');
            $table->integer('item_order')->default(0)->comment('Display order');
            $table->json('metadata')->nullable()->comment('Additional metadata');
            $table->timestamps();
            
            // Indexes
            $table->index('requirement_id');
            $table->index('item_type');
            $table->index('is_required');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portal_requirement_items');
    }
};

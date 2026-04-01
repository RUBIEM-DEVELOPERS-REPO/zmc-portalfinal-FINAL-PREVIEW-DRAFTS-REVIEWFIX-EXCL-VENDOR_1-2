<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_templates', function (Blueprint $table) {
            $table->id();
            $table->enum('template_type', ['accreditation_card', 'registration_certificate']);
            $table->string('template_name');
            $table->string('version', 50);
            $table->integer('year');
            $table->string('background_image_path', 500)->nullable();
            $table->json('layout_config');
            $table->boolean('is_active')->default(false);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->index(['template_type', 'is_active']);
            $table->index('year');
        });

        // Only create print_logs if it doesn't exist
        if (!Schema::hasTable('print_logs')) {
            Schema::create('print_logs', function (Blueprint $table) {
                $table->id();
                $table->enum('record_type', ['accreditation', 'registration']);
                $table->unsignedBigInteger('record_id');
                $table->foreignId('template_id')->constrained('design_templates');
                $table->foreignId('printed_by')->constrained('users');
                $table->string('printer_name')->nullable();
                $table->integer('print_count')->default(1);
                $table->timestamp('printed_at');
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->index(['record_type', 'record_id']);
                $table->index('printed_by');
                $table->index('printed_at');
            });
        } else {
            // Add new columns to existing print_logs table
            Schema::table('print_logs', function (Blueprint $table) {
                if (!Schema::hasColumn('print_logs', 'record_type')) {
                    $table->enum('record_type', ['accreditation', 'registration'])->nullable();
                }
                if (!Schema::hasColumn('print_logs', 'record_id')) {
                    $table->unsignedBigInteger('record_id')->nullable();
                }
                if (!Schema::hasColumn('print_logs', 'template_id')) {
                    $table->foreignId('template_id')->nullable()->constrained('design_templates');
                }
                if (!Schema::hasColumn('print_logs', 'print_count')) {
                    $table->integer('print_count')->default(1);
                }
                if (!Schema::hasColumn('print_logs', 'notes')) {
                    $table->text('notes')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('print_logs');
        Schema::dropIfExists('design_templates');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('media_house_user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('status')->default('pending'); // pending, paid, rejected, reversed
            $table->string('payment_method')->nullable(); // paynow, proof, cash
            $table->string('proof_path')->nullable();
            $table->json('metadata')->nullable(); // For storing selected journalist IDs or notes
            $table->timestamps();
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->foreignId('batch_id')->nullable()->constrained('batches')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->dropColumn('batch_id');
        });
        Schema::dropIfExists('batches');
    }
};

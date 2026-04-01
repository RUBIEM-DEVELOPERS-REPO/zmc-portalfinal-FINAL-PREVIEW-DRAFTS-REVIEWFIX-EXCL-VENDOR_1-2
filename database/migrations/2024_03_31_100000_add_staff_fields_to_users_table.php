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
        Schema::table('users', function (Blueprint $table) {
            // Add missing fields for staff accounts
            $table->string('username')->unique()->after('id');
            $table->string('phone')->nullable()->after('email');
            $table->string('department')->nullable()->after('phone');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('department');
            $table->timestamp('last_login_at')->nullable()->after('updated_at');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
            
            // Add indexes
            $table->index('username');
            $table->index('status');
            $table->index('department');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['username']);
            $table->dropIndex(['status']);
            $table->dropIndex(['department']);
            
            $table->dropColumn([
                'username',
                'phone',
                'department',
                'status',
                'last_login_at',
                'last_login_ip'
            ]);
        });
    }
};

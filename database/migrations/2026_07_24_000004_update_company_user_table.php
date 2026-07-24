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
        Schema::table('company_user', function (Blueprint $table) {
            if (!Schema::hasColumn('company_user', 'role_id')) {
                $table->foreignId('role_id')->nullable()->after('user_id')->constrained('roles')->nullOnDelete();
            }
            if (!Schema::hasColumn('company_user', 'status')) {
                $table->string('status')->default('active')->after('role_id'); // active, pending, suspended, inactive
            }
            if (!Schema::hasColumn('company_user', 'invited_by')) {
                $table->foreignId('invited_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('company_user', 'joined_at')) {
                $table->timestamp('joined_at')->nullable()->after('invited_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_user', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['invited_by']);
            $table->dropColumn(['role_id', 'status', 'invited_by', 'joined_at']);
        });
    }
};
